<?php
namespace App\Controller;

use App\Core\Abstract\AbstractController;
use App\Service\CompteurService;
use App\Core\ApiResponse;

class CompteurController extends AbstractController {
    private CompteurService $compteurService;

    public function __construct(CompteurService $compteurService) {
        parent::__construct();
        $this->compteurService = $compteurService;
    }

    public function index() {
        // Retourner la liste des compteurs (à implémenter si nécessaire)
        ApiResponse::error("Méthode non implémentée", 501)->send();
    }

    public function create() {
        // Affichage du formulaire de création (pour interface web)
        ApiResponse::error("Méthode non implémentée", 501)->send();
    }

    public function store() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                ApiResponse::error("Données JSON invalides", 400)->send();
                return;
            }

            $response = $this->compteurService->createCompteur($input);
            $response->send();
        } catch (\Exception $e) {
            ApiResponse::error("Erreur serveur: " . $e->getMessage(), 500)->send();
        }
    }

    public function show() {
        try {
            $numero = $_GET['numero'] ?? null;
            
            if (!$numero) {
                ApiResponse::error("Numéro de compteur requis", 400)->send();
                return;
            }

            $response = $this->compteurService->getCompteurByNumero($numero);
            $response->send();
        } catch (\Exception $e) {
            ApiResponse::error("Erreur serveur: " . $e->getMessage(), 500)->send();
        }
    }

    public function edit() {
        // Affichage du formulaire d'édition (pour interface web)
        ApiResponse::error("Méthode non implémentée", 501)->send();
    }

    public function verifier() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['numero_compteur'])) {
                ApiResponse::error("Numéro de compteur requis", 400)->send();
                return;
            }

            $response = $this->compteurService->getCompteurByNumero($input['numero_compteur']);
            $response->send();
        } catch (\Exception $e) {
            ApiResponse::error("Erreur serveur: " . $e->getMessage(), 500)->send();
        }
    }
}