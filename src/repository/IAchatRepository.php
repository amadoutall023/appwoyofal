<?php
namespace App\Repository;

interface IAchatRepository extends IRepository
{
    public function findByClientId(int $clientId): array;
    public function findByNumeroCompteur(string $numeroCompteur): array;
    public function findByReference(string $reference): ?object;
    public function findByDateRange(\DateTime $dateDebut, \DateTime $dateFin): array;
    public function findByStatut(string $statut): array;
}