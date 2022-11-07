<?php

use App\Models\StaffUsers;
use Illuminate\Database\Seeder;

class AddStaffUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(StaffUsers::class, 2)->create();
    }
}
