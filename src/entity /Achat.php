<?php
class Achat {
    private int $id;
    private string $reference;
    private string $numeroCompteur;
    private string $codeRecharge;
    private float $montant;
    private float $nbreKwt;
    private string $tranche;
    private float $prixUnitaire;
    private \DateTime $dateAchat;
    private string $statut;
    private int $clientId;

    public function __construct(
        int $id,
        string $reference,
        string $numeroCompteur,
        string $codeRecharge,
        float $montant,
        float $nbreKwt,
        string $tranche,
        float $prixUnitaire,
        int $clientId
    ) {
        $this->id = $id;
        $this->reference = $reference;
        $this->numeroCompteur = $numeroCompteur;
        $this->codeRecharge = $codeRecharge;
        $this->montant = $montant;
        $this->nbreKwt = $nbreKwt;
        $this->tranche = $tranche;
        $this->prixUnitaire = $prixUnitaire;
        $this->clientId = $clientId;
        $this->dateAchat = new \DateTime();
        $this->statut = 'success';
    }

    // Getters
    public function getId(): int { return $this->id; }
    public function getReference(): string { return $this->reference; }
    public function getNumeroCompteur(): string { return $this->numeroCompteur; }
    public function getCodeRecharge(): string { return $this->codeRecharge; }
    public function getMontant(): float { return $this->montant; }
    public function getNbreKwt(): float { return $this->nbreKwt; }
    public function getTranche(): string { return $this->tranche; }
    public function getPrixUnitaire(): float { return $this->prixUnitaire; }
    public function getDateAchat(): \DateTime { return $this->dateAchat; }
    public function getStatut(): string { return $this->statut; }
    public function getClientId(): int { return $this->clientId; }

    // Méthodes de conversion
    public function toArray(): array {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'numero_compteur' => $this->numeroCompteur,
            'code_recharge' => $this->codeRecharge,
            'montant' => $this->montant,
            'nbre_kwt' => $this->nbreKwt,
            'tranche' => $this->tranche,
            'prix_unitaire' => $this->prixUnitaire,
            'date_achat' => $this->dateAchat->format('Y-m-d H:i:s'),
            'statut' => $this->statut,
            'client_id' => $this->clientId
        ];
    }

    public static function toObject(array $data): self {
        $achat = new self(
            0, // ID temporaire
            $data['reference'] ?? '',
            $data['numero_compteur'] ?? '',
            $data['code_recharge'] ?? '',
            $data['montant'] ?? 0.0,
            $data['nbre_kwt'] ?? 0.0,
            $data['tranche'] ?? '',
            $data['prix_unitaire'] ?? 0.0,
            $data['client_id'] ?? 0
        );

        if (isset($data['id'])) {
            $reflection = new \ReflectionClass(self::class);
            $property = $reflection->getProperty('id');
            $property->setAccessible(true);
            $property->setValue($achat, (int)$data['id']);
        }

        if (isset($data['date_achat'])) {
            $reflection = new \ReflectionClass(self::class);
            $property = $reflection->getProperty('dateAchat');
            $property->setAccessible(true);
            $property->setValue($achat, new \DateTime($data['date_achat']));
        }

        if (isset($data['statut'])) {
            $reflection = new \ReflectionClass(self::class);
            $property = $reflection->getProperty('statut');
            $property->setAccessible(true);
            $property->setValue($achat, $data['statut']);
        }

        return $achat;
    }
}