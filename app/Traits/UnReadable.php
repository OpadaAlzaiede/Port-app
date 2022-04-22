<?php

namespace App\Traits;

use App\Models\Notify;

use App\Models\UnreadApplication;
use Illuminate\Support\Facades\Auth;



trait UnReadable
{

    public function setAsUnread($model, $request) {

        $requestStatus = $request->status;

        $users = $request->path()->get();

        $pendingsUsersIds = [];
        $inProgressUsersIds = [];

        foreach($users as $user) {
            if($user->pivot->is_served === 0) {
                array_push($pendingsUsersIds, $user->id);
            }else {
                array_push($inProgressUsersIds, $user->id);
            }
        }

        if($requestStatus === 1) {
            foreach($pendingsUsersIds as $userId) {
                $this->setRequestAsUnreadInPhase($model, $request->id, $userId, 0);
            }
            foreach($inProgressUsersIds as $userId) {
                $this->setRequestAsUnreadInPhase($model, $request->id, $userId, 1);
            }
        }else if($requestStatus === 2) {
            foreach($request->path()->get() as $user) {
                $this->setRequestAsUnreadInPhase($model, $request->id, $user->id, 2);
            }
        }else {
            foreach($request->path()->get() as $user) {
                $this->setRequestAsUnreadInPhase($model, $request->id, $user->id, 3);
            }
        }
    }

    protected function setRequestAsUnreadInPhase($model, $requestId, $userId, $type) {

            $oldNotification = Notify::where('notifyable_id', $requestId)
                                            ->where('notifyable_type', $model)
                                            ->where('user_id', $userId)
                                            ->where('type', $type)
                                            ->where('is_read', false)
                                            ->get();

            $otherStateNotifications = Notify::where('notifyable_id', $requestId)
                                                        ->where('notifyable_type', $model)
                                                        ->where('user_id', $userId)
                                                        ->where('type', '!=', $type)
                                                        ->where('is_read', false)
                                                        ->get();

            foreach($otherStateNotifications as $notify)
                $notify->delete();

            if(count($oldNotification) > 0)
                return;

            $notify = new Notify();
            $notify->user_id = $userId;
            $notify->notifyable_id = $requestId;
            $notify->notifyable_type = $model;
            $notify->type = $type;
            $notify->municipality_id = Auth::user()->municipality_id;
            $notify->save();
    }

    public function setAsRead($user_id, $requestId, $type, $model) {

        $notifies = Notify::where('user_id', $user_id)
                            ->where('notifyable_id', $requestId)
                            ->where('type', $type)
                            ->where('notifyable_type', $model)
                            ->get();

        foreach($notifies as $notify)
        {
            $notify->delete();
        }
    }
}
