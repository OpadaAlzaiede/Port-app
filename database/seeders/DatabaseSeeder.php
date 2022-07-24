<?php

namespace Database\Seeders;

use App\Models\PayloadRequest;
use App\Models\PayloadType;
use App\Models\Pier;
use App\Models\PortRequest;
use App\Models\ProcessType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Yard;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $merchantRole = Role::create(['name' => Config::get('constants.roles.merchant_role')]);
        $captainRole = Role::create(['name' => Config::get('constants.roles.captain_role')]);
        $adminRole = Role::create(['name' => Config::get('constants.roles.admin_role')]);
        $pierOfficerRole = Role::create(['name' => Config::get('constants.roles.pier_officer_role')]);
        $tugBoatOfficerRole = Role::create(['name' => Config::get('constants.roles.tugboat_officer_role')]);
        $yardOfficerRole = Role::create(['name' => Config::get('constants.roles.yard_officer_role')]);

        for ($i = 1; $i < 6; $i++) {

            User::factory()->create([
                'username' => 'merchant' . $i
            ])->assignRole($merchantRole);

            User::factory()->create([
                'username' => 'captain' . $i
            ])->assignRole($captainRole);

            User::factory()->create(['username' => 'pier_officer' . $i])->assignRole($pierOfficerRole);
            User::factory()->create(['username' => 'yard_officer' . $i])->assignRole($yardOfficerRole);
            User::factory()->create(['username' => 'tugboat_officer' . $i])->assignRole($tugBoatOfficerRole);
        }


        User::factory()->create(['username' => 'admin'])->assignRole($adminRole);
        Pier::factory(5)->create();

        $piers = Pier::all();
        $i = 1;
        foreach ($piers as $pier) {
            DB::table('enter_port_pier')->insert([ 'order' => 1,
                'enter_date' => Carbon::now(),
                'leave_date' => Carbon::now()->addDays(1),
                'enter_port_request_id' => $i++,
                'yard_id' => rand(1,3),
                'pier_id' => $pier->id]);

//            PortRequest::create([
//                'order' => 1,
//                'enter_date' => Carbon::now(),
//                'leave_date' => Carbon::now()->addDays(1),
//                'enter_port_request_id' => $i++,
//                'pier_id' => $pier->id
//            ]);
        }

        for ($i = 0; $i < 10; $i++) {
            PortRequest::create([
                'ship_name' => 'test' . $i,
                'ship_length' => mt_rand(10, 20000),
                'ship_draft_length' => mt_rand(10, 20000),
                'payload_weight' => mt_rand(10, 20000),
                'ship_weight' => mt_rand(10, 20000),
                'shipping_policy_number' => 'test' . $i * 10,
                'status' => mt_rand(1, 3),
                'way' => mt_rand(1, 3),
                'process_type_id' => mt_rand(1, 2),
                'payload_type_id' => mt_rand(1, 4),
                'user_id' => mt_rand(1, 5),
            ]);

        foreach (PayloadType::getTypes() as $type) {

            PayloadType::create(['name' => $type]);
        }

        foreach (ProcessType::getTypes() as $type) {

            ProcessType::create(['name' => $type]);
        }

        for ($i = 0; $i < 10; $i++) {
            PortRequest::create([
                'ship_name' => 'test' . $i,
                'ship_length' => mt_rand(10, 20000),
                'ship_draft_length' => mt_rand(10, 20000),
                'payload_weight' => mt_rand(10, 20000),
                'ship_weight' => mt_rand(10, 20000),
                'shipping_policy_number' => 'test' . $i * 10,
                'status' => mt_rand(1, 3),
                'process_type_id' => mt_rand(1, 2),
                'payload_type_id' => mt_rand(1, 4),
                'user_id' => mt_rand(1, 5),
            ]);

            PayloadRequest::create([
                'amount' => mt_rand(10, 20000),
                'shipping_policy_number' => 'test' . $i * 10,
                'ship_number' => 'test_test_test_test',
                'status' => mt_rand(1, 2),
                'way' => mt_rand(1, 2),
                'date' => '2022-04-21 12:0:0',
                'payload_type_id' => mt_rand(1, 4),
                'process_type_id' => mt_rand(1, 2),
                'user_id' => mt_rand(1, 5),
            ]);

            // Pier::create([
            //     'name' => ucwords($this->faker->name()) . rand(20,100),
            //     'length' => mt_rand(1, 200),
            //     'draft' => mt_rand(1, 200),
            //     'code' => 'test' . $i . '*' . $i,
            //     'type' => rand(1, 2),
            //     'payload_type_id' => rand(1, 4),
            //     'status' => rand(1, 2),
            // ]);

            Yard::create([
                'size' => mt_rand(1, 3000),
                'payload_type_id' => rand(1, 4),
                'name' => rand(1, 4),
                'code' => rand(1, 4),
                'capacity' => rand(1, 4),
            ]);
        }
    }
}
}
