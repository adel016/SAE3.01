<?php 

namespace App\Meteo\Model\DataObject;

class Logs {
    public int $log_id;
    public ?int $user_id;
    public string $action;
    public string $description;
    public string $timestamp;

    public function __construct(int $log_id, ?int $user_id, string $action, string $timestamp, string $description = '') {
        $this->log_id = $log_id;
        $this->user_id = $user_id ?? 0; // Use null coalescing operator to set a default value
        $this->action = $action;
        $this->timestamp = $timestamp;
        $this->description = $description;
    }
    

    public function formatTableau(): array {
        return [
            'logId' => $this->log_id,
            'utilisateurId' => $this->user_id,
            'action' => $this->action,
            'description' => $this->description,
            'timestamp' => $this->timestamp
        ];
    }
    
    public function getLogId(): int {
        return $this->log_id;
    }
    
    public function getUtilisateurId(): ?int {
        return $this->user_id;
    }

    public function getAction(): string {
        return $this->action;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getTimestamp(): string {
        return $this->timestamp;
    }
}
?>
