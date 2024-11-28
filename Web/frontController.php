<?php

require_once __DIR__ . '/../src/Lib/Psr4AutoloaderClass.php';

use App\Covoiturage\Lib\Psr4AutoloaderClass;

// Initialisation de l'autoloader
$loader = new Psr4AutoloaderClass();
$loader->addNamespace('App\Covoiturage', __DIR__ . '/../src');
$loader->register();

// Gestion des actions pour la requête GET
$controller = htmlspecialchars($_GET['controller'] ?? 'Utilisateur');
$action = htmlspecialchars($_GET['action'] ?? 'readAll');

$controllerClass = "App\\Covoiturage\\Controller\\Controller" . ucfirst($controller);

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
