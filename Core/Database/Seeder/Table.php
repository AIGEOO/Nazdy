<?php

declare(strict_types = 1);

namespace Core\Database\Seeder;

use Faker\Factory;
use Core\Database\Semiloquent;

class Table
{
    protected string $table;
    protected array $columns;
    protected array $values;
    protected Semiloquent $semiloquent;
    protected int $count;
    
    public function __construct()
    {
        // $this->semiloquent = new Semiloquent($this->table);
    }

    public function setName(string $table)
    {
        $this->table = $table;
    }

    public function setCount(int $times)
    {
        $this->count = $times;
    }

    public function refreshValue(array $values)
    {
        $results = [];

        foreach ($values as $value)
        {
            print_r($value);
            // $results[] = call_user_func($value);
        } 

        return $results;
    }

    public function setData(array $data)
    {
        $this->columns = array_keys($data);
        $this->values  = array_values($this->refreshValue($data));
    }

    protected function insert()
    {
        // $this->semiloquent = new Semiloquent($this->table);
        // $this->semiloquent->create($this->data);

        echo "insert";
        // print_r(array_combine($this->columns, $this->values));
    }
    
    public function CreateNewFakerInstance()
    {
        return Factory::create();
    }
    
    public function insertData()
    {
        for ($i = 0; $i <= $this->count; $i++) {
            $this->insert();
        }
    }

    protected function truncate()
    {
        $this->semiloquent->truncate();
    }
}
