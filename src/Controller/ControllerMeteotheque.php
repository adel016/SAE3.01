<?php

namespace App\Meteo\Controller;

use App\Meteo\Model\DataRepository\MeteothequeRepository;
use App\Meteo\Model\DataObject\Meteotheques;
use App\Meteo\Lib\MessageFlash;

class ControllerMeteotheque {
    // Methode qui affiche la Meteothèque de l'utilisateur
    public static function showMeteotheque() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['utilisateur_id'])) {
            MessageFlash::ajouter('danger', 'Vous devez être connecté pour accéder à votre profil.');
            header('Location: /Web/frontController.php?action=login&controller=utilisateur');
            exit();
        }

        $utilisateurId = $_SESSION['utilisateur_id'];

        // Récupérer la Meteothèque de l'utilisateur
        $meteothequeRepo = new MeteothequeRepository();
        $requetes = $meteothequeRepo->findByUserId($utilisateurId);

        self::afficheVue('utilisateur/profil.php', [
            'utilisateurId' => $utilisateurId,
            'requetes' => $requetes,
            'pagetitle' => 'Profil utilisateur',
        ]);
    }

    // Methode qui enregistre une requête
    public static function saveRequest() {
        if (!isset($_SESSION['utilisateur_id'])) {
            echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté.']);
            return;
        }

        // Récupérez les données envoyées depuis le client
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['region'])) {
            echo json_encode(['success' => false, 'message' => 'Données invalides.']);
            return;
        }

        $regionName = $data['region'];
        $utilisateurId = $_SESSION['utilisateur_id'];

        // Sauvegarde dans la base de données
        $repo = new MeteothequeRepository();
        $meteo = new Meteotheques(
            0, // ID généré automatiquement
            $utilisateurId,
            $regionName,
            "Carte Interactive => Région $regionName",
            date('Y-m-d H:i:s')
        );

        $success = $repo->sauvegarder($meteo);

        // Réponse JSON
        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Requête sauvegardée avec succès.' : 'Erreur lors de la sauvegarde.'
        ]);
    }

    // Methode qui enregistre une requête
    public static function saveRequestThermique() {
        if (!isset($_SESSION['utilisateur_id'])) {
            echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté.']);
            return;
        }

        // Récupérez les données envoyées depuis le client
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['region'])) {
            echo json_encode(['success' => false, 'message' => 'Données invalides.']);
            return;
        }

        $regionName = $data['region'];
        $utilisateurId = $_SESSION['utilisateur_id'];

        // Sauvegarde dans la base de données
        $repo = new MeteothequeRepository();
        $meteo = new Meteotheques(
            0, // ID généré automatiquement
            $utilisateurId,
            $regionName,
            "Carte Thermique => Région $regionName",
            date('Y-m-d H:i:s')
        );

        $success = $repo->sauvegarder($meteo);

        // Réponse JSON
        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Requête sauvegardée avec succès.' : 'Erreur lors de la sauvegarde.'
        ]);
    }
    
    // Méthode pour afficher une vue
    private static function afficheVue(string $cheminVue, array $parametres = []): void {
        extract($parametres);
        require __DIR__ . '/../view/' . $cheminVue;
    }
}
