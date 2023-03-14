<?php

declare(strict_types = 1);

namespace Core\Database\Seeder;

use Faker\Generator;
use Core\Application;
use Core\Database\Seeder\Table;

class Seeder
{
    private array $tables;
    private Table $table;
    public Generator $faker;

    public function __construct()
    {
        $this->table = new Table();
        $this->faker = $this->table->CreateNewFakerInstance();
    }
    
    public function table(string $name)
    {
        $this->table->setName($name);
        return $this;
    }

    public function call(array $seeders)
    {
        foreach ($seeders as $seeder)
        {
            $class = Application::container()->get($seeder);

            if (method_exists($class, 'run')) {
                call_user_func([$class, 'run']);
            }
        }
    }

    public function factory(array $data)
    {
        $this->table->setData($data);
        return $this;
    }

    public function count(int $times)
    {
        $this->table->setCount($times);
    }

    public function fill()
    {
        $this->table->insertData();
        $this->table->setName("");
        $this->table->setData([]);
        $this->table->setCount(0);
    }
}