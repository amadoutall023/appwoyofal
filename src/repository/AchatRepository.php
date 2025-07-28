<?php
namespace App\Repository;

use PDO;

class AchatRepository implements IAchatRepository {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Méthode conforme à l'interface IRepository
    public function create(array $data): object {
        $stmt = $this->pdo->prepare("
            INSERT INTO achats (reference, numero_compteur, code_recharge, montant, nbre_kwt, 
                              tranche, prix_unitaire, date_achat, statut, client_id) 
            VALUES (:reference, :numero_compteur, :code_recharge, :montant, :nbre_kwt, 
                    :tranche, :prix_unitaire, :date_achat, :statut, :client_id)
        ");
        
        $stmt->execute([
            'reference' => $data['reference'],
            'numero_compteur' => $data['numero_compteur'],
            'code_recharge' => $data['code_recharge'],
            'montant' => $data['montant'],
            'nbre_kwt' => $data['nbre_kwt'],
            'tranche' => $data['tranche'],
            'prix_unitaire' => $data['prix_unitaire'],
            'date_achat' => $data['date_achat'],
            'statut' => $data['statut'],
            'client_id' => $data['client_id']
        ]);
        
        // Retourner l'objet créé avec l'ID
        $id = $this->pdo->lastInsertId();
        return $this->findById((int)$id);
    }

    // Méthode spécifique pour créer avec un objet Achat
    public function createFromEntity(Achat $achat): bool {
        $data = $achat->toArray();
        return $this->create($data) !== null;
    }

    // Méthodes de l'interface IRepository
    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM achats ORDER BY date_achat DESC");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function findById(int $id): ?object {
        $stmt = $this->pdo->prepare("SELECT * FROM achats WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_OBJ) ?: null;
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->pdo->prepare("
            UPDATE achats SET 
                statut = :statut,
                montant = :montant,
                nbre_kwt = :nbre_kwt,
                tranche = :tranche,
                prix_unitaire = :prix_unitaire
            WHERE id = :id
        ");
        
        return $stmt->execute([
            'id' => $id,
            'statut' => $data['statut'] ?? null,
            'montant' => $data['montant'] ?? null,
            'nbre_kwt' => $data['nbre_kwt'] ?? null,
            'tranche' => $data['tranche'] ?? null,
            'prix_unitaire' => $data['prix_unitaire'] ?? null
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM achats WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    // Méthodes spécifiques de IAchatRepository
    public function findByClientId(int $clientId): array {
        $stmt = $this->pdo->prepare("SELECT * FROM achats WHERE client_id = :client_id ORDER BY date_achat DESC");
        $stmt->execute(['client_id' => $clientId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function findByNumeroCompteur(string $numeroCompteur): array {
        $stmt = $this->pdo->prepare("SELECT * FROM achats WHERE numero_compteur = :numero_compteur ORDER BY date_achat DESC");
        $stmt->execute(['numero_compteur' => $numeroCompteur]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function findByReference(string $reference): ?object {
        $stmt = $this->pdo->prepare("SELECT * FROM achats WHERE reference = :reference");
        $stmt->execute(['reference' => $reference]);
        return $stmt->fetch(PDO::FETCH_OBJ) ?: null;
    }

    public function findByDateRange(\DateTime $dateDebut, \DateTime $dateFin): array {
        $stmt = $this->pdo->prepare("
            SELECT * FROM achats 
            WHERE date_achat BETWEEN :date_debut AND :date_fin 
            ORDER BY date_achat DESC
        ");
        $stmt->execute([
            'date_debut' => $dateDebut->format('Y-m-d H:i:s'),
            'date_fin' => $dateFin->format('Y-m-d H:i:s')
        ]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function findByStatut(string $statut): array {
        $stmt = $this->pdo->prepare("SELECT * FROM achats WHERE statut = :statut ORDER BY date_achat DESC");
        $stmt->execute(['statut' => $statut]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getConsommationMoisCourant(int $clientId): float {
        $stmt = $this->pdo->prepare("
            SELECT COALESCE(SUM(montant), 0) as total
            FROM achats 
            WHERE client_id = :client_id 
            AND EXTRACT(MONTH FROM date_achat) = EXTRACT(MONTH FROM CURRENT_DATE)
            AND EXTRACT(YEAR FROM date_achat) = EXTRACT(YEAR FROM CURRENT_DATE)
            AND statut = 'success'
        ");
        $stmt->execute(['client_id' => $clientId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (float) $result['total'];
    }
}
