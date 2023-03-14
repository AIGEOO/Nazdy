<?php

namespace App\Database\Seeders;

use Core\Database\Seeder\Seeder;

class UserSeeder
{
    public function run()
    {
        $seeder = new Seeder();
        
        $faker = $seeder->generator->getFakerInstance();

        $seeder->table('users')->factory([
            'id' => null,
            'name'=> $seeder->faker->unique()->name(),
            'email' => $seeder->faker->unique()->safeEmail(),
            'password' => "12345678",
            'favorites' => $seeder->faker->name() . ', ' . $seeder->faker->name(),
            'role' => $seeder->faker->randomElement(['seller', 'user']),
            'profile_img' => '/temp/lol.jpg',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ])->count(30);

        $seeder->fill();
    }
}