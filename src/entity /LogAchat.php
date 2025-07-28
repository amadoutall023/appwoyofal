<?php
class LogAchat {
    private int $id;
    private \DateTime $dateHeure;
    private string $localisation;
    private string $adresseIp;
    private string $statut;
    private string $numeroCompteur;
    private ?string $codeRecharge;
    private ?float $nbreKwt;
    private string $messageErreur;

    public function __construct(
        int $id,
        string $localisation,
        string $adresseIp,
        string $statut,
        string $numeroCompteur,
        ?string $codeRecharge = null,
        ?float $nbreKwt = null,
        string $messageErreur = ''
    ) {
        $this->id = $id;
        $this->dateHeure = new \DateTime();
        $this->localisation = $localisation;
        $this->adresseIp = $adresseIp;
        $this->statut = $statut;
        $this->numeroCompteur = $numeroCompteur;
        $this->codeRecharge = $codeRecharge;
        $this->nbreKwt = $nbreKwt;
        $this->messageErreur = $messageErreur;
    }

    // Getters
    public function getId(): int { return $this->id; }
    public function getDateHeure(): \DateTime { return $this->dateHeure; }
    public function getLocalisation(): string { return $this->localisation; }
    public function getAdresseIp(): string { return $this->adresseIp; }
    public function getStatut(): string { return $this->statut; }
    public function getNumeroCompteur(): string { return $this->numeroCompteur; }
    public function getCodeRecharge(): ?string { return $this->codeRecharge; }
    public function getNbreKwt(): ?float { return $this->nbreKwt; }
    public function getMessageErreur(): string { return $this->messageErreur; }

    // Méthodes de conversion
    public function toArray(): array {
        return [
            'id' => $this->id,
            'date_heure' => $this->dateHeure->format('Y-m-d H:i:s'),
            'localisation' => $this->localisation,
            'adresse_ip' => $this->adresseIp,
            'statut' => $this->statut,
            'numero_compteur' => $this->numeroCompteur,
            'code_recharge' => $this->codeRecharge,
            'nbre_kwt' => $this->nbreKwt,
            'message_erreur' => $this->messageErreur
        ];
    }

    public static function toObject(array $data): self {
        $log = new self(
            0, // ID temporaire
            $data['localisation'] ?? '',
            $data['adresse_ip'] ?? '',
            $data['statut'] ?? '',
            $data['numero_compteur'] ?? '',
            $data['code_recharge'] ?? null,
            $data['nbre_kwt'] ?? null,
            $data['message_erreur'] ?? ''
        );

        if (isset($data['id'])) {
            $reflection = new \ReflectionClass(self::class);
            $property = $reflection->getProperty('id');
            $property->setAccessible(true);
            $property->setValue($log, (int)$data['id']);
        }

        if (isset($data['date_heure'])) {
            $reflection = new \ReflectionClass(self::class);
            $property = $reflection->getProperty('dateHeure');
            $property->setAccessible(true);
            $property->setValue($log, new \DateTime($data['date_heure']));
        }

        return $log;
    }
}