<?php
namespace App\Meteo\Controller;

require_once __DIR__ . '/../Lib/Psr4AutoloaderClass.php';


use App\Meteo\Model\ModelUtilisateur;
use App\Meteo\Lib\Psr4AutoloaderClass;

// Configuration de l’autoloader
$loader = new Psr4AutoloaderClass();
$loader->register();
$loader->addNamespace('App\Meteo', __DIR__ . '/../');

// Pour l'affichages des erreurs silencieuses 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Fichier ControllerUtilisateur.php chargé avec succès.<br>";

// Détermine l'action à exécuter
$action = $_GET['action'] ?? null;

if ($action === 'inscription') {
    ControllerUtilisateur::inscription();
} elseif ($action === 'connexion') {
    ControllerUtilisateur::connexion();
} else {
    echo "Aucune action spécifiée ou action inconnue.";
}

class ControllerUtilisateur {
    public static function inscription() {
        echo "Méthode inscription appelée.<br>";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo "Données POST reçues : ";
            print_r($_POST);
            echo "<br>";

            $nom = htmlspecialchars($_POST['nom']);
            $prenom = htmlspecialchars($_POST['prenom']);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $motdepasse = password_hash($_POST['motdepasse'], PASSWORD_DEFAULT);

            // Crée un nouvel utilisateur
            $utilisateur = new ModelUtilisateur(0, $nom, $prenom, $email, $motdepasse, date('Y-m-d H:i:s'));

            if ($utilisateur->sauvegarder()) {
                echo "Utilisateur ajouté avec succès !<br>";
            } else {
                echo "Erreur lors de l'ajout de l'utilisateur.<br>";
            }
        } else {
            echo "Méthode non autorisée.<br>";
        }
    }

    public static function connexion() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $motdepasse = $_POST['motdepasse'];

            $utilisateur = ModelUtilisateur::getUtilisateurByEmail($email);

            if ($utilisateur && password_verify($motdepasse, $utilisateur->getMotDePasse())) {
                echo "Connexion réussie. Bienvenue, " . $utilisateur->getNom();
            } else {
                echo "Erreur : Email ou mot de passe incorrect.";
            }
        }
    }
}
