<?php

namespace App\Meteo\Model\DataRepository;

use App\Meteo\Model\DataRepository\DatabaseConnection;
use App\Meteo\Model\DataObject\Utilisateur;
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
        $pdoStatement = $pdo->query($sql);
        $result = $pdoStatement->fetchAll(PDO::FETCH_ASSOC);

        $objets = [];
        foreach ($result as $objetFormatTableau) {
            $objets[] = $this->construire($objetFormatTableau);
        }
        return $objets;
    }

    // Méthode générique pour récupérer un objet par clé primaire
    public function select(string $id): ?object {
        $pdo = DatabaseConnection::getPdo();
        $sql = "SELECT * FROM " . $this->getNomTable() . " WHERE " . $this->getPrimaryKey() . " = :id";
        $pdoStatement = $pdo->prepare($sql);
        $pdoStatement->execute([':id' => $id]);
        $result = $pdoStatement->fetch(PDO::FETCH_ASSOC);

        return $result ? $this->construire($result) : null;
    }  

    // Méthode générique pour supprimer un objet par clé primaire
    public function delete(string $id): bool {
        $pdo = DatabaseConnection::getPdo();
        $sql = "DELETE FROM " . $this->getNomTable() . " WHERE " . $this->getPrimaryKey() . " = :id";
        $pdoStatement = $pdo->prepare($sql);
        $pdoStatement->execute([':id' => $id]);

        return $pdoStatement->rowCount() > 0;
    }

    // Methode generique pour creer un objet par le noms des colonnes
    public function sauvegarder(object $object): bool {
        $pdo = DatabaseConnection::getPdo();
    
        // Génération des colonnes et des paramètres pour la requête SQL
        $colonnes = $this->getNomsColonnes();
        $parametres = array_map(fn($col) => ":$col", $colonnes);
    
        $sql = "INSERT INTO " . $this->getNomTable() . " (" . implode(", ", $colonnes) . ")
                VALUES (" . implode(", ", $parametres) . ")
                ON DUPLICATE KEY UPDATE " .
                implode(", ", array_map(fn($col) => "$col = :$col", $colonnes));
    
        $stmt = $pdo->prepare($sql);
    
        // Transformation de l'objet en tableau à l'aide de formatTableau()
        $params = $object->formatTableau();
    
        return $stmt->execute($params);
    }
    

    // Methode generique pour modifier un objet par le nom des colonnes
    public function update(object $object): bool {
        $pdo = DatabaseConnection::getPdo();
    
        // Préparer les colonnes à mettre à jour
        $colonnes = $this->getNomsColonnes();
        $colonnesMaj = array_map(fn($col) => "$col = :$col", $colonnes);
    
        $sql = "UPDATE " . $this->getNomTable() . "
                SET " . implode(", ", $colonnesMaj) . "
                WHERE " . $this->getPrimaryKey() . " = :" . $this->getPrimaryKey();
    
        $stmt = $pdo->prepare($sql);
    
        // Utiliser formatTableau pour préparer les données
        $params = $object->formatTableau();
    
        return $stmt->execute($params);
    }

    ////////////// LOGS //////////////
    public function countByAction($action, $dateDebut = null, $dateFin = null) {
        $sql = "SELECT COUNT(*) FROM logs WHERE action = :action";
    
        // Ajouter une condition de filtre par date si nécessaire
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
        $query = "INSERT INTO " . $this->getNomTable() . " (utilisateur_id, action, description, timestamp) 
                  VALUES (:utilisateur_id, :action, :description, NOW())";
        $stmt = DatabaseConnection::getPdo()->prepare($query);
        $stmt->bindParam(':utilisateur_id', $utilisateurId);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':description', $description);
        $stmt->execute();
    }

    public function getActionsParJour($action, $dateDebut = null, $dateFin = null) {
        $sql = "SELECT DATE(timestamp) as jour, COUNT(*) as total 
                FROM logs 
                WHERE action = :action";
    
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
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }    

    public function getLogsByDate($dateDebut, $dateFin) {
        $sql = "SELECT * FROM logs WHERE DATE(timestamp) BETWEEN :dateDebut AND :dateFin ORDER BY timestamp DESC";
        
        $pdo = DatabaseConnection::getPdo();   
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':dateDebut', $dateDebut);
        $stmt->bindValue(':dateFin', $dateFin);
        $stmt->execute();
        
        $logsData = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $logs = [];
        
        foreach ($logsData as $log) {
            $logs[] = new Logs(
                $log['log_id'],
                $log['user_id'] ?? null, // Use null coalescing operator to handle potential null values
                $log['action'],
                $log['timestamp'],
                $log['description'] ?? ''
            );
        }
        
        return $logs;
    }      

    ////////////// UTILISATEUR / STATIONS / METEOTHEQUES ///////////////////
    // Methode specifique pour recuperer un utilisateur avec son email (unique methode)
    public function selectByEmail(string $email): ?Utilisateur {
        $pdo = DatabaseConnection::getPdo();
        $query = "SELECT * FROM Utilisateurs WHERE email = :email";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
    
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($data) {
            return new Utilisateur(
                $data['utilisateur_id'],
                $data['nom'],
                $data['prenom'],
                $data['email'],
                $data['mot_de_passe'], // Mot de passe haché
                $data['date_creation'],
                $data['role'],
                $data['etat_compte']
            );
        }
    
        return null; // Aucun utilisateur trouvé
    }
          
    // Methode specifique pour recuperer une STATION avec sa region (unique methode)
    public function selectByReg(string $region): ?array {
        $pdo = DatabaseConnection::getPdo();
        $sql = "SELECT * FROM stations WHERE region = :region";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['region' => $region]);
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $stations = [];
        foreach ($result as $stationData) {
            $stations[] = $this->construire($stationData);
        }
        return $stations;
    }

    // Methode specifique pour recuperer la meteotheque d'un utilisateur (unique methode)
    public function findByUserId(int $userId): array {
        $pdo = DatabaseConnection::getPdo();
        $sql = "SELECT * FROM " . $this->getNomTable() . " WHERE utilisateur_id = :userId ORDER BY date_creation DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['userId' => $userId]);

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'construire'], $result);
    }

    public function getAllMeteotheques($userId) {
        $sql = "SELECT nom_collection AS nom, description, date_creation AS date
                FROM " . $this->getNomTable() . "
                WHERE utilisateur_id = :userId";
        $pdo = DatabaseConnection::getPdo();
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
     

    // Methode specifique pour recuperer la meteotheque d'un utilisateur (unique methode)
    public function getAllUtilisateurs() {
        $sql = "
            SELECT DISTINCT 
                u.utilisateur_id AS id, 
                u.nom AS nom,
                u.prenom AS prenom 
            FROM meteotheques m
            JOIN utilisateurs u ON m.utilisateur_id = u.utilisateur_id
        ";
        $pdo = DatabaseConnection::getPdo();
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }    
}

?>