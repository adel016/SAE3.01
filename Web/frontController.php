<?php

require_once __DIR__ . '/../src/Lib/Psr4AutoloaderClass.php';

use App\Covoiturage\Controller\ControllerUtilisateur;
use App\Covoiturage\Lib\Psr4AutoloaderClass;

// Initialisation de l'autoloader
$loader = new Psr4AutoloaderClass();
$loader->addNamespace('App\Covoiturage', __DIR__ . '/../src');
$loader->register();

// Gestion des actions pour la requete GET
$action = $_GET['action'] ?? 'readAll';
$controllerClass = ControllerUtilisateur::class;

// Vérification que l'action est définie et que la méthode existe dans le contrôleur
try {
    if (method_exists($controllerClass, $action)) {
        $controllerClass::$action();
    } else {
        throw new Exception("Action inconnue : $action");
    }
} catch (Exception $e) {
    // Gestion des erreurs
    require __DIR__ . '/../src/View/error.php';
    echo "Erreur : " . htmlspecialchars($e->getMessage());
}
?>
