<?php

use Illuminate\Database\Seeder;

class RegionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $faker = Faker\Factory::create('id_ID');

      for($i = 0; $i < 100; $i++) {
        App\Region::create([
          'name' => $faker->state,
        ]);
      }
    }
}
