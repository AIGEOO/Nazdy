<?php

namespace App\Database\Seeders;

use Core\Database\Seeder\Seeder;

class SellerSeeder
{
    public function run()
    { 
        $seeder = new Seeder();
        
        $faker = $seeder->generator->getFakerInstance();

        $seeder->table('sellers')->factory([
            'id' => null,
            'name'=> $seeder->faker->unique()->name(),
            'email' => $seeder->faker->unique()->safeEmail(),
            'phone' => $seeder->faker->phoneNumber(),
            'about' => $seeder->faker->paragraph(),
            'profile_img' => '/temp/lol.jpg',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ])->count(30);

        $seeder->fill();
    }
}