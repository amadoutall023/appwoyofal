<?php
// namespace App\Controllers;

// use App\Services\WoyofalService;
// use App\Validators\WoyofalValidator;
// use App\Core\Controller;

// class WoyofalController {
//     private WoyofalService $woyofalService;

//     public function __construct(WoyofalService $woyofalService) {
//         $this->woyofalService = $woyofalService;
//     }

//     public function acheterCredit(): void {
//         header('Content-Type: application/json');
        
//         try {
//             // Récupérer les données JSON
//             $input = json_decode(file_get_contents('php://input'), true);
            
//             // Validation des données d'entrée
//             if (!isset($input['numeroCompteur']) || !isset($input['montant'])) {
//                 echo json_encode([
//                     'data' => null,
//                     'statut' => 'error',
//                     'code' => 400,
//                     'message' => 'Numéro de compteur et montant sont obligatoires'
//                 ]);
//                 return;
//             }

//             $numeroCompteur = trim($input['numeroCompteur']);
//             $montant = (float) $input['montant'];

//             // Validation du montant
//             if ($montant <= 0) {
//                 echo json_encode([
//                     'data' => null,
//                     'statut' => 'error',
//                     'code' => 400,
//                     'message' => 'Le montant doit être supérieur à zéro'
//                 ]);
//                 return;
//             }

//             // Récupérer l'adresse IP
//             $adresseIp = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

//             // Traiter l'achat
//             $resultat = $this->woyofalService->acheterCredit($numeroCompteur, $montant, $adresseIp);
            
//             // Définir le code de statut HTTP
//             http_response_code($resultat['code']);
            
//             echo json_encode($resultat);

//         } catch (\Exception $e) {
//             http_response_code(500);
//             echo json_encode([
//                 'data' => null,
//                 'statut' => 'error',
//                 'code' => 500,
//                 'message' => 'Erreur interne du serveur'
//             ]);
//         }
//     }
// }