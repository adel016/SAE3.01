<?php

    namespace App\Covoiturage\Config;

    class Conf {
        static private array $databases = array(
            // Le nom d'hote est localhost sur votre machine
            'hostname' => 'localhost',
            // Sur votre machine, vous devrez creer une BDD
            'database' => 'Meteo',
            // Sur votre machine, vous avez surement un compte 'root'
            'login' => 'root',
            // Sur votre machine, vous avez créé ou non ce mdp a l'installation
            'password' => 'root'
        );

        // Methode pour recuperer le login
        static public function getLogin() : string {
            // L'attribut statique $databases s'obtient
            // avec la syntaxe static::$databases
            // au lieu de $this->databases pour un attribut non statique
            return static::$databases['login'];
        }

        // Methode pour recuperer le nom d'hote
        static public function getHostname() : string {
            return static::$databases['hostname'];
        }

        // Methode pour recuperer le nom de la DB
        static public function getDatabase() : string {
            return static::$databases['database'];
        }

        // Methode pour recuperer le password
        static public function getPassword() : string {
            return static::$databases['password'];
        }
    }
?>