<?php
namespace App\Service;

use App\Entity\LogAchat;
use App\Repository\ILogAchatRepository;
use App\Core\ApiResponse;

class LogAchatService {
    private ILogAchatRepository $logAchatRepository;

    public function __construct(ILogAchatRepository $logAchatRepository) {
        $this->logAchatRepository = $logAchatRepository;
    }

    public function createLog(array $data): ApiResponse {
        try {
            $log = new LogAchat(
                null,
                $data['date_heure'] ?? date('Y-m-d H:i:s'),
                $data['localisation'] ?? 'Non spécifié',
                $data['adresse_ip'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                $data['statut'],
                $data['numero_compteur'],
                $data['code_recharge'] ?? null,
                $data['nbre_kwt'] ?? 0,
                $data['message_erreur'] ?? null
            );

            $created = $this->logAchatRepository->create($log);
            
            if (!$created) {
                return ApiResponse::error("Erreur lors de l'enregistrement du log", 500);
            }

            return ApiResponse::success($log->toArray(), "Log enregistré avec succès", 201);
        } catch (\Exception $e) {
            return ApiResponse::error("Erreur lors de la création du log: " . $e->getMessage(), 500);
        }
    }
}