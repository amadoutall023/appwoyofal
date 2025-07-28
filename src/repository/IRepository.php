<?php
namespace App\Repository;

interface IRepository
{
    public function findAll(): array;
    public function findById(int $id): ?object;
    public function create(array $data): object;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}