<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notify;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Password;
use App\Http\Requests\ForgetPasswordRequest;

class UserController extends Controller
{
    public function resetPassword(ForgetPasswordRequest $request) {

        $user = User::where('email', $request->email)->first();

        $newPassword = Hash::make(Str::random(10));
        Mail::send(new ResetPasswordMail($newPassword), [], function($message) use ($user){
            $message->to($user);
            $message->subject('Reset Password');
        });

        return $this->success([], Config::get('constants.success.password_reset_success'));
    }

    public function getNotifications() {

        $notifications = array();

        foreach(Notify::getNotifiables() as $model) {
            
            $unRead= DB::table('request_notifications')
                            ->selectRaw("COUNT(*) AS 'count', type")
                            ->where('user_id' , Auth::id())
                            ->where('notifyable_type', $model)
                            ->where('is_read' , 0)
                            ->groupBy('type')
                            ->get();

        $split_model = explode('\\', $model);
        $directory = $split_model[count($split_model)-1];

            $notifications[$directory] = $unRead;
        }

        return response()->json($notifications);
    }
}
