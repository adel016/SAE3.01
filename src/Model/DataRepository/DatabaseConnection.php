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
            // Message de succès (à supprimer en production)
            // echo "Connexion réussie avec UTF-8 et gestion des erreurs activée !<br>";
        } catch (PDOException $e) {
            // En cas d'erreur, affiche un message et arrête le script
            echo "Erreur de connexion : " . $e->getMessage();
            die();
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
