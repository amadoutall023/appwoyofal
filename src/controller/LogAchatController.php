<?php
namespace App\Controller;

use App\Core\Abstract\AbstractController;
use App\Service\LogAchatService;
use App\Core\ApiResponse;

class LogAchatController extends AbstractController {
    private LogAchatService $logAchatService;

    public function __construct(LogAchatService $logAchatService) {
        parent::__construct();
        $this->logAchatService = $logAchatService;
    }

    public function index() {
        // Retourner la liste des logs (à implémenter si nécessaire)
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

            $response = $this->logAchatService->createLog($input);
            $response->send();
        } catch (\Exception $e) {
            ApiResponse::error("Erreur serveur: " . $e->getMessage(), 500)->send();
        }
    }

    public function show() {
        // Afficher les détails d'un log spécifique
        ApiResponse::error("Méthode non implémentée", 501)->send();
    }

    public function edit() {
        // Affichage du formulaire d'édition (pour interface web)
        ApiResponse::error("Méthode non implémentée", 501)->send();
    }

    /**
     * Endpoint pour consulter les logs par période
     */
    public function consulter() {
        try {
            $dateDebut = $_GET['date_debut'] ?? null;
            $dateFin = $_GET['date_fin'] ?? null;
            $statut = $_GET['statut'] ?? null;

            // À implémenter: logique de consultation des logs
            ApiResponse::error("Fonctionnalité en cours de développement", 501)->send();
        } catch (\Exception $e) {
            ApiResponse::error("Erreur serveur: " . $e->getMessage(), 500)->send();
        }
    }

    /**
     * Endpoint pour les statistiques des transactions
     */
    public function statistiques() {
        try {
            // À implémenter: calcul des statistiques
            ApiResponse::error("Fonctionnalité en cours de développement", 501)->send();
        } catch (\Exception $e) {
            ApiResponse::error("Erreur serveur: " . $e->getMessage(), 500)->send();
        }
    }
}