<?php

namespace App\Meteo\Model;

use App\Meteo\Model\Model;
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

    public function modifier() : bool {
        try {
            $sql = "UPDATE Logs
                    SET utilisateur_id = :utilisateur_id,
                        action = :action,
                        timestamp = :timestamp";
            
            $pdoStatement = Model::getPdo()->prepare($sql);
            $values = array(
                'utilisateurId' => $this->utilisateurId,
                'action' => $this->action,
                'timestamp' => $this->timestamp
            );

            $pdoStatement->execute($values);
            // Retourne true si la modification a ete effectue
            return $pdoStatement->rowCount() > 0;
        } catch (PDOException $e) {
            echo "Erreur lors de la modification : " . $e->getMessage();
            return false;
        }

    }

    public function delete() : bool {
        try {
            $sql = "DELETE FROM Logs WHERE log_id = :log_id";

            $pdoStatement = Model::getPdo()->prepare($sql);

            $values = array(
                'log_id' => $this->logId
            );

            $pdoStatement->execute($values);
            return $pdoStatement->rowCount() > 0;

        } catch (PDOException $e) {
            echo "Erreur lors de la suppression : " . $e->getMessage();
            return false;
        }
    }
}
?>
