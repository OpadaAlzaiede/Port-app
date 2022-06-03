<?php

namespace App\Http\Requests;

use App\Traits\JsonErrors;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    use JsonErrors;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $adminRole = Config::get('constants.roles.admin_role');
        $pierOfficerRole = Config::get('constants.roles.pier_officer_role');
        $tugboatOfficerRole = Config::get('constants.roles.tugboat_officer_role');
        $yardOfficerRole = Config::get('constants.roles.yard_officer_role');

        $adminRoleId = Role::where('name', $adminRole)->first()->id;
        $pierOfficerRole = Role::where('name', $pierOfficerRole)->first()->id;
        $tugboatOfficerRoleId = Role::where('name', $tugboatOfficerRole)->first()->id;
        $yardOfficerRoleId = Role::where('name', $yardOfficerRole)->first()->id;

        $systemRoles = [$adminRoleId, $pierOfficerRole, $tugboatOfficerRoleId, $yardOfficerRoleId];

        return [
            'first_name' => 'required',
            'father_name' => 'required',
            'last_name' => 'required',
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required',
            'role_id' => ['required', Rule::exists('roles', 'id')->whereNotIn('id', $systemRoles)],
            'password' => 'required|min:8|confirmed'
        ];
    }
}
