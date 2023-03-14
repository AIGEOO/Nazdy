<?php

namespace App\Database\Seeders;

use Core\Database\Seeder\Seeder;

class ReviewSeeder
{
    public function run()
    {
        $seeder = new Seeder();
        
        $faker = $seeder->generator->getFakerInstance();

        $seeder->table('reviews')->factory([
            'id' => null,
            'user_id'=> $seeder->faker->unique()->numberBetween(1, 30),
            'product_id'=> $seeder->faker->unique()->numberBetween(1, 30),
            'rate' => $seeder->faker->numberBetween(1, 5),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ])->count(30);

        $seeder->fill();
    }
}