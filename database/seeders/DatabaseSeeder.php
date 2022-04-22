<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\PayloadType;
use App\Models\ProcessType;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Config;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $userRole = Role::create(['name' => Config::get('constants.roles.user_role')]);
        $adminRole = Role::create(['name' => Config::get('constants.roles.admin_role')]);
        $officerRole = Role::create(['name' => Config::get('constants.roles.officer_role')]);

        for($i = 1; $i < 6; $i++) {
            User::factory()->create([
                'username' => 'user'.$i
            ])->assignRole($userRole);
        }

        User::factory()->create(['username' => 'officer'])->assignRole($officerRole);
        User::factory()->create(['username' => 'admin'])->assignRole($adminRole);
        PayloadType::factory(5)->create();
        ProcessType::factory(5)->create();
    }
}
