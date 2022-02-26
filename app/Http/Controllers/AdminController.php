<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\GetUserRegisterationNotificationRequest;

class AdminController extends Controller
{
    public function getNotifications(GetUserRegisterationNotificationRequest $request) {

        $notifications = Auth::user()->unreadNotifications;

        foreach($notifications as $notification) {
            $notification->markAsRead();
        }

        return $notifications;
    }
}
