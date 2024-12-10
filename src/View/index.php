<!-- Ceci est le fihcier qui contiendra la page principales du site-->
<?php

require_once __DIR__ . '/../src/Lib/Psr4AutoloaderClass.php';

use App\Meteo\Lib\Psr4AutoloaderClass;

// Instancie et configure l'autoloader
$loader = new Psr4AutoloaderClass();
$loader->register();

// Ajoute le namespace principal et son répertoire de base
$loader->addNamespace('App\Meteo', __DIR__ . '/../src');

// Teste le chargement automatique d'une classe
use App\Meteo\Model\ModelUtilisateur;

$utilisateur = new ModelUtilisateur(
    1, 
    'TestNom', 
    'TestPrenom', 
    'test@example.com', 
    'password123', 
    date('Y-m-d H:i:s'), 
    'utilisateur', 
    'actif'
);

echo "Autoload fonctionne : " . $utilisateur->getNom();
