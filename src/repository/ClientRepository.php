<?php
namespace App\Repository;

class ClientRepository implements IClientRepository {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function findById(int $id): ?Client {
        $stmt = $this->pdo->prepare("
            SELECT id, nom, prenom, telephone, email, date_creation 
            FROM clients 
            WHERE id = :id
        ");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return Client::toObject($data);
    }

    public function create(Client $client): bool {
        $data = $client->toArray();
        $stmt = $this->pdo->prepare("
            INSERT INTO clients (nom, prenom, telephone, email, date_creation) 
            VALUES (:nom, :prenom, :telephone, :email, :date_creation)
        ");
        
        return $stmt->execute([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'telephone' => $data['telephone'],
            'email' => $data['email'],
            'date_creation' => $data['date_creation']
        ]);
    }
}
