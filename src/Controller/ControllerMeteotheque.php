<?php

namespace App\Meteo\Controller;

use App\Meteo\Model\DataRepository\UtilisateurRepository;
use App\Meteo\Model\DataRepository\MeteothequeRepository;
use App\Meteo\Model\DataRepository\LogRepository;
use App\Meteo\Model\DataObject\Meteotheques;
use Exception;

class ControllerMeteotheque {
    // Montre toutes les meteotheques des utilisateurs existants
    public static function readAll() {
        $repo = new MeteothequeRepository();
        $utilisateurs = $repo->getAllUtilisateurs(); // Méthode à implémenter dans `MeteothequeRepository`
    
        self::afficheVue('view.php', [
            'pagetitle' => 'MéteoVision - Météothèques',
            'utilisateurs' => $utilisateurs,
            'cheminVueBody' => 'meteotheque/list.php'
        ]);
    }

    public static function readMeteothequeByUser() {
        $userId = $_GET['user_id'] ?? null;
    
        if (!$userId || !ctype_digit($userId)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'ID utilisateur invalide.']);
            return;
        }
    
        try {
            $repo = new MeteothequeRepository();
    
            // Récupérer les informations de l'utilisateur
            $utilisateurRepo = new UtilisateurRepository();
            $utilisateur = $utilisateurRepo->select($userId);
    
            if (!$utilisateur) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Utilisateur introuvable.']);
                return;
            }
    
            // Récupérer les météothèques associées
            $meteotheques = $repo->getAllMeteotheques((int) $userId);
    
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'user' => [
                    'nom' => $utilisateur->getNom(),
                    'prenom' => $utilisateur->getPrenom(),
                ],
                'results' => $meteotheques,
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Erreur interne : ' . $e->getMessage()]);
        }
    }           

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

    public static function deleteMeteotheque() {
        if (!isset($_SESSION['utilisateur_id'])) {
            echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté.']);
            return;
        }
    
        $meteoId = $_GET['meteo_id'] ?? null;
        $utilisateurId = $_SESSION['utilisateur_id'];
    
        if (!$meteoId || !ctype_digit($meteoId)) {
            echo json_encode(['success' => false, 'message' => 'ID de la météothèque invalide.']);
            return;
        }
    
        try {
            $repo = new MeteothequeRepository();
            $meteo = $repo->select($meteoId);
    
            if (!$meteo || $meteo->getUtilisateurId() !== (int) $utilisateurId) {
                echo json_encode(['success' => false, 'message' => 'Accès refusé ou météothèque introuvable.']);
                return;
            }
    
            $success = $repo->delete($meteoId);
    
            if ($success) {
                $logRepository = new LogRepository();
                $logRepository->addLog($utilisateurId, 'suppression_meteotheque');
            }
    
            echo json_encode([
                'success' => $success,
                'message' => $success ? 'Météothèque supprimée avec succès.' : 'Erreur lors de la suppression.'
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur interne : ' . $e->getMessage()]);
        }

        header('Location: /SAE3.01/Web/frontController.php?action=readAll&controller=utilisateur');
        exit();
    }
    

    public static function afficheVue(string $cheminVue, array $parametres = []): void {
        extract($parametres);
        require __DIR__ . '/../view/' . $cheminVue;
    }
    
}
?>