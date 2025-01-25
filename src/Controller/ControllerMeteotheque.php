<?php

namespace App\Meteo\Controller;

use App\Meteo\Model\DataRepository\MeteothequeRepository;
use App\Meteo\Model\DataObject\Meteotheques;

class ControllerMeteotheque {
    // Enregistre une requête pour la carte interactive
    public static function saveRequest() {
        if (!isset($_SESSION['utilisateur_id'])) {
            echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté.']);
            return;
        }
    
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['region']) || !isset($data['details'])) {
            echo json_encode(['success' => false, 'message' => 'Données invalides.']);
            return;
        }
    
        $regionName = $data['region'];
        $details = $data['details']; // Récupérer les détails envoyés
        $utilisateurId = $_SESSION['utilisateur_id'];
    
        $description = "Carte Interactive => Région $regionName. Détails: $details";
    
        $repo = new MeteothequeRepository();
        $meteo = new Meteotheques(
            0, // ID généré automatiquement
            $utilisateurId,
            $regionName,
            $description,
            date('Y-m-d H:i:s')
        );
    
        $success = $repo->sauvegarder($meteo);
    
        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Requête sauvegardée avec succès.' : 'Erreur lors de la sauvegarde.'
        ]);
    }    

    // Enregistre une requête pour la carte thermique
    public static function saveRequestThermique() {
        if (!isset($_SESSION['utilisateur_id'])) {
            echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté.']);
            return;
        }
    
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['region']) || !isset($data['details'])) {
            echo json_encode(['success' => false, 'message' => 'Données invalides.']);
            return;
        }
    
        $regionName = $data['region'];
        $details = $data['details']; // Détails des températures moyennes
        $utilisateurId = $_SESSION['utilisateur_id'];
    
        $description = "Carte Thermique => Région $regionName. Détails: $details";
    
        $repo = new MeteothequeRepository();
        $meteo = new Meteotheques(
            0, // ID généré automatiquement
            $utilisateurId,
            $regionName,
            $description,
            date('Y-m-d H:i:s')
        );
    
        $success = $repo->sauvegarder($meteo);
    
        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Requête sauvegardée avec succès.' : 'Erreur lors de la sauvegarde.'
        ]);
    }    
}
?>