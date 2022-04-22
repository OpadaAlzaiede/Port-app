<?php

namespace Database\Seeders;

use App\Models\PayloadRequest;
use App\Models\PortRequest;
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

        for ($i = 0; $i<10 ; $i++)
        {
            PortRequest::create([
               'ship_name' => 'test' . $i,
               'ship_length' => mt_rand(10,20000),
               'ship_draft_length' => mt_rand(10,20000),
               'payload_weight' => mt_rand(10,20000),
               'ship_weight' => mt_rand(10,20000),
               'shipping_policy_number' => 'test' . $i * 10,
                'status' => mt_rand(1,3),
                'process_type_id' => mt_rand(1,5),
                'payload_type_id' => mt_rand(1,5),
                'user_id' => mt_rand(1,5),

            ]);

            PayloadRequest::create([
                'amount' => mt_rand(10,20000),
                'shipping_policy_number' => 'test' . $i * 10,
                'ship_number' => 'test_test_test_test',
                'status' => mt_rand(1,2),
                'way' => mt_rand(1,2),
                'date' =>  '2022-04-21 12:0:0',
                'payload_type_id' => mt_rand(1,5),
                'process_type_id' => mt_rand(1,5),
                'user_id' => mt_rand(1,5),
            ]);
        }
    }
}
