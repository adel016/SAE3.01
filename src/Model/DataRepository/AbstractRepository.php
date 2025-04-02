<?php

namespace App\Meteo\Model\DataRepository;

use App\Meteo\Model\DataRepository\DatabaseConnection;
use App\Meteo\Model\DataObject\Utilisateur;
use App\Meteo\Model\DataObject\Logs;
use PDO;
use PDOException;

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

    public function getAllMeteotheques($userId) {
        $pdo = DatabaseConnection::getPdo();
        $stmt = $pdo->prepare("SELECT nom_collection AS nom, description, date_creation AS date FROM " . $this->getNomTable() . " WHERE utilisateur_id = :userId");
        $stmt->execute([':userId' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Methode specifique pour recuperer la meteotheque d'un utilisateur (unique methode)
    public function getAllUtilisateurs() {
        $pdo = DatabaseConnection::getPdo();
        $stmt = $pdo->query("SELECT DISTINCT u.utilisateur_id AS id, u.nom AS nom, u.prenom AS prenom FROM meteotheques m JOIN utilisateurs u ON m.utilisateur_id = u.utilisateur_id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function saveStationData(string $stationId, string $details): bool {
        // Récupérer l'ID de l'utilisateur courant
        $utilisateurId = $_SESSION['utilisateur_id'] ?? null;

        if (!$utilisateurId) {
            error_log("Utilisateur non authentifié.");
            return false;
        }

        // Nom de la collection par défaut
        $nomCollection = "Favoris";

        try {
            $pdo = DatabaseConnection::getPdo();

            // 1. Vérifier si une météothèque existe déjà pour cet utilisateur et cette collection
            $queryCheck = "SELECT meteo_id FROM meteotheques WHERE utilisateur_id = :utilisateur_id AND nom_collection = :nom_collection";
            $stmtCheck = $pdo->prepare($queryCheck);
            $stmtCheck->execute([
                'utilisateur_id' => $utilisateurId,
                'nom_collection' => $nomCollection,
            ]);
            $existingMeteo = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if ($existingMeteo) {
                // Météothèque existante, mettre à jour la description (ajouter la station)
                $meteoId = $existingMeteo['meteo_id'];
                $currentDescription = $this->getDescription($meteoId);  // Récupérer la description actuelle

                // Ajouter les détails de la nouvelle station
                $newDescription = $currentDescription . "; " . $stationId . ": " . $details;

                $queryUpdate = "UPDATE meteotheques SET description = :description WHERE meteo_id = :meteo_id";
                $stmtUpdate = $pdo->prepare($queryUpdate);
                $stmtUpdate->execute([
                    'description' => $newDescription,
                    'meteo_id' => $meteoId,
                ]);

                error_log("Météothèque existante mise à jour : " . $meteoId);
                return true;
            } else {
                // 2. Créer une nouvelle météothèque si elle n'existe pas
                $dateCreation = date('Y-m-d H:i:s');
                $queryInsert = "INSERT INTO meteotheques (utilisateur_id, nom_collection, description, date_creation) VALUES (:utilisateur_id, :nom_collection, :description, :date_creation)";
                $stmtInsert = $pdo->prepare($queryInsert);
                $stmtInsert->execute([
                    'utilisateur_id' => $utilisateurId,
                    'nom_collection' => $nomCollection,
                    'description' => $stationId . ": " . $details,
                    'date_creation' => $dateCreation,
                ]);

                error_log("Nouvelle météothèque créée pour l'utilisateur : " . $utilisateurId);
                return true;
            }

        } catch (PDOException $e) {
            error_log("Erreur lors de l'enregistrement de la station : " . $e->getMessage());
            return false;
        }
    }

    private function getDescription(int $meteoId): string {
        try {
            $pdo = DatabaseConnection::getPdo();
            $query = "SELECT description FROM meteotheques WHERE meteo_id = :meteo_id";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['meteo_id' => $meteoId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result ? $result['description'] : '';
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération de la description : " . $e->getMessage());
            return '';
        }
    }
}

?>