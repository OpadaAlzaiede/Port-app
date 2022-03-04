<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePayloadTypeRequest;
use App\Http\Requests\UpdatePayloadTypeRequest;
use App\Http\Resources\PayloadTypeResource;
use App\Models\PayloadType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Spatie\QueryBuilder\QueryBuilder;

class PayloadTypeController extends Controller
{

    public function __construct(Request $request)
    {
        $this->setConstruct($request, PayloadTypeResource::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payloadTypes = QueryBuilder::for(PayloadType::class)
                                    ->allowedFilters(['id', 'name'])
                                    ->defaultSort('-id')
                                    ->paginate($this->perPage, ['*'], 'page', $this->page);

        return $this->collection($payloadTypes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePayloadTypeRequest $request)
    {
        $payloadType = PayloadType::create($request->all());

        return $this->resource($payloadType);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $payloadType = PayloadType::find($id);

        if(!$payloadType)
            return $this->error(404, Config::get('constants.errors.not_found'));

        return $this->resource($payloadType);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePayloadTypeRequest $request, $id)
    {
        $payloadType = PayloadType::find($id);

        if(!$payloadType)
            return $this->error(404, Config::get('constants.errors.not_found'));

        $payloadType->update($request->all());

        return $this->resource($payloadType);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $payloadType = PayloadType::find($id);

        if(!$payloadType)
            return $this->error(404, Config::get('constants.errors.not_found'));

        $payloadType->delete();

        return $this->success([], Config::get('constants.success.delete'));
    }
}
