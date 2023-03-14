<?php

declare(strict_types=1);

namespace App\Database\Seeders;

use Core\Database\Seeder\Seeder;

class DatabaseSeeder
{
    private Seeder $seeder;
    
    public function __construct()
    {
        $seeder = new Seeder();
        
        $this->seeder->call([
            UserSeeder::class,
            InvoiceSeeder::class,
            SellerSeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
            ReviewSeeder::class
        ]);
    }
}
