<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgetPasswordRequest;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

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
}
