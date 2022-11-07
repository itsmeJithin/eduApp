<?php

use App\Models\AddonsVideoMaterials;
use Illuminate\Database\Seeder;

class AddonVideoMaterialsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(AddonsVideoMaterials::class, 73)->create();

    }
}
