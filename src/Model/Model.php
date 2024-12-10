<?php
// Inclusion du fichier de configuration
namespace App\Meteo\Model;

use App\Meteo\Config\Conf;
use \PDO;
use \PDOException;

class Model {

    private static $instance = null;
    // Attribut pour stocker l'objet PDO
    private $pdo;

    // Constructeur sans argument pour l'instant
    private function __construct() {
        // Les informations de connexion sont récupérées via la classe Conf
        $hostname = Conf::getHostname();
        $databaseName = Conf::getDatabase();
        $login = Conf::getLogin();
        $password = Conf::getPassword();

        // Initialisation de l'objet PDO
       try {
            // Connexion à la base de données 
            /* 
            Le dernier argument sert à ce que toutes les chaînes de caractères
            en entrée et sortie de MySQL soient dans le codage UTF-8
            */
            $this->pdo = new PDO("mysql:host=$hostname;dbname=$databaseName", 
                                $login, $password,
                                array (PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            
            // On active le mode d'affichage des erreurs, et le lancement d'exception
            // en cas d'erreur
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Message de succès pour la connexion
            echo "Connexion réussie avec UTF-8 et gestion des erreurs activée !<br>";
            echo "<br>";
       } catch (PDOException $e) {
            // En cas d'erreur, affichage d'un message et arrêt du script
            echo "Erreur de connexion : " . $e->getMessage();
            die();
       }
    }

    protected static function getInstance() {
        if (is_null(static::$instance))
            static::$instance = new Model();
        return static::$instance;
    }

    // Méthode getter pour récupérer l'objet PDO
    public static function getPdo() {
        return static::getInstance()->pdo;
    }
}
?>
