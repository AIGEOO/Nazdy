<?php

declare(strict_types=1);

namespace Core\Database;

use Core\Application;
use PDO;

/**
 * TODO: Make security validation for each function
 * TODO: Refactor DB connection
 */

class Semiloquent
{
    public PDO $db;

    public function __construct(public string $table)
    {
        $this->db = Application::db()->pdo;    
    }

    public function query(string $sql, array $bindings): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->fetch();
    }

    public function all(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM :table");
        $stmt->execute([':table' => $this->table]);
        return $stmt->fetch();
    }

    public function find(string $name)
    {
        $stmt = $this->db->prepare("SELECT * FROM :table WHERE name = :name");
        $stmt->execute([':table' => $this->table, ':name' => $name]);
        return $stmt->fetch();
    }

    public function count(): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM :table");
        $stmt->execute([':table' => $this->table]);
        return (int) $stmt->fetch();
    }

    public function avg(string $column): float
    {
        $stmt = $this->db->prepare("SELECT AVG(:column) FROM :table");
        $stmt->execute([':column' => $column, ':table' => $this->table]);
        return (float) $stmt->fetch();
    }

    public function min(string $column)
    {
        $stmt = $this->db->prepare("SELECT MIN(:column) FROM :table");
        $stmt->execute([':column' => $column, ':table' => $this->table]);
        return $stmt->fetch();
    }

    public function max(string $column)
    {
        $stmt = $this->db->prepare("SELECT MAX(:column) FROM :table");
        $stmt->execute([':column' => $column, ':table' => $this->table]);
        return $stmt->fetch();
    }

    public function sum(string $column): float
    {
        $stmt = $this->db->prepare("SELECT SUM(:column) FROM :table");
        $stmt->execute([':column' => $column, ':table' => $this->table]);
        return (float) $stmt->fetch();
    }

    public function create(array $data): bool
    {
        $keys = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $stmt = $this->db->prepare("INSERT INTO :table ({$keys}) VALUES ({$placeholders})");
        return $stmt->execute($data);
    }

    public function update(array $data, array $condition): bool
    {
        $set = '';
        $conditionString = '';
        $conditionData = [];

        foreach ($data as $key => $value) {
            $set .= "{$key} = :{$key}, ";
        }
        $set = rtrim($set, ', ');

        foreach ($condition as $key => $value) {
            $conditionString .= "{$key} = :{$key} AND ";
            $conditionData[$key] = $value;
        }
        $conditionString = rtrim($conditionString, ' AND ');

        $data = array_merge($data, $conditionData);
        $stmt = $this->db->prepare("UPDATE :table SET {$set} WHERE {$conditionString}");

        return $stmt->execute($data);
    }

    public function updateOrCreate(array $data, array $condition): bool
    {
        $update = $this->update($data, $condition);
        if (!$update) {
            return $this->create($data);
        }
        return $update;
    }
    
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM :table WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
    
    public function where(array $condition): array
    {
        $where = '';
        foreach ($condition as $key => $value) {
            $where .= "{$key} = :{$key} AND ";
        }
        $where = rtrim($where, ' AND ');
        $stmt = $this->db->prepare("SELECT * FROM :table WHERE {$where}");
        $stmt->execute($condition);
        return $stmt->fetch();
    }
    
    public function findOrFail(string $name): array
    {
        $record = $this->find($name);
        if (!$record) {
            throw new \Exception("Record with name {$name} not found in table :table");
        }
        return $record;
    }
}    