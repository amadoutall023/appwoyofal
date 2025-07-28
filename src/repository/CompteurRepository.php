<?php
namespace App\Repository;

use App\Entity\Compteur;
use App\Entity\Client;
use App\Entity\Achat;
use App\Entity\LogAchat;
use PDO;

class CompteurRepository implements ICompteurRepository{
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function findByNumero(string $numeroCompteur): ?Compteur {
        $stmt = $this->pdo->prepare("
            SELECT id, numero_compteur, client_id, statut, date_creation 
            FROM compteurs 
            WHERE numero_compteur = :numero AND statut = 'actif'
        ");
        $stmt->execute(['numero' => $numeroCompteur]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return Compteur::toObject($data);
    }

    public function create(Compteur $compteur): bool {
        $data = $compteur->toArray();
        $stmt = $this->pdo->prepare("
            INSERT INTO compteurs (numero_compteur, client_id, statut, date_creation) 
            VALUES (:numero_compteur, :client_id, :statut, :date_creation)
        ");
        
        return $stmt->execute([
            'numero_compteur' => $data['numero_compteur'],
            'client_id' => $data['client_id'],
            'statut' => $data['statut'],
            'date_creation' => $data['date_creation']
        ]);
    }
}