<?php

namespace App\Covoiturage\Model;

use App\Covoiturage\Model\Model;
use \PDOException;

class ModelLog {
    private int $logId;
    private int $utilisateurId;
    private string $action;
    private string $timestamp;

    public function __construct(int $logId, int $utilisateurId, string $action, string $timestamp) {
        $this->logId = $logId;
        $this->utilisateurId = $utilisateurId;
        $this->action = $action;
        $this->timestamp = $timestamp;
    }

    public static function construire(array $logFormatTableau) : ModelLog {
        return new ModelLog(
            $logFormatTableau['log_id'],
            $logFormatTableau['utilisateur_id'],
            $logFormatTableau['action'],
            $logFormatTableau['timestamp']
        );
    }

    public static function getLogs() {
        $pdo = Model::getPdo();
        $sql = "SELECT * FROM Logs";
        $pdoStatement = $pdo->query($sql);

        $logs = [];
        foreach ($pdoStatement as $logFormatTableau) {
            $logs[] = ModelLog::construire($logFormatTableau);
        }
        return $logs;
    }

    public function sauvegarder() : bool {
        try {
            $sql = "INSERT INTO Logs (utilisateur_id, action, timestamp) VALUES (:utilisateurId, :action, :timestamp)";
            $pdoStatement = Model::getPdo()->prepare($sql);
            $values = array(
                'utilisateurId' => $this->utilisateurId,
                'action' => $this->action,
                'timestamp' => $this->timestamp
            );
            $pdoStatement->execute($values);
            return true;
        } catch (PDOException $e) {
            echo "Erreur lors de la sauvegarde : " . $e->getMessage();
            return false;
        }
    }
}
?>
