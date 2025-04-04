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
        // Définit l'en-tête Content-Type pour indiquer que la réponse est du JSON
        header('Content-Type: application/json');

        // Récupère l'ID utilisateur depuis la requête GET
        $userId = $_GET['user_id'] ?? null;

        // Log de l'ID utilisateur reçu
        error_log("🔍 ID utilisateur reçu: " . ($userId ?: 'Aucun'));

        // Vérifie si l'ID utilisateur est valide
        if (!isset($userId) || !ctype_digit($userId) || (int)$userId <= 0) {
            // Log de l'erreur
            error_log("❌ ID utilisateur invalide : " . $userId);

            // Envoie une réponse JSON avec un message d'erreur
            echo json_encode(['success' => false, 'message' => 'ID utilisateur invalide.']);
            return;
        }

        try {
            // Initialise les repositories
            $repo = new MeteothequeRepository();
            $utilisateurRepo = new UtilisateurRepository();

            // Récupère les informations de l'utilisateur
            $utilisateur = $utilisateurRepo->select($userId);

            // Vérifie si l'utilisateur existe
            if (!$utilisateur) {
                // Log de l'erreur
                error_log("❌ Utilisateur introuvable : " . $userId);

                // Envoie une réponse JSON avec un message d'erreur
                echo json_encode(['success' => false, 'message' => 'Utilisateur introuvable.']);
                return;
            }

            // Log de l'utilisateur trouvé
            error_log("✅ Utilisateur trouvé: " . json_encode($utilisateur));

            // Récupère les météothèques associées
            $meteotheques = $repo->getAllMeteotheques((int)$userId);

            // Log des météothèques trouvées
            error_log("📂 Météothèques trouvées : " . json_encode($meteotheques));

            // Prépare les données pour le graphique
            $chartData = [
                'labels' => [],
                'data' => []
            ];

            error_log("📂 Type de meteotheques : " . gettype($meteotheques));
            if (is_array($meteotheques)) {
                foreach ($meteotheques as $meteo) {
                    error_log("🔎 Type d'élément : " . gettype($meteo));
                }
            }

            // Génère les données pour le graphique
            foreach ($meteotheques as $meteo) {
                $chartData['labels'][] = $meteo->getNomCollection();
                $chartData['data'][] = rand(1, 100); // Exemple : valeur aléatoire
            }

            // Envoie une réponse JSON avec les données
            echo json_encode([
                'success' => true,
                'user' => [
                    'nom' => $utilisateur->getNom(),
                    'prenom' => $utilisateur->getPrenom(),
                ],
                'results' => array_map(fn($m) => [
                    'meteo_id' => $m->getMeteoId(),
                    'utilisateur_id' => $m->getUtilisateurId(),
                    'nom_collection' => $m->getNomCollection(),
                    'description' => $m->getDescription(),
                    'date_creation' => $m->getDateCreation(),
                ], $meteotheques),
                'chartData' => $chartData,
            ]);

        } catch (Exception $e) {
            // Log de l'erreur
            error_log("🔥 Erreur interne : " . $e->getMessage());

            // Envoie une réponse JSON avec un message d'erreur
            echo json_encode(['success' => false, 'message' => 'Erreur interne : ' . $e->getMessage()]);
        }
    }              

    // Enregistre une requête pour la carte interactive (cote Region)
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
    
    // Enregistre une requete pour la carte interactive (cote Station)
    public static function saveStationRequest() {
        if (!isset($_SESSION['utilisateur_id'])) {
            error_log("❌ Utilisateur non connecté");
            echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté.']);
            return;
        }
    
        $data = json_decode(file_get_contents('php://input'), true);
        error_log("📥 Données reçues : " . print_r($data, true));
    
        if (!$data || !isset($data['station_id']) || !is_string($data['station_id']) || !isset($data['details'])) {
            error_log("❌ Données invalides");
            echo json_encode(['success' => false, 'message' => 'Données invalides.']);
            return;
        }
    
        $stationId = $data['station_id'];
        $details = $data['details'];
        $utilisateurId = $_SESSION['utilisateur_id'];
        error_log("✅ Données valides - Utilisateur : $utilisateurId, Station : $stationId");
    
        // Création de l'objet
        $description = "Station ID: $stationId. Détails: $details";
        $repo = new MeteothequeRepository();
        $meteo = new Meteotheques(0, $utilisateurId, "Station_$stationId", $description, date('Y-m-d H:i:s'));
    
        $success = $repo->sauvegarder($meteo);
        error_log($success ? "✅ Enregistrement réussi" : "❌ Échec de l'enregistrement");
    
        // Ajout dans les logs si succès
        if ($success) {
            $logRepository = new LogRepository();
            $logRepository->addLog($utilisateurId, 'ajout_station_meteo');
            error_log("✅ Log ajouté");
        }
    
        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Données de la station enregistrées avec succès.' : 'Erreur lors de l’enregistrement.'
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
                'message' => $success ? 'Météothèque supprimée avec succès.' : 'Erreur lors de la sauvegarde.'
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur interne : ' . $e->getMessage()]);
        }
    }
    

    public static function afficheVue(string $cheminVue, array $parametres = []): void {
        extract($parametres);
        require __DIR__ . '/../view/' . $cheminVue;
    }
    
}
?>