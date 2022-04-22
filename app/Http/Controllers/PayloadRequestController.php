<?php

namespace App\Http\Controllers;

use App\constants\DataBaseConstants;
use App\Http\Requests\StorePayloadRequestRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\PayloadRequest;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\Config;
use App\Http\Requests\UpdatePayloadRequestRequest;
use App\Http\Resources\PayloadRequestResource;
use App\Models\PayloadRequestItem;
use App\Traits\Request as CustomRequest;
use Illuminate\Support\Facades\Auth;

class PayloadRequestController extends Controller
{
    use CustomRequest;

    public function __construct(Request $request)
    {
        $this->setConstruct($request, PayloadRequestResource::class);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payloadRequests = QueryBuilder::for(PayloadRequest::class)
                                       ->allowedIncludes(['payloadRequestItems', 'processType', 'payloadType', 'user'])
                                       ->allowedFilters(['id'])
                                       ->defaultSort('-id')
                                       ->paginate($this->perPage, ['*'], 'page', $this->page);

        return $this->collection($payloadRequests);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePayloadRequestRequest $request)
    {
        $payloadRequest = new PayloadRequest($request->all());
        $payloadRequest->date = Carbon::now();
        $payloadRequest->user_id = Auth::id();
        $payloadRequest->status = 0;
        $payloadRequest->way = DataBaseConstants::getWaysArr()['FORWARD'];
        $payloadRequest->save();

        foreach($request->items as $item) {
            PayloadRequestItem::create([
                'name' => $item['name'],
                'amount' => $item['amount'],
                'payload_request_id' => $payloadRequest->id
            ]);
        }

        $payloadRequest->createPath();

        return $this->resource($payloadRequest->load([
            'processType',
            'payloadType',
            'payloadRequestItems',
            'user'
        ]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $payloadRequest = PayloadRequest::find($id);

        if(!$payloadRequest)
            return $this->error(404, Config::get('constants.errors.not_found'));

        return $this->resource($payloadRequest->load([
            'processType',
            'payloadType',
            'payloadRequestItems',
            'user'
        ]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePayloadRequestRequest $request, $id)
    {
        $payloadRequest = PayloadRequest::find($id);

        if(!$payloadRequest)
            return $this->error(404, Config::get('constants.errors.not_found'));

        if($request->items) {
            $payloadRequest->payloadRequestItems()->delete();
            foreach($request->items as $item) {
                PayloadRequestItem::create([
                    'name' => $item['name'],
                    'amount' => $item['amount'],
                    'payload_request_id' => $payloadRequest->id
                ]);
            }
        }

        $payloadRequest->update($request->all());

        return $this->resource($payloadRequest->load([
            'processType',
            'payloadType',
            'payloadRequestItems',
            'user'
        ]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $payloadRequest = PayloadRequest::find($id);

        if(!$payloadRequest)
            return $this->error(404, Config::get('constants.errors.not_found'));

        $payloadRequest->payloadRequestItems()->delete();
        
        $payloadRequest->delete();
        
        return $this->success([], Config::get('constants.success.delete'));
    }
}
