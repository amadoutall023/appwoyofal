<?php
namespace App\Service;

use App\Entity\Client;
use App\Repository\IClientRepository;
use App\Core\ApiResponse;

class ClientService {
    private IClientRepository $clientRepository;

    public function __construct(IClientRepository $clientRepository) {
        $this->clientRepository = $clientRepository;
    }

    public function getClientById(int $id): ApiResponse {
        try {
            $client = $this->clientRepository->findById($id);
            
            if (!$client) {
                return ApiResponse::error("Client non trouvé", 404);
            }

            return ApiResponse::success($client->toArray(), "Client trouvé avec succès");
        } catch (\Exception $e) {
            return ApiResponse::error("Erreur lors de la récupération du client: " . $e->getMessage(), 500);
        }
    }

    public function createClient(array $data): ApiResponse {
        try {
            // Validation des données
            if (empty($data['nom']) || empty($data['prenom']) || empty($data['telephone'])) {
                return ApiResponse::error("Les champs nom, prénom et téléphone sont obligatoires", 400);
            }

            $client = new Client(
                null,
                $data['nom'],
                $data['prenom'],
                $data['telephone'],
                $data['email'] ?? null,
                date('Y-m-d H:i:s')
            );

            $created = $this->clientRepository->create($client);
            
            if (!$created) {
                return ApiResponse::error("Erreur lors de la création du client", 500);
            }

            return ApiResponse::success($client->toArray(), "Client créé avec succès", 201);
        } catch (\Exception $e) {
            return ApiResponse::error("Erreur lors de la création du client: " . $e->getMessage(), 500);
        }
    }
}
