<?php
namespace App\Controller;

use App\Core\Abstract\AbstractController;
use App\Service\AchatService;
use App\Core\ApiResponse;

class AchatController extends AbstractController {
    private AchatService $achatService;

    public function __construct(AchatService $achatService) {
        parent::__construct();
        $this->achatService = $achatService;
    }

    public function index() {
        // Retourner la liste des achats (à implémenter si nécessaire)
        ApiResponse::error("Méthode non implémentée", 501)->send();
    }

    public function create() {
        // Affichage du formulaire d'achat (pour interface web)
        ApiResponse::error("Méthode non implémentée", 501)->send();
    }

    public function store() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                ApiResponse::error("Données JSON invalides", 400)->send();
                return;
            }

            // Validation des champs requis
            if (!isset($input['numero_compteur']) || !isset($input['montant'])) {
                ApiResponse::error("Le numéro de compteur et le montant sont obligatoires", 400)->send();
                return;
            }

            $numeroCompteur = $input['numero_compteur'];
            $montant = (float)$input['montant'];

            $response = $this->achatService->acheterCredit($numeroCompteur, $montant);
            $response->send();
        } catch (\Exception $e) {
            ApiResponse::error("Erreur serveur: " . $e->getMessage(), 500)->send();
        }
    }

    public function show() {
        // Afficher les détails d'un achat spécifique
        ApiResponse::error("Méthode non implémentée", 501)->send();
    }

    public function edit() {
        // Affichage du formulaire d'édition (pour interface web)
        ApiResponse::error("Méthode non implémentée", 501)->send();
    }

    /**
     * Endpoint principal pour l'achat de crédit Woyofal
     */
    public function acheter() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                ApiResponse::error("Données JSON invalides", 400)->send();
                return;
            }

            // Validation des champs requis
            if (empty($input['numero_compteur'])) {
                ApiResponse::error("Le numéro de compteur est obligatoire", 400)->send();
                return;
            }

            if (!isset($input['montant']) || $input['montant'] <= 0) {
                ApiResponse::error("Le montant est obligatoire et doit être supérieur à zéro", 400)->send();
                return;
            }

            $numeroCompteur = trim($input['numero_compteur']);
            $montant = (float)$input['montant'];

            $response = $this->achatService->acheterCredit($numeroCompteur, $montant);
            $response->send();
        } catch (\Exception $e) {
            ApiResponse::error("Erreur serveur: " . $e->getMessage(), 500)->send();
        }
    }

    /**
     * Endpoint pour obtenir l'historique des achats d'un compteur
     */
    public function historique() {
        try {
            $numeroCompteur = $_GET['numero_compteur'] ?? null;
            
            if (!$numeroCompteur) {
                ApiResponse::error("Numéro de compteur requis", 400)->send();
                return;
            }

            // À implémenter: récupérer l'historique des achats
            ApiResponse::error("Fonctionnalité en cours de développement", 501)->send();
        } catch (\Exception $e) {
            ApiResponse::error("Erreur serveur: " . $e->getMessage(), 500)->send();
        }
    }
}