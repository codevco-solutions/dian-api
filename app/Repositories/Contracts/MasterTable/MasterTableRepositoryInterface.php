<?php

namespace App\Repositories\Contracts\MasterTable;

interface MasterTableRepositoryInterface
{
    public function all(string $table);
    public function findById(string $table, int $id);
    public function findByCode(string $table, string $code);
    public function create(string $table, array $data);
    public function update(string $table, int $id, array $data);
    public function delete(string $table, int $id);
    public function active(string $table);
}
