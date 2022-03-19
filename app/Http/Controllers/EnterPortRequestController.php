<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEnterPortRequestRequest;
use App\Http\Requests\UpdateEnterPortRequestRequest;
use App\Http\Resources\EnterPortRequestResource;
use App\Models\PortRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class EnterPortRequestController extends Controller
{

    /**
     * EnterPortRequestController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->setConstruct($request, EnterPortRequestResource::class);
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $enterPortRequests = QueryBuilder::for(PortRequest::class)
            ->allowedIncludes(['processType', 'payloadType', 'user'])
            ->allowedFilters(['ship_name', 'payload_weight', 'shipping_policy_number', 'status'])
            ->defaultSort('-id')
            ->paginate($this->perPage, ['*'], 'page', $this->page);

        return $this->collection($enterPortRequests);
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreEnterPortRequestRequest $request
     * @return void
     */
    public function store(StoreEnterPortRequestRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $enterPortRequest = PortRequest::create($data);
        return $this->resource($enterPortRequest->load([
            'processType',
            'payloadType',
            'user'
        ]));
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $enterPortRequest = PortRequest::find($id);
        if (!$enterPortRequest) {
            $this->error('401', 'NOT FOUND');
        }

        return $this->resource($enterPortRequest->load([
            'processType',
            'payloadType',
            'user'
        ]));
    }

    /**
     * Update the specified resource in storage.
     * @param UpdateEnterPortRequestRequest $request
     * @param int $id
     * @return Response
     */
    public function update(UpdateEnterPortRequestRequest $request, $id)
    {
        $enterPortRequest = PortRequest::find($id);
        if (!$enterPortRequest) {
            $this->error('401', 'NOT FOUND');
        }
        $enterPortRequest->update($request->validated());
        return $this->resource($enterPortRequest->load([
            'processType',
            'payloadType',
            'user'
        ]));
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $enterPortRequest = PortRequest::find($id);
        if (!$enterPortRequest) {
            $this->error('401', 'NOT FOUND');
        }

        $enterPortRequest->delete();

        return $this->success([], 'deleted Successfully');
    }
}
