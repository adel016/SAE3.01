<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_log("Session: " . print_r($_SESSION, true));

require_once __DIR__ . '/../src/Lib/Psr4AutoloaderClass.php';

use \App\Meteo\Lib\Psr4AutoloaderClass;

$loader = new Psr4AutoloaderClass();
$loader->addNamespace('App\Meteo', __DIR__ . '/../src');
$loader->register();

$controller = htmlspecialchars($_GET['controller'] ?? 'utilisateur', ENT_QUOTES, 'UTF-8');
$action = htmlspecialchars($_GET['action'] ?? 'default', ENT_QUOTES, 'UTF-8');
$controllerClass = "App\\Meteo\\Controller\\Controller" . ucfirst($controller);

try {
    if (!class_exists($controllerClass)) {
        throw new Exception("Le contrôleur spécifié est introuvable : $controllerClass");
    }

    if (method_exists($controllerClass, $action)) {
        $controllerClass::$action();
    } else {
        throw new Exception("Action inconnue : $action");
    }
} catch (Exception $e) {
    error_log("❌ Exception: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}
?>