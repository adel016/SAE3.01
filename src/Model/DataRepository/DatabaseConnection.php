<?php

namespace App\Meteo\Model\DataRepository;

use App\Meteo\Config\Conf;
use PDO;
use PDOException;

class DatabaseConnection {
    private static ?DatabaseConnection $instance = null; // Singleton
    private PDO $pdo;

    // Constructeur privé pour empêcher l'instanciation directe
    private function __construct() {
        try {
            // Utilise la configuration via la classe Conf
            $this->pdo = Conf::getPDO();
            
            // Vérification de la connexion
            if ($this->pdo) {
                error_log("Connexion à la base de données réussie !");
            } else {
                error_log("ÉCHEC de connexion à la base de données !");
            }
        } catch (PDOException $e) {
            // En cas d'erreur, log l'erreur et arrête le script
            error_log("Erreur de connexion : " . $e->getMessage());
            die("Erreur de connexion à la base de données.");
        }
    }

    // Récupère l'instance unique de la classe
    private static function getInstance(): DatabaseConnection {
        if (self::$instance === null) {
            self::$instance = new DatabaseConnection();
        }
        return self::$instance;
    }

    // Récupère l'objet PDO pour effectuer des requêtes
    public static function getPdo(): PDO {
        return self::getInstance()->pdo;
    }
}
?>