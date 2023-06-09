<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Rejection;
use Illuminate\Http\Request;
use App\Models\PayloadRequest;
use App\Models\PayloadRequestItem;
use App\constants\DataBaseConstants;
use App\Http\Requests\RefusePayloadRequestRequest;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\Config;
use App\Traits\Request as CustomRequest;
use App\Http\Resources\PayloadRequestResource;
use App\Http\Requests\StorePayloadRequestRequest;
use App\Http\Requests\UpdatePayloadRequestRequest;
use Spatie\QueryBuilder\AllowedFilter;

class PayloadRequestController extends Controller
{
    use CustomRequest;

    private $includes = ['payloadRequestItems', 'processType', 'payloadType', 'user', 'refusals'];
    private $filters = ['id', 'shipping_policy_number', 'ship_number'];

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
                                    ->allowedIncludes($this->includes)
                                    ->allowedFilters(['id', 'status', AllowedFilter::exact('shipping_policy_number')])
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
        $payloadRequest->status = DataBaseConstants::getStatusesArr()['IN_PROGRESS'];
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
        $this->setAsUnread(PayloadRequest::class, $payloadRequest);

        return $this->resource($payloadRequest->load($this->includes));
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

        return $this->resource($payloadRequest->load($this->includes));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePayloadRequestRequest $request, PayloadRequest $requestObject)
    {
        if($request->items) {
            $requestObject->payloadRequestItems()->delete();
            foreach($request->items as $item) {
                PayloadRequestItem::create([
                    'name' => $item['name'],
                    'amount' => $item['amount'],
                    'payload_request_id' => $requestObject->id
                ]);
            }
        }
        $this->setRequest(PayloadRequest::class, $requestObject, Rejection::class);

        $officer = User::getUserByRoleName(Config::get('constants.roles.pier_officer_role'));

        $this->reProcessRequest(Auth::user(), $officer, $request->all());

        return $this->resource($requestObject->load($this->includes));
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

    public function getPendings() {

        $pendings = $this->scopeRequests(DataBaseConstants::getStatusesArr()['IN_PROGRESS'], 0);
        $this->flushNotifications($pendings, 0);
        return $this->collection($pendings);
    }

    public function getInProgress() {

        $inProgress = $this->scopeRequests(DataBaseConstants::getStatusesArr()['IN_PROGRESS'], 1);
        $this->flushNotifications($inProgress, 1);
        return $this->collection($inProgress);
    }

    public function getDone() {

        $dones = $this->scopeRequests(DataBaseConstants::getStatusesArr()['DONE'], 1);
        $this->flushNotifications($dones, 2);
        return $this->collection($dones);
    }

    public function getCanceled() {

        $canceled = $this->scopeRequests(DataBaseConstants::getStatusesArr()['CANCELED'], 1);
        $this->flushNotifications($canceled, 3);
        return $this->collection($canceled);
    }

    public function approve($id) {

        $payloadRequest = PayloadRequest::find($id);

        if(!$payloadRequest)
            return $this->error(404, Config::get('constants.errors.not_found'));

        if(!$this->checkIfCanMakeAction($payloadRequest))
            return $this->error(401, Config::get('constants.errors.unauthorized'));

        $this->setRequest(PayloadRequest::class, $payloadRequest, Rejection::class);
        $this->approveRequest(Auth::user());

        return $this->resource($payloadRequest->load($this->includes));
    }

    public function refuse(RefusePayloadRequestRequest $request, $id) {

        $payloadRequest = PayloadRequest::find($id);

        if(!$payloadRequest)
            return $this->error(404, Config::get('constants.errors.not_found'));

        if(!$this->checkIfCanMakeAction($payloadRequest))
            return $this->error(401, Config::get('constants.errors.unauthorized'));

        $data['reason'] = $request->reason;
        $data['date'] = Carbon::now();
        $data['rejectable_type'] = PayloadRequest::class;
        $data['rejectable_id'] = $payloadRequest->id;
        $data['user_id'] = Auth::id();

        $this->setRequest(PayloadRequest::class, $payloadRequest, Rejection::class);

        $user = User::find($payloadRequest->user_id);

        $this->refuseRequest(Auth::user(), $user, $data);

        return $this->resource($payloadRequest->load($this->includes));
    }

    public function cancel($id) {

        $payloadRequest = PayloadRequest::find($id);

        if(!$payloadRequest)
            return $this->error(404, Config::get('constants.errors.not_found'));

        if(!$this->checkIfCanMakeAction($payloadRequest))
            return $this->error(401, Config::get('constants.errors.unauthorized'));

        $this->setRequest(PayloadRequest::class, $payloadRequest, Rejection::class);

        $this->cancelRequest(Auth::user());

        return $this->resource($payloadRequest->load($this->includes));
    }

    private function checkIfCanMakeAction($payloadRequest) {

        $userRecord = $payloadRequest->path()->where('user_id', Auth::id())->first();

        if(!$userRecord)
            return false;

        return $userRecord->pivot->is_served === DataBaseConstants::IS_SERVED_NO;
    }

    private function scopeRequests($status, $isServed) {

        $requests =  $this->getRequestsDependingOnStatus(
            $status,
            Auth::user()->payloadRequests(),
            $this->includes,
            $this->filters,
            $isServed,
            $this->perPage,
            $this->page
        );

        return $requests;
    }

    private function flushNotifications($requests, $status) {

        if($requests)
            foreach ($requests as $request) {
                $this->setAsRead(Auth::id(), $request->id, $status, PayloadRequest::class);
            }
    }
}
