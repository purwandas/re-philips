<?php

use Illuminate\Database\Seeder;

class DistrictTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $faker = Faker\Factory::create('id_ID');

      for($i = 0; $i < 1000; $i++) {
        App\District::create([
          'area_id'=> rand(1, 99),
          'name' => $faker->city,
        ]);
      }
    }
}
