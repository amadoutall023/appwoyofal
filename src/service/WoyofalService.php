<?php
// namespace App\Services;

// use App\Models\Achat;
// use App\Models\LogAchat;
// use App\Repositories\CompteurRepository;
// use App\Repositories\ClientRepository;
// use App\Repositories\AchatRepository;
// use App\Repositories\LogAchatRepository;

// class WoyofalService {
//     private CompteurRepository $compteurRepository;
//     private ClientRepository $clientRepository;
//     private AchatRepository $achatRepository;
//     private LogAchatRepository $logRepository;
//     private TrancheService $trancheService;

//     // Tarifs par tranche (en FCFA par kWh)
//     private const TARIFS = [
//         'T1' => 79,   // 0 à 150 kWh
//         'T2' => 94,   // 151 à 250 kWh
//         'T3' => 99,   // 251 à 500 kWh
//         'T4' => 109   // > 500 kWh
//     ];

//     public function __construct(
//         CompteurRepository $compteurRepository,
//         ClientRepository $clientRepository,
//         AchatRepository $achatRepository,
//         LogAchatRepository $logRepository,
//         TrancheService $trancheService
//     ) {
//         $this->compteurRepository = $compteurRepository;
//         $this->clientRepository = $clientRepository;
//         $this->achatRepository = $achatRepository;
//         $this->logRepository = $logRepository;
//         $this->trancheService = $trancheService;
//     }

//     public function acheterCredit(string $numeroCompteur, float $montant, string $adresseIp): array {
//         try {
//             // 1. Vérifier l'existence du compteur
//             $compteur = $this->compteurRepository->findByNumero($numeroCompteur);
//             if (!$compteur) {
//                 $this->loggerTentative($numeroCompteur, $adresseIp, 'Échec', 'Compteur non trouvé');
//                 return [
//                     'data' => null,
//                     'statut' => 'error',
//                     'code' => 404,
//                     'message' => 'Le numéro de compteur non retrouvé'
//                 ];
//             }

//             // 2. Récupérer les informations du client
//             $client = $this->clientRepository->findById($compteur->getClientId());
//             if (!$client) {
//                 $this->loggerTentative($numeroCompteur, $adresseIp, 'Échec', 'Client non trouvé');
//                 return [
//                     'data' => null,
//                     'statut' => 'error',
//                     'code' => 404,
//                     'message' => 'Client associé au compteur non trouvé'
//                 ];
//             }

//             // 3. Calculer la tranche et le nombre de kWh
//             $consommationMoisCourant = $this->achatRepository->getConsommationMoisCourant($client->getId());
//             $resultCalcul = $this->trancheService->calculerKwhEtTranche($montant, $consommationMoisCourant);

//             // 4. Générer la référence et le code de recharge
//             $reference = $this->genererReference();
//             $codeRecharge = $this->genererCodeRecharge();

//             // 5. Créer l'achat
//             $achat = new Achat(
//                 0, // L'ID sera généré par la base
//                 $reference,
//                 $numeroCompteur,
//                 $codeRecharge,
//                 $montant,
//                 $resultCalcul['kwh'],
//                 $resultCalcul['tranche'],
//                 $resultCalcul['prix_unitaire'],
//                 $client->getId()
//             );

//             // 6. Enregistrer l'achat
//             if (!$this->achatRepository->create($achat)) {
//                 $this->loggerTentative($numeroCompteur, $adresseIp, 'Échec', 'Erreur lors de l\'enregistrement');
//                 return [
//                     'data' => null,
//                     'statut' => 'error',
//                     'code' => 500,
//                     'message' => 'Erreur lors de l\'enregistrement de l\'achat'
//                 ];
//             }

//             // 7. Logger le succès
//             $this->loggerTentative($numeroCompteur, $adresseIp, 'Success', '', $codeRecharge, $resultCalcul['kwh']);

//             // 8. Retourner la réponse de succès
//             return [
//                 'data' => [
//                     'compteur' => $numeroCompteur,
//                     'reference' => $reference,
//                     'code' => $codeRecharge,
//                     'date' => $achat->getDateAchat()->format('Y-m-d H:i:s'),
//                     'tranche' => $resultCalcul['tranche'],
//                     'prix' => $resultCalcul['prix_unitaire'],
//                     'nbreKwt' => $resultCalcul['kwh'],
//                     'client' => $client->getNomComplet()
//                 ],
//                 'statut' => 'success',
//                 'code' => 200,
//                 'message' => 'Achat effectué avec succès'
//             ];

//         } catch (\Exception $e) {
//             $this->loggerTentative($numeroCompteur, $adresseIp, 'Échec', $e->getMessage());
//             return [
//                 'data' => null,
//                 'statut' => 'error',
//                 'code' => 500,
//                 'message' => 'Erreur interne du serveur'
//             ];
//         }
//     }

//     private function genererReference(): string {
//         return 'WYF' . date('YmdHis') . rand(1000, 9999);
//     }

//     private function genererCodeRecharge(): string {
//         return implode('-', [
//             rand(1000, 9999),
//             rand(1000, 9999),
//             rand(1000, 9999),
//             rand(1000, 9999)
//         ]);
//     }

//     private function loggerTentative(
//         string $numeroCompteur, 
//         string $adresseIp, 
//         string $statut, 
//         string $messageErreur = '',
//         ?string $codeRecharge = null,
//         ?float $nbreKwt = null
//     ): void {
//         $log = new LogAchat(
//             0,
//             'Dakar, Sénégal', // Localisation par défaut
//             $adresseIp,
//             $statut,
//             $numeroCompteur,
//             $codeRecharge,
//             $nbreKwt,
//             $messageErreur
//         );

//         $this->logRepository->create($log);
//     }
// }