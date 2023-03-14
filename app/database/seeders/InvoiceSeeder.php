<?php

namespace App\Database\Seeders;

use Core\Database\Seeder\Seeder;

class InvoiceSeeder
{
    public function run()
    {
        $seeder = new Seeder();
        
        $faker = $seeder->generator->getFakerInstance();

        $seeder->table('invoices')->factory([
            'id' => null,
            'user_id'=> $seeder->faker->unique()->numberBetween(1, 30),
            'products_info' => "{}",
            'total' => $seeder->faker->randomFloat(2),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ])->count(30);

        $seeder->fill();
    }
}