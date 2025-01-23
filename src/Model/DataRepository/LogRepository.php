<?php

namespace App\Meteo\Model\DataRepository;

use App\Meteo\Model\DataObject\Logs;
use App\Meteo\Config\Conf;
use PDO;

class LogRepository extends AbstractRepository {
    protected function getNomTable(): string {
        return "Logs";
    }

    protected function getPrimaryKey(): string {
        return "log_id";
    }

    protected function getNomsColonnes(): array {
        return ['log_id', 'utilisateur_id', 'action', 'timestamp'];
    }
    
    // Méthode pour construire un objet Log à partir d'un tableau de données
    protected function construire(array $data): Logs {
        return new Logs(
            $data['log_id'],
            $data['utilisateur_id'],
            $data['action'],
            $data['timestamp']
        );
    }

    public function countByAction($action) {
        $query = "SELECT COUNT(*) as count FROM " . $this->getNomTable() . " WHERE action = :action";
        $stmt = Conf::getPDO()->prepare($query);
        $stmt->bindParam(':action', $action);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    public function addLog($utilisateurId, $action) {
        $query = "INSERT INTO " . $this->getNomTable() . " (utilisateur_id, action, timestamp) VALUES (:utilisateur_id, :action, NOW())";
        $stmt = Conf::getPDO()->prepare($query);
        $stmt->bindParam(':utilisateur_id', $utilisateurId);
        $stmt->bindParam(':action', $action);
        $stmt->execute();
    }

    public function getActionsParJour($action) {
        $query = "
            SELECT DAYOFWEEK(timestamp) AS jour, COUNT(*) AS count
            FROM " . $this->getNomTable() . "
            WHERE action = :action
            GROUP BY jour
        ";
        $stmt = Conf::getPDO()->prepare($query);
        $stmt->bindParam(':action', $action);
        $stmt->execute();
    
        // Initialiser un tableau pour les jours de la semaine
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $actionsParJour = array_fill(0, 7, 0); // Dimanche = 1, Samedi = 7
    
        foreach ($result as $row) {
            $jourIndex = $row['jour'] - 1; // Convertir pour commencer à 0
            $actionsParJour[$jourIndex] = (int) $row['count'];
        }
    
        return $actionsParJour;
    }    
    
}
