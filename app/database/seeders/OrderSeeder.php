<?php

namespace App\Database\Seeders;

use Core\Database\Seeder\Seeder;

class OrderSeeder
{
    public function run()
    {
        $seeder = new Seeder();
        
        $faker = $seeder->generator->getFakerInstance();

        $seeder->table('orders')->factory([
            'id' => null,
            'user_id'=> $seeder->faker->numberBetween(1, 30),
            'product_id'=> $seeder->faker->numberBetween(1, 30),
            'quantity' => $seeder->faker->numberBetween(0, 100),
            'total' => $seeder->faker->randomFloat(2),
            'street' => $seeder->faker->streetName(),
            'city' => $seeder->faker->city(),
            'country' => $seeder->faker->countryCode(),
            'postcode' => $seeder->faker->postcode(),
            'status' => $seeder->faker->randomElement(['packaged', 'shipped', 'ordered', 'delivered']),
            'shipping_cost' => $seeder->faker->randomFloat(2),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ])->count(30);

        $seeder->fill();
    }
}