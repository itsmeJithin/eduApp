<?php

use App\Models\LatestNews;
use Illuminate\Database\Seeder;

class AddLatestNewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(LatestNews::class, 25)->create();

    }
}
