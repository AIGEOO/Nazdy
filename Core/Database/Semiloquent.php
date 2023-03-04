<?php

declare(strict_types=1);

namespace Core\Database;

use Core\Exceptions\RecordNotFoundException;
use PDO;

class Semiloquent
{
    public PDO $db;
    protected ?string $query;
    protected ?array $bindings = [];

    public function __construct(public string $table)
    {
        $this->db = \Core\Application::db()->pdo;
        $this->query = "SELECT * FROM {$this->table}";    
    }

    public function query(string $sql, array $bindings): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->fetch();
    }

    public function all(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetch();
    }

    public function find(int $id): array
    {
        return $this->where('id', '=', $id)->get();
    }

    public function where(string $column, string $condition, string|int $value): self
    {
        $this->query = $this->query . " WHERE {$column} {$condition} ?";
        $this->bindings = [$value];
        return $this;
    }

    public function get(): array
    {
        $stmt = $this->db->prepare($this->query);
        $stmt->execute($this->bindings);

        $this->query = 'SELECT * FROM {$this->table}';
        $this->bindings = [];
        return $stmt->fetchAll();
    }

    public function count(string $column = '*'): self
    {
        $this->query = str_replace('*', "COUNT(" . $column . ") AS value", $this->query);
        return $this;
    }

    protected function mathQueries(string $type, string $column): self
    {
        $this->query = "SELECT $type($column) AS value FROM {$this->table}";
        return $this;
    }

    public function avg(string $column): self
    {
        return $this->mathQueries("AVG", $column);
    }

    public function min(string $column): self
    {
        return $this->mathQueries("MIN", $column);
    }

    public function max(string $column): self
    {
        return $this->mathQueries("MAX", $column);
    }

    public function sum(string $column): self
    {
        return $this->mathQueries("SUM", $column);
    }

    public function create(array $data): bool
    {
        $columns = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));

        $this->query = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $this->bindings = array_values($data);

        return is_array($this->get());
    }

    # use where() after update()
    public function update(array $data): self
    {
        $values = implode(', ', array_map(function ($key, $value) {
            return "$key = '$value'";
        }, array_keys($data), $data));

        $this->query = "UPDATE {$this->table} SET " . $values . " ";
        return $this;
    }

    # use where() after updateOrCreate()
    public function updateOrCreate(array $data, string $column, string $condition, string|int $value): bool
    {
        $exsistingCheck = $this->where($column, $condition, $value)->get();

        if ($exsistingCheck) {
            return is_array($this->update($data)->where($column, $condition, $value)->get());
        }

        return $this->create($data);
    }
    
    public function delete(int $id): bool
    {
        if (! empty($this->find($id))) {
            $this->query = "DELETE FROM {$this->table}";
            return is_array($this->where('id', '=', $id)->get());
        }
        
        throw new RecordNotFoundException("Record with id $id not found in table $this->table");
    }
    
    public function findOrFail(int $id): array
    {
        $record = $this->find($id);
        if (empty($record)) {
            throw new RecordNotFoundException("Record with id $id not found in table $this->table");
        }
        return $record;
    }
}
