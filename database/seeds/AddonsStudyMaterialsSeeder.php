<?php

use App\Models\AddonsStudyMaterials;
use Illuminate\Database\Seeder;

class AddonsStudyMaterialsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(AddonsStudyMaterials::class, 33)->create();
    }
}
