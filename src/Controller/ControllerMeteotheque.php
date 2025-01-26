<?php

namespace App\Meteo\Controller;

use App\Meteo\Model\DataRepository\MeteothequeRepository;
use App\Meteo\Model\DataRepository\LogRepository;
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

        // Ajouter une entrée dans les logs
        if ($success) {
            $logRepository = new LogRepository();
            $logRepository->addLog($utilisateurId, 'ajout_meteotheque');
        }
    
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

        // Ajouter une entrée dans les logs
        if ($success) {
            $logRepository = new LogRepository();
            $logRepository->addLog($utilisateurId, 'ajout_meteotheque');
        }
    
        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Requête sauvegardée avec succès.' : 'Erreur lors de la sauvegarde.'
        ]);
    }    

    public static function saveRequestTableaudeBordGraphique() {
        if (!isset($_SESSION['utilisateur_id'])) {
            echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté.']);
            return;
        }
    
        $data = json_decode(file_get_contents('php://input'), true);
    
        if (!$data || !isset($data['libgeo']) || !isset($data['region']) || !isset($data['nom_dept']) || !isset($data['date_debut']) || !isset($data['date_fin'])) {
            echo json_encode(['success' => false, 'message' => 'Données invalides.']);
            return;
        }
    
        $ville = $data['libgeo'];
        $region = $data['region'];
        $departement = $data['nom_dept'];
        $dateDebut = $data['date_debut'];
        $dateFin = $data['date_fin'];
        $utilisateurId = $_SESSION['utilisateur_id'];
    
        $description = "Tableau de Bord avec Graphique => Ville: $ville, Région: $region, Département: $departement, Période: $dateDebut à $dateFin";
    
        $repo = new MeteothequeRepository();
        $meteo = new Meteotheques(
            0, // ID généré automatiquement
            $utilisateurId,
            $region,
            $description,
            date('Y-m-d H:i:s')
        );
    
        $success = $repo->sauvegarder($meteo);
    
        // Ajouter une entrée dans les logs
        if ($success) {
            $logRepository = new LogRepository();
            $logRepository->addLog($utilisateurId, 'recherche_tableau_bord');
        }
    
        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Requête sauvegardée avec succès.' : 'Erreur lors de la sauvegarde.'
        ]);
    }

    public static function saveRequestTableaudeBordJSONouTableau() {
        if (!isset($_SESSION['utilisateur_id'])) {
            echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté.']);
            return;
        }
    
        $data = json_decode(file_get_contents('php://input'), true);
    
        if (!$data || !isset($data['libgeo']) || !isset($data['region']) || !isset($data['nom_dept']) || !isset($data['date_debut']) || !isset($data['date_fin']) || !isset($data['displayMode'])) {
            echo json_encode(['success' => false, 'message' => 'Données invalides.']);
            return;
        }
    
        $ville = $data['libgeo'];
        $region = $data['region'];
        $departement = $data['nom_dept'];
        $dateDebut = $data['date_debut'];
        $dateFin = $data['date_fin'];
        $displayMode = $data['displayMode']; // JSON ou Tableau
        $utilisateurId = $_SESSION['utilisateur_id'];
    
        // Déterminer la description en fonction du format d'affichage
        $formatDescription = $displayMode === 'json' ? 'JSON brut' : 'Tableau';
        $description = "Tableau de Bord => Format: $formatDescription, Ville: $ville, Région: $region, Département: $departement, Période: $dateDebut à $dateFin";
    
        $repo = new MeteothequeRepository();
        $meteo = new Meteotheques(
            0, // ID généré automatiquement
            $utilisateurId,
            $region,
            $description,
            date('Y-m-d H:i:s')
        );
    
        $success = $repo->sauvegarder($meteo);
    
        // Ajouter une entrée dans les logs
        if ($success) {
            $logRepository = new LogRepository();
            $logRepository->addLog($utilisateurId, 'recherche_tableau_bord');
        }
    
        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Requête sauvegardée avec succès.' : 'Erreur lors de la sauvegarde.'
        ]);
    }    
    
}
?>