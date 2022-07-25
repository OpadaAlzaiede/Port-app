<?php

namespace App\Http\Controllers;

use App\constants\DataBaseConstants;
use App\Http\Requests\ApproveEnterPortRequest;
use App\Http\Requests\RefusePayloadRequestRequest;
use App\Http\Requests\StoreEnterPortRequestRequest;
use App\Http\Requests\UpdateEnterPortRequestRequest;
use App\Http\Resources\EnterPortRequestResource;
use App\Models\Pier;
use App\Models\PortRequest;
use App\Models\PortRequestItem;
use App\Models\Rejection;
use App\Models\User;
use App\Models\Yard;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;

class EnterPortRequestController extends Controller
{
    use \App\Traits\Request;

    private $includes = ['processType', 'payloadType', 'user', 'portRequestItems'];
    private $filters = ['id'];


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
            ->allowedIncludes($this->includes)
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
        $this->setRequest(PortRequest::class, $enterPortRequest, Rejection::class);
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
        $officer = User::getUserByRoleName(Config::get('constants.roles.pier_officer_role'));
        $this->reProcessRequest(Auth::user(), $officer, $request->validated());
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

    public function approve(ApproveEnterPortRequest $request, $id)
    {

        $enterPortRequest = PortRequest::find($id);

        if (!$enterPortRequest)
            return $this->error(404, Config::get('constants.errors.not_found'));

        if (!$this->checkIfCanMakeAction($enterPortRequest))
            return $this->error(401, Config::get('constants.errors.unauthorized'));

        $this->setRequest(PortRequest::class, $enterPortRequest, Rejection::class);
        $matchPier = Pier::find($this->chooseAvailablePier($enterPortRequest));

        if (!$matchPier)
            return $this->error(301, "couldn't found appropriate pier !");

        $matchYard = new Yard();

        $yardResult = $matchYard->getAppropriateYardByPierId($matchPier, $enterPortRequest)->first();

        if (!$yardResult)
            return $this->error(301, "couldn't found appropriate yard !");

        $this->attachPortPier($matchPier, $yardResult->yard_id, $enterPortRequest, $request->validated());

        $this->approveRequest(Auth::user());


        $yard = Yard::find($yardResult->yard_id);

        $yard->changeCapacity($enterPortRequest);

        return $this->resource($enterPortRequest->load([
            'processType',
            'payloadType',
            'user',
            'portRequestItems'
        ]));
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

        return $this->resource($enterPortRequest->load([
            'processType',
            'payloadType',
            'user',
            'portRequestItems'
        ]));
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

        return $this->resource($enterPortRequest->load([
            'processType',
            'payloadType',
            'user',
            'portRequestItems'
        ]));
    }

    private function scopeRequests($status, $isServed)
    {

        $requests = $this->getRequestsDependingOnStatus(
            $status,
            Auth::user()->enterPortRequests(),
            $this->includes,
            $this->filters,
            $isServed,
            $this->perPage,
            $this->page
        );

        return $requests;
    }

    private function flushNotifications($requests, $status)
    {

        if ($requests)
            foreach ($requests as $request) {
                $this->setAsRead(Auth::id(), $request->id, $status, PortRequest::class);
            }
    }

    private function chooseAvailablePier(PortRequest $enterPortRequest)
    {
        return $enterPortRequest->pickPier($enterPortRequest);
    }

    private function attachPortPier(Pier $pier, $matchYardId, PortRequest $enterPortRequest, $dateDetails)
    {

        $model = DB::table('enter_port_pier')->where('pier_id', $pier->id)->orderByDesc('order')->first();


        if (!$pier->enterPortRequests()->exists()) {

            $pier->enterPortRequests()->attach($enterPortRequest->id, [
                'order' => is_null($model) ? 1 : $model->order + 1,
                'enter_date' => is_null($model) ? Carbon::now() : $model->leave_date,
                'yard_id', $matchYardId
            ]);
            return;
        }

        $getLastPierOrder = $pier->enterPortRequests()->latest('id')->first();
        $leaveDate = new Carbon($getLastPierOrder->pivot->leave_date);

        $pier->enterPortRequests()->attach($enterPortRequest->id, [
            'order' => ++$getLastPierOrder->pivot->order,
            'enter_date' => $getLastPierOrder->pivot->leave_date,
            'leave_date' => $leaveDate->addHours($dateDetails['leave_date']),
            'yard_id' => $matchYardId
        ]);
    }

    public function getPending()
    {

        $pending = $this->scopeRequests(DataBaseConstants::getStatusesArr()['IN_PROGRESS'], 0);
        $this->flushNotifications($pending, 0);
        return $this->collection($pending);
    }

    public function getInProgress()
    {

        $inProgress = $this->scopeRequests(DataBaseConstants::getStatusesArr()['IN_PROGRESS'], 1);
        $this->flushNotifications($inProgress, 1);
        return $this->collection($inProgress);
    }

    public function getDone()
    {

        $done = $this->scopeRequests(DataBaseConstants::getStatusesArr()['DONE'], 1);
        $this->flushNotifications($done, 2);
        return $this->collection($done);
    }

    public function getCanceled()
    {

        $canceled = $this->scopeRequests(DataBaseConstants::getStatusesArr()['CANCELED'], 1);
        $this->flushNotifications($canceled, 3);
        return $this->collection($canceled);
    }
}
