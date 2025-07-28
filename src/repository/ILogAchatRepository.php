<?php
namespace App\Repository;

interface ILogAchatRepository extends IRepository
{
    public function findByNumeroCompteur(string $numeroCompteur): array;
    public function findByStatut(string $statut): array;
    public function findByDateRange(\DateTime $dateDebut, \DateTime $dateFin): array;
    public function findByAdresseIp(string $adresseIp): array;
}