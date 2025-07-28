<?php
namespace App\Service;

use App\Entity\Compteur;
use App\Repository\ICompteurRepository;
use App\Core\ApiResponse;

class CompteurService {
    private ICompteurRepository $compteurRepository;

    public function __construct(ICompteurRepository $compteurRepository) {
        $this->compteurRepository = $compteurRepository;
    }

    public function getCompteurByNumero(string $numeroCompteur): ApiResponse {
        try {
            $compteur = $this->compteurRepository->findByNumero($numeroCompteur);
            
            if (!$compteur) {
                return ApiResponse::error("Le numéro de compteur non retrouvé", 404);
            }

            return ApiResponse::success($compteur->toArray(), "Compteur trouvé avec succès");
        } catch (\Exception $e) {
            return ApiResponse::error("Erreur lors de la vérification du compteur: " . $e->getMessage(), 500);
        }
    }

    public function createCompteur(array $data): ApiResponse {
        try {
            // Validation des données
            if (empty($data['numero_compteur']) || empty($data['client_id'])) {
                return ApiResponse::error("Le numéro de compteur et l'ID client sont obligatoires", 400);
            }

            // Vérifier si le compteur existe déjà
            $existingCompteur = $this->compteurRepository->findByNumero($data['numero_compteur']);
            if ($existingCompteur) {
                return ApiResponse::error("Ce numéro de compteur existe déjà", 409);
            }

            $compteur = new Compteur(
                null,
                $data['numero_compteur'],
                $data['client_id'],
                'actif',
                date('Y-m-d H:i:s')
            );

            $created = $this->compteurRepository->create($compteur);
            
            if (!$created) {
                return ApiResponse::error("Erreur lors de la création du compteur", 500);
            }

            return ApiResponse::success($compteur->toArray(), "Compteur créé avec succès", 201);
        } catch (\Exception $e) {
            return ApiResponse::error("Erreur lors de la création du compteur: " . $e->getMessage(), 500);
        }
    }
}