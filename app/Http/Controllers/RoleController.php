<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function getRoles() {

        $adminRole = Config::get('constants.roles.admin_role');
        $officerRole = Config::get('constants.roles.officer_role');

        $adminRoleId = Role::where('name', $adminRole)->first()->id;
        $officerRoleId = Role::where('name', $officerRole)->first()->id;
        
        $systemRoles = [$adminRoleId, $officerRoleId];

        $roles = Role::whereNotIn('id', $systemRoles)->get();

        return $roles;
    }
}
