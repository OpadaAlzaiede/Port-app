<?php


namespace App\Traits;

use App\constants\DataBaseConstants;
use Carbon\Carbon;
use App\Traits\UnReadable;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

trait Request
{
    use UnReadable;

    protected $requestModel;
    protected $requestObject;
    protected $refuseModel;

    public function approveRequest($currentUser, $nextUser = null) {

        $currentUserRecord = $this->requestObject->path()->where('user_id', $currentUser->id)->first();
        $currentUserRecord->pivot->is_served = 1;
        $currentUserRecord->pivot->save();

        if(!$nextUser) {
            $this->markAsDoneRequest();
            $this->markAsHoldingRequest();
            $this->setAsUnread($this->requestModel, $this->requestObject);
            return;
        }

        $this->requestObject->path()->attach([$nextUser->id => [
            'is_served' => 0
        ]]);

        $this->requestObject->level++;
        $this->requestObject->save();

        $this->setAsUnread($this->requestModel, $this->requestObject);

    }

    public function refuseRequest($currentUser, $previousUser, $refuseData) {

        $currentUserRecord = $this->requestObject->path()->where('user_id', $currentUser->id)->first();
        $currentUserRecord->pivot->is_served = 1;
        $currentUserRecord->pivot->save();

        $previousUserRecord = $this->requestObject->path()->where('user_id', $previousUser->id)->first();
        $previousUserRecord->pivot->is_served = 0;
        $previousUserRecord->pivot->save();

        $this->refuseModel::create($refuseData);

        $this->markAsBackwardRequest();

        $this->requestObject->level--;
        $this->requestObject->save();

        $this->setAsUnread($this->requestModel, $this->requestObject);
    }

    public function reProcessRequest($currentUser, $nextUser, $newData) {

        $this->requestObject->update($newData);

        $currentUserRecord = $this->requestObject->path()->where('user_id', $currentUser->id)->first();
        $currentUserRecord->pivot->is_served = 1;
        $currentUserRecord->pivot->save();

        $nextUserRecord = $this->requestObject->path()->where('user_id', $nextUser->id)->first();
        $nextUserRecord->pivot->is_served = 0;
        $nextUserRecord->pivot->save();

        $this->markAsForwardRequest();
        $this->requestObject->level++;
        $this->requestObject->save();

        $this->setAsUnread($this->requestModel, $this->requestObject);
    }

    public function cancelRequest($currentUser) {

        $currentUserRecord = $this->requestObject->path()->where('user_id', $currentUser->id)->first();
        $currentUserRecord->pivot->is_served = 1;
        $currentUserRecord->pivot->save();

        $this->markAsCanceledRequest();
        $this->markAsHoldingRequest();

        $this->setAsUnread($this->requestModel, $this->requestObject);
    }


    private function markAsDoneRequest() {

        $this->requestObject->status = DataBaseConstants::getStatusesArr()['DONE'];
        $this->requestObject->save();
    }

    private function markAsCanceledRequest() {

        $this->requestObject->status = DataBaseConstants::getStatusesArr()['CANCELED'];
        $this->requestObject->save();
    }

    private function markAsForwardRequest() {

        $this->requestObject->status = DataBaseConstants::getWaysArr()['FORWARD'];
        $this->requestObject->save();
    }

    private function markAsBackwardRequest() {

        $this->requestObject->status = DataBaseConstants::getWaysArr()['BACKWARD'];
        $this->requestObject->save();
    }

    private function markAsHoldingRequest() {

        $this->requestObject->status = DataBaseConstants::getWaysArr()['HOLDING'];
        $this->requestObject->save();
    }

    public function setRequest($requestModel, $requestObject, $refuseModel) {

        $this->requestModel = $requestModel;
        $this->requestObject = $requestObject;
        $this->refuseModel = $refuseModel;
    }

    public function getRequestsDependingOnStatus($status, $relation, $includes, $filters, $compareColumn, $column, $isServed, $perPage, $page) {

        $requests = $relation->where('is_served', $isServed);

        return QueryBuilder::for($requests)
                            ->allowedIncludes($includes)
                            ->allowedFilters($filters)
                            ->where('status', $status)
                            ->where($compareColumn, $column)
                            ->defaultSort('-id')
                            ->paginate($perPage, ['*'], 'page', $page);
    }


}
