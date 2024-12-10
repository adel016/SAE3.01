<?php
namespace App\Meteo\Controller;

use App\Meteo\Model\ModelUtilisateur;


try {
    $utilisateur = new ModelUtilisateur(0, 'John', 'Doe', 'john.doe@example.com', password_hash('password', PASSWORD_DEFAULT), date('Y-m-d H:i:s'));
    if ($utilisateur->sauvegarder()) {
        echo "Test réussi : utilisateur ajouté !";
    } else {
        echo "Erreur : utilisateur non ajouté.";
    }
} catch (\Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
