<?php
namespace App\Repository;
use App\Entity\LogAchat;
class LogAchatRepository implements ILogAchatRepository{
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function create(LogAchat $log): bool {
        $data = $log->toArray();
        $stmt = $this->pdo->prepare("
            INSERT INTO logs_achats (date_heure, localisation, adresse_ip, statut, 
                                   numero_compteur, code_recharge, nbre_kwt, message_erreur) 
            VALUES (:date_heure, :localisation, :adresse_ip, :statut, 
                    :numero_compteur, :code_recharge, :nbre_kwt, :message_erreur)
        ");
        
        return $stmt->execute([
            'date_heure' => $data['date_heure'],
            'localisation' => $data['localisation'],
            'adresse_ip' => $data['adresse_ip'],
            'statut' => $data['statut'],
            'numero_compteur' => $data['numero_compteur'],
            'code_recharge' => $data['code_recharge'],
            'nbre_kwt' => $data['nbre_kwt'],
            'message_erreur' => $data['message_erreur']
        ]);
    }
}