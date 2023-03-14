<?php

namespace App\Database\Seeders;

use Core\Database\Seeder\Seeder;

class ProductSeeder
{
    public function run()
    {
        $seeder = new Seeder();
        
        $faker = $seeder->generator->getFakerInstance();

        $seeder->table('products')->factory([
            'id' => null,
            'seller_id'=> $seeder->faker->unique()->numberBetween(1, 30),
            'name' => $seeder->faker->name(),
            'price' => $seeder->faker->randomFloat(2),
            'category' => $seeder->faker->randomElement(['a', 'b', 'c', 'd', 'e']),
            'availability' => 'exsist',
            'description' => $seeder->faker->paragraph(),
            'width' => $seeder->faker->numberBetween(0, 100),
            'height' => $seeder->faker->numberBetween(0, 100),
            'weight' => $seeder->faker->numberBetween(0, 100),
            'quantity' => $seeder->faker->numberBetween(0, 100),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ])->count(30);

        $seeder->fill();
    }
}