<?php
namespace App\Service;

use App\Entity\Achat;
use App\Entity\LogAchat;
use App\Repository\IAchatRepository;
use App\Repository\ILogAchatRepository;
use App\Repository\ICompteurRepository;
use App\Repository\IClientRepository;
use App\Core\Response\ApiResponse;

class AchatService {
    private IAchatRepository $achatRepository;
    private ILogAchatRepository $logAchatRepository;
    private ICompteurRepository $compteurRepository;
    private IClientRepository $clientRepository;

    // Tranches de prix par kWh
    private const TRANCHES = [
        ['min' => 0, 'max' => 25000, 'prix' => 79.98, 'nom' => 'Tranche 1'],
        ['min' => 25001, 'max' => 50000, 'prix' => 84.23, 'nom' => 'Tranche 2'],
        ['min' => 50001, 'max' => 100000, 'prix' => 88.67, 'nom' => 'Tranche 3'],
        ['min' => 100001, 'max' => PHP_INT_MAX, 'prix' => 93.16, 'nom' => 'Tranche 4']
    ];

    public function __construct(
        IAchatRepository $achatRepository,
        ILogAchatRepository $logAchatRepository,
        ICompteurRepository $compteurRepository,
        IClientRepository $clientRepository
    ) {
        $this->achatRepository = $achatRepository;
        $this->logAchatRepository = $logAchatRepository;
        $this->compteurRepository = $compteurRepository;
        $this->clientRepository = $clientRepository;
    }

    public function acheterCredit(string $numeroCompteur, float $montant): ApiResponse {
        $logData = [
            'date_heure' => date('Y-m-d H:i:s'),
            'localisation' => 'Dakar, Sénégal',
            'adresse_ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'numero_compteur' => $numeroCompteur,
            'statut' => 'Échec',
            'code_recharge' => null,
            'nbre_kwt' => 0,
            'message_erreur' => null
        ];

        try {
            // Validation du montant
            if ($montant <= 0) {
                $error = "Le montant doit être supérieur à zéro";
                $this->logTransaction($logData, $error);
                return ApiResponse::error($error, 400);
            }

            // Vérification de l'existence du compteur
            $compteur = $this->compteurRepository->findByNumero($numeroCompteur);
            if (!$compteur) {
                $error = "Le numéro de compteur non retrouvé";
                $this->logTransaction($logData, $error);
                return ApiResponse::error($error, 404);
            }

            // Récupération du client
            $client = $this->clientRepository->findById($compteur->getClientId());
            if (!$client) {
                $error = "Client associé au compteur non trouvé";
                $this->logTransaction($logData, $error);
                return ApiResponse::error($error, 404);
            }

            // Calcul de la consommation du mois courant
            $consommationMoisCourant = $this->achatRepository->getConsommationMoisCourant($compteur->getClientId());
            
            // Calcul des kWh et de la tranche
            $calculResult = $this->calculerKwhEtTranche($montant, $consommationMoisCourant);
            
            // Génération du code de recharge
            $codeRecharge = $this->genererCodeRecharge();
            $reference = $this->genererReference();

            // Création de l'achat
            $achat = new Achat(
                null,
                $reference,
                $numeroCompteur,
                $codeRecharge,
                $montant,
                $calculResult['nbre_kwt'],
                $calculResult['tranche'],
                $calculResult['prix_unitaire'],
                date('Y-m-d H:i:s'),
                'success',
                $compteur->getClientId()
            );

            $created = $this->achatRepository->create($achat);
            
            if (!$created) {
                $error = "Erreur lors de l'enregistrement de l'achat";
                $this->logTransaction($logData, $error);
                return ApiResponse::error($error, 500);
            }

            // Log de succès
            $logData['statut'] = 'Success';
            $logData['code_recharge'] = $codeRecharge;
            $logData['nbre_kwt'] = $calculResult['nbre_kwt'];
            $this->logTransaction($logData);

            // Préparation de la réponse
            $responseData = [
                'compteur' => $numeroCompteur,
                'reference' => $reference,
                'code' => $codeRecharge,
                'date' => date('Y-m-d H:i:s'),
                'tranche' => $calculResult['tranche'],
                'prix' => $calculResult['prix_unitaire'],
                'nbreKwt' => $calculResult['nbre_kwt'],
                'client' => $client->getNom() . ' ' . $client->getPrenom()
            ];

            return ApiResponse::success($responseData, "Achat effectué avec succès");

        } catch (\Exception $e) {
            $error = "Erreur système: " . $e->getMessage();
            $this->logTransaction($logData, $error);
            return ApiResponse::error($error, 500);
        }
    }

    private function calculerKwhEtTranche(float $montant, float $consommationActuelle): array {
        $montantRestant = $montant;
        $kwhTotal = 0;
        $trancheUtilisee = '';
        $prixUnitaire = 0;

        foreach (self::TRANCHES as $tranche) {
            if ($montantRestant <= 0) break;

            $limiteInferieure = max($tranche['min'], $consommationActuelle + 1);
            $limiteSuperieure = $tranche['max'];

            if ($limiteInferieure > $limiteSuperieure) continue;

            $capaciteTranche = $limiteSuperieure - $limiteInferieure + 1;
            $coutTranche = $capaciteTranche * $tranche['prix'];

            if ($montantRestant >= $coutTranche) {
                // Utiliser toute la tranche
                $kwhTotal += $capaciteTranche;
                $montantRestant -= $coutTranche;
                $consommationActuelle += $capaciteTranche;
            } else {
                // Utiliser partiellement la tranche
                $kwhPartiels = $montantRestant / $tranche['prix'];
                $kwhTotal += $kwhPartiels;
                $montantRestant = 0;
            }

            $trancheUtilisee = $tranche['nom'];
            $prixUnitaire = $tranche['prix'];
        }

        return [
            'nbre_kwt' => round($kwhTotal, 2),
            'tranche' => $trancheUtilisee,
            'prix_unitaire' => $prixUnitaire
        ];
    }

    private function genererCodeRecharge(): string {
        return 'WYF' . str_pad(mt_rand(1, 999999999999), 12, '0', STR_PAD_LEFT);
    }

    private function genererReference(): string {
        return 'REF' . date('YmdHis') . mt_rand(1000, 9999);
    }

    private function logTransaction(array $logData, string $messageErreur = null): void {
        $logData['message_erreur'] = $messageErreur;
        
        $log = new LogAchat(
            null,
            $logData['date_heure'],
            $logData['localisation'],
            $logData['adresse_ip'],
            $logData['statut'],
            $logData['numero_compteur'],
            $logData['code_recharge'],
            $logData['nbre_kwt'],
            $logData['message_erreur']
        );

        $this->logAchatRepository->create($log);
    }
}