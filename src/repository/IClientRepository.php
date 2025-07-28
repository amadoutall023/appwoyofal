<?php
namespace App\Repository;

interface IClientRepository extends IRepository
{
    public function findByEmail(string $email): ?object;
    public function findByTelephone(string $telephone): ?object;
}