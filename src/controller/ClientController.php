<?php
namespace App\Controller;

use App\Core\Abstract\AbstractController;
use App\Service\ClientService;
use App\Core\ApiResponse;

class ClientController extends AbstractController {
    private ClientService $clientService;

    public function __construct(ClientService $clientService) {
        parent::__construct();
        $this->clientService = $clientService;
    }

    public function index() {
        // Retourner la liste des clients (à implémenter si nécessaire)
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

            $response = $this->clientService->createClient($input);
            $response->send();
        } catch (\Exception $e) {
            ApiResponse::error("Erreur serveur: " . $e->getMessage(), 500)->send();
        }
    }

    public function show() {
        try {
            $id = $_GET['id'] ?? null;
            
            if (!$id || !is_numeric($id)) {
                ApiResponse::error("ID client requis et doit être numérique", 400)->send();
                return;
            }

            $response = $this->clientService->getClientById((int)$id);
            $response->send();
        } catch (\Exception $e) {
            ApiResponse::error("Erreur serveur: " . $e->getMessage(), 500)->send();
        }
    }

    public function edit() {
        // Affichage du formulaire d'édition (pour interface web)
        ApiResponse::error("Méthode non implémentée", 501)->send();
    }
}
