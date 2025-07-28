<?php
namespace App\Entity;

class Compteur {
    private int $id;
    private string $numeroCompteur;
    private int $clientId;
    private string $statut;
    private \DateTime $dateCreation;

    public function __construct(int $id, string $numeroCompteur, int $clientId, string $statut = 'actif') {
        $this->id = $id;
        $this->numeroCompteur = $numeroCompteur;
        $this->clientId = $clientId;
        $this->statut = $statut;
        $this->dateCreation = new \DateTime();
    }

    // Getters
    public function getId(): int { return $this->id; }
    public function getNumeroCompteur(): string { return $this->numeroCompteur; }
    public function getClientId(): int { return $this->clientId; }
    public function getStatut(): string { return $this->statut; }
    public function getDateCreation(): \DateTime { return $this->dateCreation; }

    // Setters
    public function setStatut(string $statut): void { $this->statut = $statut; }

    // Méthodes de conversion
    public function toArray(): array {
        return [
            'id' => $this->id,
            'numero_compteur' => $this->numeroCompteur,
            'client_id' => $this->clientId,
            'statut' => $this->statut,
            'date_creation' => $this->dateCreation->format('Y-m-d H:i:s')
        ];
    }

    public static function toObject(array $data): self {
        $compteur = new self(
            0, // ID temporaire
            $data['numero_compteur'] ?? '',
            $data['client_id'] ?? 0,
            $data['statut'] ?? 'actif'
        );

        if (isset($data['id'])) {
            $reflection = new \ReflectionClass(self::class);
            $property = $reflection->getProperty('id');
            $property->setAccessible(true);
            $property->setValue($compteur, (int)$data['id']);
        }

        if (isset($data['date_creation'])) {
            $reflection = new \ReflectionClass(self::class);
            $property = $reflection->getProperty('dateCreation');
            $property->setAccessible(true);
            $property->setValue($compteur, new \DateTime($data['date_creation']));
        }

        return $compteur;
    }
}