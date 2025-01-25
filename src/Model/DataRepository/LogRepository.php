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
}
