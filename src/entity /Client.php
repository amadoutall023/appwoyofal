<?php
class Client {
    private int $id;
    private string $nom;
    private string $prenom;
    private string $telephone;
    private string $email;
    private \DateTime $dateCreation;

    public function __construct(int $id, string $nom, string $prenom, string $telephone, string $email) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->telephone = $telephone;
        $this->email = $email;
        $this->dateCreation = new \DateTime();
    }

    // Getters
    public function getId(): int { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function getPrenom(): string { return $this->prenom; }
    public function getNomComplet(): string { return $this->nom . ' ' . $this->prenom; }
    public function getTelephone(): string { return $this->telephone; }
    public function getEmail(): string { return $this->email; }
    public function getDateCreation(): \DateTime { return $this->dateCreation; }

    // Méthodes de conversion
    public function toArray(): array {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'telephone' => $this->telephone,
            'email' => $this->email,
            'date_creation' => $this->dateCreation->format('Y-m-d H:i:s')
        ];
    }

    public static function toObject(array $data): self {
        $client = new self(
            0, // ID temporaire
            $data['nom'] ?? '',
            $data['prenom'] ?? '',
            $data['telephone'] ?? '',
            $data['email'] ?? ''
        );

        if (isset($data['id'])) {
            $reflection = new \ReflectionClass(self::class);
            $property = $reflection->getProperty('id');
            $property->setAccessible(true);
            $property->setValue($client, (int)$data['id']);
        }

        if (isset($data['date_creation'])) {
            $reflection = new \ReflectionClass(self::class);
            $property = $reflection->getProperty('dateCreation');
            $property->setAccessible(true);
            $property->setValue($client, new \DateTime($data['date_creation']));
        }

        return $client;
    }
}