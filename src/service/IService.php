<?php
namespace App\Service;

interface ServiceInterface
{
    public function getAll(): array;
    public function getById(int $id): ?object;
    public function create(array $data): object;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}