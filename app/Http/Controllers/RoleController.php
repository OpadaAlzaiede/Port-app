<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function getRoles() {

        $adminRole = Config::get('constants.roles.admin_role');
        $pierOfficerRole = Config::get('constants.roles.pier_officer_role');
        $tugboatOfficerRole = Config::get('constants.roles.tugboat_officer_role');
        $yardOfficerRole = Config::get('constants.roles.yard_officer_role');

        $adminRoleId = Role::where('name', $adminRole)->first()->id;
        $pierOfficerRole = Role::where('name', $pierOfficerRole)->first()->id;
        $tugboatOfficerRoleId = Role::where('name', $tugboatOfficerRole)->first()->id;
        $yardOfficerRoleId = Role::where('name', $yardOfficerRole)->first()->id;

        $systemRoles = [$adminRoleId, $pierOfficerRole, $tugboatOfficerRoleId, $yardOfficerRoleId];

        $roles = Role::whereNotIn('id', $systemRoles)->get();

        return $roles;
    }
}
