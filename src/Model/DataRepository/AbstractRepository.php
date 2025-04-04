<?php

namespace App\Meteo\Model\DataRepository;

use App\Meteo\Model\DataRepository\DatabaseConnection;
use App\Meteo\Model\DataObject\Utilisateur;
use App\Meteo\Model\DataObject\Meteotheques;
use App\Meteo\Model\DataObject\Logs;
use PDO;

abstract class AbstractRepository {
    // Méthode abstraite pour retourner le nom des colonnes d'une table
    abstract protected function getNomsColonnes(): array;

    // Méthode abstraite pour retourner le nom de la table
    abstract protected function getNomTable(): string;

    // Méthode abstraite pour retourner la clé primaire
    abstract protected function getPrimaryKey(): string;

    // Méthode abstraite pour construire un objet depuis un tableau
    abstract protected function construire(array $objetFormatTableau): object;

    // Méthode générique pour récupérer tous les objets
    public function getAll(): array {
        $pdo = DatabaseConnection::getPdo();
        $sql = "SELECT * FROM " . $this->getNomTable();
        $stmt = $pdo->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([$this, 'construire'], $result);
    }

    // Méthode générique pour récupérer un objet par clé primaire
    public function select(string $id): ?object {
        $pdo = DatabaseConnection::getPdo();
        $sql = "SELECT * FROM " . $this->getNomTable() . " WHERE " . $this->getPrimaryKey() . " = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $this->construire($result) : null;
    }  

    // Méthode générique pour supprimer un objet par clé primaire
    public function delete(string $id): bool {
        $pdo = DatabaseConnection::getPdo();
        $sql = "DELETE FROM " . $this->getNomTable() . " WHERE " . $this->getPrimaryKey() . " = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    // Methode generique pour creer un objet par le noms des colonnes
    public function sauvegarder(object $object): bool {
        $pdo = DatabaseConnection::getPdo();
        $colonnes = $this->getNomsColonnes();
        $parametres = array_map(fn($col) => ":$col", $colonnes);
        $sql = "INSERT INTO " . $this->getNomTable() . " (" . implode(", ", $colonnes) . ") VALUES (" . implode(", ", $parametres) . ")";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($object->formatTableau());
    }  

    // Methode generique pour modifier un objet par le nom des colonnes
    public function update(object $object): bool {
        $pdo = DatabaseConnection::getPdo();
        $colonnesMaj = array_map(fn($col) => "$col = :$col", $this->getNomsColonnes());
        $sql = "UPDATE " . $this->getNomTable() . " SET " . implode(", ", $colonnesMaj) . " WHERE " . $this->getPrimaryKey() . " = :" . $this->getPrimaryKey();
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($object->formatTableau());
    }

    ////////////// LOGS //////////////
    public function countByAction($action, $dateDebut = null, $dateFin = null) {
        $sql = "SELECT COUNT(*) FROM logs WHERE action = :action";
        if ($dateDebut && $dateFin) {
            $sql .= " AND DATE(timestamp) BETWEEN :dateDebut AND :dateFin";
        }
        $pdo = DatabaseConnection::getPdo();
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':action', $action);
        if ($dateDebut && $dateFin) {
            $stmt->bindValue(':dateDebut', $dateDebut);
            $stmt->bindValue(':dateFin', $dateFin);
        }
        $stmt->execute();
        return $stmt->fetchColumn();
    }  

    public function addLog($utilisateurId, $action, $description = null) {
        $query = "INSERT INTO logs (utilisateur_id, action, description, timestamp) VALUES (:utilisateur_id, :action, :description, NOW())";
        $stmt = DatabaseConnection::getPdo()->prepare($query);
        $stmt->execute([':utilisateur_id' => $utilisateurId, ':action' => $action, ':description' => $description]);
    }

    public function getActionsParJour($action, $dateDebut = null, $dateFin = null) {
        $sql = "SELECT DATE(timestamp) as jour, COUNT(*) as total FROM logs WHERE action = :action";
        if ($dateDebut && $dateFin) {
            $sql .= " AND DATE(timestamp) BETWEEN :dateDebut AND :dateFin";
        }
        $sql .= " GROUP BY DATE(timestamp)";
        $pdo = DatabaseConnection::getPdo();
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':action', $action);
        if ($dateDebut && $dateFin) {
            $stmt->bindValue(':dateDebut', $dateDebut);
            $stmt->bindValue(':dateFin', $dateFin);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }  

    public function getLogsByDate($dateDebut, $dateFin) {
        $sql = "SELECT * FROM logs WHERE DATE(timestamp) BETWEEN :dateDebut AND :dateFin ORDER BY timestamp DESC";
        $pdo = DatabaseConnection::getPdo();   
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':dateDebut' => $dateDebut, ':dateFin' => $dateFin]);
        $logsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($log) => new Logs($log['log_id'], $log['user_id'] ?? null, $log['action'], $log['timestamp'], $log['description'] ?? ''), $logsData);
    }

    ////////////// UTILISATEUR / STATIONS / METEOTHEQUES ///////////////////
    // Methode specifique pour recuperer un utilisateur avec son email (unique methode)
    public function selectByEmail(string $email): ?Utilisateur {
        $pdo = DatabaseConnection::getPdo();
        $stmt = $pdo->prepare("SELECT * FROM Utilisateurs WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $data ? new Utilisateur($data['utilisateur_id'], $data['nom'], $data['prenom'], $data['email'], $data['mot_de_passe'], $data['date_creation'], $data['role'], $data['etat_compte']) : null;
    }
          
    // Methode specifique pour recuperer une STATION avec sa region (unique methode)
    public function selectByReg(string $region): array {
        $pdo = DatabaseConnection::getPdo();
        $stmt = $pdo->prepare("SELECT * FROM stations WHERE region = :region");
        $stmt->execute([':region' => $region]);
        return array_map([$this, 'construire'], $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    // Methode specifique pour recuperer la meteotheque d'un utilisateur (unique methode)
    public function findByUserId(int $userId): array {
        $pdo = DatabaseConnection::getPdo();
        $stmt = $pdo->prepare("SELECT * FROM " . $this->getNomTable() . " WHERE utilisateur_id = :userId ORDER BY date_creation DESC");
        $stmt->execute([':userId' => $userId]);
        return array_map([$this, 'construire'], $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getAllMeteotheques($userId): array {
        $sql = "SELECT * FROM " . $this->getNomTable() ." WHERE utilisateur_id = :userId";
        $stmt = DatabaseConnection::getPDO()->prepare($sql);
        $stmt->execute(['userId' => $userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $meteotheques = [];
        foreach ($rows as $row) {
            $meteotheques[] = new Meteotheques(
                $row['meteo_id'],
                $row['utilisateur_id'],
                $row['nom_collection'],
                $row['description'],
                $row['date_creation']
            );
        }
    
        return $meteotheques;
    }
       

    // Methode specifique pour recuperer la meteotheque d'un utilisateur (unique methode)
    public function getAllUtilisateurs() {
        $pdo = DatabaseConnection::getPdo();
        $stmt = $pdo->query("SELECT DISTINCT u.utilisateur_id AS id, u.nom AS nom, u.prenom AS prenom FROM meteotheques m JOIN utilisateurs u ON m.utilisateur_id = u.utilisateur_id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>