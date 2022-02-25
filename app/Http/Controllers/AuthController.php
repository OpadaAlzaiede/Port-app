<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request) {
        
        if (!Auth::attempt(['username' => $request['username'], 'password' => $request['password']])) 
            return $this->error(401, Config::get('constants.errors.login_fail'));
        
        $user = auth()->user();
        return $this->success([
            'user' => new UserResource($user),
            'token' => auth()
                ->user()
                ->createToken('Login API Token')
                ->plainTextToken
        ], Config::get('constants.success.login_success'));
    }

    public function register(RegisterRequest $request) {

        $user = User::create($request->all());
        $user->password = Hash::make($user->password);
        $user->save();

        return $this->success([
            'user' => new UserResource($user),
            'token' => $user
            ->createToken('Login API Token')
            ->plainTextToken
        ]);
    }

    public function logout() {

        auth()->user()->tokens()->delete();

        return $this->success([], Config::get('constants.success.logout_success'));
    }
}
