<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Démarrer la session au début
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../src/Lib/Psr4AutoloaderClass.php';

use \App\Meteo\Lib\Psr4AutoloaderClass;

// Initialisation de l'autoloader
$loader = new Psr4AutoloaderClass();
$loader->addNamespace('App\Meteo', __DIR__ . '/../src');
$loader->register();

// Gestion des actions pour la requête GET
$controller = htmlspecialchars($_GET['controller'] ?? 'utilisateur', ENT_QUOTES, 'UTF-8');
$action = htmlspecialchars($_GET['action'] ?? 'default', ENT_QUOTES, 'UTF-8');

// Déterminer la classe du contrôleur
if ($controller === 'tableauDeBord') {
    $controllerClass = "App\\Meteo\\Controller\\TableauDeBordController";
} elseif ($controller === 'contact') {
    $controllerClass = "App\\Meteo\\Controller\\ContactController";
} else {
    $controllerClass = "App\\Meteo\\Controller\\Controller" . ucfirst($controller);
}

try {
    // Vérification que la classe contrôleur existe
    if (!class_exists($controllerClass)) {
        throw new Exception("Le contrôleur spécifié est introuvable : $controllerClass");
    }

    // Vérification que l'action existe
    if (method_exists($controllerClass, $action)) {
        $controllerClass::$action();
    } else {
        throw new Exception("Action inconnue : $action");
    }
} catch (Exception $e) {
    // Gestion des erreurs
    $message = htmlspecialchars($e->getMessage());
    require __DIR__ . '/../src/View/error.php';
}
?>