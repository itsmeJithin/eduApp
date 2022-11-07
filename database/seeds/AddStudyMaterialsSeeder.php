<?php

use App\Models\StudyMaterials;
use Illuminate\Database\Seeder;

class AddStudyMaterialsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(StudyMaterials::class, 100)->create();
    }
}
