<?php

namespace App\Meteo\Config;

use \PDO;

class Conf {
    // Paramètres de connexion à la base de données
    static private array $databases = array(
        'hostname' => 'localhost',    // Nom de l'hôte
        'database' => 'meteo_db',    // Nom de la base de données (ajusté ici)
        'login' => 'root',           // Utilisateur (par défaut root sur WAMP)
        'password' => 'root'             // Mot de passe (vide par défaut sur WAMP)
    );

    // Méthode pour récupérer le login
    static public function getLogin(): string {
        return static::$databases['login'];
    }

    // Méthode pour récupérer le nom d'hôte
    static public function getHostname(): string {
        return static::$databases['hostname'];
    }

    // Méthode pour récupérer le nom de la base de données
    static public function getDatabase(): string {
        return static::$databases['database'];
    }

    // Méthode pour récupérer le mot de passe
    static public function getPassword(): string {
        return static::$databases['password'];
    }

    // Méthode pour créer une connexion PDO
    static public function getPDO(): PDO {
        try {
            // Construction du DSN (Data Source Name)
            $dsn = 'mysql:host=' . static::getHostname() . ';dbname=' . static::getDatabase() . ';charset=utf8';

            // Création de la connexion PDO
            $pdo = new PDO($dsn, static::getLogin(), static::getPassword(), [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Active les exceptions en cas d'erreur
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Résultats sous forme de tableau associatif
            ]);

            return $pdo;
        } catch (\PDOException $e) {
            die('Erreur de connexion à la base de données : ' . $e->getMessage());
        }
    }
}
