<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../src/Lib/Psr4AutoloaderClass.php';

use \App\Meteo\Lib\Psr4AutoloaderClass;

$loader = new Psr4AutoloaderClass();
$loader->addNamespace('App\Meteo', __DIR__ . '/../src');
$loader->register();

$controller = htmlspecialchars($_GET['controller'] ?? 'utilisateur', ENT_QUOTES, 'UTF-8');
$action = htmlspecialchars($_GET['action'] ?? 'default', ENT_QUOTES, 'UTF-8');

if ($controller === 'tableauDeBord') {
    $controllerClass = "App\\Meteo\\Controller\\TableauDeBordController";
} elseif ($controller === 'contact') {
    $controllerClass = "App\\Meteo\\Controller\\ContactController";
} else {
    $controllerClass = "App\\Meteo\\Controller\\Controller" . ucfirst($controller);
}

try {
    if (!class_exists($controllerClass)) {
        throw new Exception("Le contrôleur spécifié est introuvable : $controllerClass");
    }
    if (method_exists($controllerClass, $action)) {
        if ($action === 'saveStationRequest' && $controller === 'meteotheque') {
            error_log("saveStationRequest action triggered");
            $data = json_decode(file_get_contents('php://input'), true);
            error_log("Data received: " . print_r($data, true));
        
            header('Content-Type: application/json');
        
            if (!empty($data['station_name']) && !empty($data['details'])) {
                error_log("Data is valid, saving station...");
                echo json_encode(['success' => true, 'message' => 'Données enregistrées avec succès.']);
            } else {
                error_log("Data is invalid");
                echo json_encode(['success' => false, 'message' => 'Données invalides.']);
            }
        
            error_log("Response sent");
            exit;
        }        
        $controllerClass::$action();
    } else {
        throw new Exception("Action inconnue : $action");
    }
} catch (Exception $e) {
    $message = htmlspecialchars($e->getMessage());
    require __DIR__ . '/../src/View/error.php';
    exit; // Ajouter cette ligne
}
?>