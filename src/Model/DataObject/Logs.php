<?php 

namespace App\Meteo\Model\DataObject;

class Logs {
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

    // Implémentation de la méthode formatTableau
    public function formatTableau(): array {
        return [
            'logId' => $this->logId,
            'utilisateurId' => $this->utilisateurId,
            'action' => $this->action,
            'timestamp' => $this->timestamp
        ];
    }

    // Getters
    public function getLogId() : int {
        return $this->logId;
    }

    public function getUtilisateurId() : int {
        return $this->utilisateurId;
    }

    public function getAction() : string {
        return $this->action;
    }

    public function getTimestamp() : string {
        return $this->timestamp;
    }
}
?>
