<?php
namespace App\Repository;

interface ICompteurRepository extends IRepository
{
    public function findByNumero(string $numeroCompteur): ?object;
    public function findByClientId(int $clientId): array;
    public function findByStatut(string $statut): array;
}