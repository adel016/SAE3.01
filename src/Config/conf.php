<?php

namespace App\Meteo\Config;

use \PDO;

class Conf {
    private static array $config = [];

    // Charger la configuration depuis le fichier .env
    public static function loadConfig(): void {
        $envPath = __DIR__ . '/.env';

        if (file_exists($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) {
                    continue; // Ignore les commentaires
                }

                [$key, $value] = explode('=', $line, 2);
                self::$config[trim($key)] = trim($value);
            }
        } else {
            die('.env file not found. Please configure the database settings.');
        }
    }

    public static function getConfig(string $key): string {
        if (!isset(self::$config[$key])) {
            die("Configuration clé $key manquante dans le fichier .env");
        }
        return self::$config[$key];
    }

    public static function getPDO(): PDO {
        try {
            // Charger les configurations si elles ne sont pas encore chargées
            if (!self::$config) {
                self::loadConfig();
            }

            $dsn = 'mysql:host=' . self::getConfig('DB_HOST') . 
                   ';port=' . self::getConfig('DB_PORT') . 
                   ';dbname=' . self::getConfig('DB_NAME') . 
                   ';charset=utf8';

            return new PDO($dsn, self::getConfig('DB_USER'), self::getConfig('DB_PASS'), [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (\PDOException $e) {
            die('Erreur de connexion à la base de données : ' . $e->getMessage());
        }
    }
}
