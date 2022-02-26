<?php

namespace App\Http\Requests;

use App\Traits\JsonErrors;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Http\FormRequest;

class GetUserRegisterationNotificationRequest extends FormRequest
{
    use JsonErrors;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->hasRole(Config::get('constants.roles.admin_role'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
