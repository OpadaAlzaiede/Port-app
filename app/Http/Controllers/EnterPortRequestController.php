<?php

namespace App\Http\Controllers;

use App\constants\DataBaseConstants;
use App\Http\Requests\RefusePayloadRequestRequest;
use App\Http\Requests\StoreEnterPortRequestRequest;
use App\Http\Requests\UpdateEnterPortRequestRequest;
use App\Http\Resources\EnterPortRequestResource;
use App\Models\PortRequest;
use App\Models\PortRequestItem;
use App\Models\Rejection;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Spatie\QueryBuilder\QueryBuilder;

class EnterPortRequestController extends Controller
{
    use \App\Traits\Request;

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
        $data['status'] = DataBaseConstants::getStatusesArr()["IN_PROGRESS"];
        $data['way'] = DataBaseConstants::getWaysArr()["FORWARD"];
        $enterPortRequest = PortRequest::create($data);
        $enterPortRequestItems = $request->get('enter_port_request_items');
        foreach ($enterPortRequestItems as $enterPortRequestItem) {
            PortRequestItem::create([
                'name' => $enterPortRequestItem['name'],
                'amount' => $enterPortRequestItem['amount'],
                'enter_port_request_id' => $enterPortRequest->id,
            ]);
        }

        $enterPortRequest->createPath();

        return $this->resource($enterPortRequest->load([
            'processType',
            'payloadType',
            'user',
            'portRequestItems'
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
            'user',
            'portRequestItems'
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
        if ($request->has('enter_port_request_items')) {
            $enterPortRequestItems = $enterPortRequest->portRequestItems;
            foreach ($enterPortRequestItems as $enterPortRequestItem) {
                $enterPortRequestItem->delete();
            }
            $newEnterPortRequestItems = $request->get('enter_port_request_items');
            foreach ($newEnterPortRequestItems as $newEnterPortRequestItem) {
                PortRequestItem::create([
                    'name' => $newEnterPortRequestItem['name'],
                    'amount' => $newEnterPortRequestItem['amount'],
                    'enter_port_request_id' => $enterPortRequest->id,
                ]);
            }
        }
        $enterPortRequest->update($request->validated());
        return $this->resource($enterPortRequest->load([
            'processType',
            'payloadType',
            'user',
            'portRequestItems'
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


    private function checkIfCanMakeAction($request)
    {

        $userRecord = $request->path()->where('user_id', Auth::id())->first();

        if ($userRecord->pivot->is_served != 0)
            return false;

        return true;
    }

    public function approve($id)
    {

        $enterPortRequest = PortRequest::find($id);

        if (!$enterPortRequest)
            return $this->error(404, Config::get('constants.errors.not_found'));

        if (!$this->checkIfCanMakeAction($enterPortRequest))
            return $this->error(401, Config::get('constants.errors.unauthorized'));

        $this->setRequest(PortRequest::class, $enterPortRequest, Rejection::class);
        $this->approveRequest(Auth::user());

        return $this->resource($enterPortRequest->load($this->includes));
    }

    public function refuse(RefusePayloadRequestRequest $request, $id)
    {

        $enterPortRequest = PortRequest::find($id);

        if (!$enterPortRequest)
            return $this->error(404, Config::get('constants.errors.not_found'));

        if (!$this->checkIfCanMakeAction($enterPortRequest))
            return $this->error(401, Config::get('constants.errors.unauthorized'));

        $data['reason'] = $request->reason;
        $data['date'] = Carbon::now();
        $data['rejectable_type'] = PortRequest::class;
        $data['rejectable_id'] = $enterPortRequest->id;
        $data['user_id'] = Auth::id();

        $this->setRequest(PortRequest::class, $enterPortRequest, Rejection::class);

        $user = User::find($enterPortRequest->user_id);

        $this->refuseRequest(Auth::user(), $user, $data);

        return $this->resource($enterPortRequest);
    }

    public function cancel($id)
    {

        $enterPortRequest = PortRequest::find($id);

        if (!$enterPortRequest)
            return $this->error(404, Config::get('constants.errors.not_found'));

        if (!$this->checkIfCanMakeAction($enterPortRequest))
            return $this->error(401, Config::get('constants.errors.unauthorized'));

        $this->setRequest(PortRequest::class, $enterPortRequest, Rejection::class);

        $this->cancelRequest(Auth::user());

        return $this->resource($enterPortRequest);
    }
}
