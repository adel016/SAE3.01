<?php

namespace App\Meteo\Controller;

use App\Meteo\Model\DataRepository\UtilisateurRepository;
use App\Meteo\Model\DataRepository\LogRepository;
use App\Meteo\Lib\MessageFlash;

class ControllerAdmin {

    // Affiche le tableau de bord principal
    public static function tableauDeBord() {
        $utilisateurRepository = new UtilisateurRepository();
        $utilisateurs = $utilisateurRepository->getAll(); // Récupère tous les utilisateurs

        // Affiche la vue générique avec la liste des utilisateurs
        self::afficheVue('admin.php', [
            'pagetitle' => 'Tableau de bord Admin',
            'cheminVueBody' => 'admin/AdminDashBoard.php',
            'utilisateurs' => $utilisateurs,
        ]);
    }

    // Affiche les statistiques et logs dans une vue dédiée
    public static function StatistiquesEtLogs() {
        $logRepository = new LogRepository();

        // Récupérer les statistiques générales
        $nombreInscriptions = $logRepository->countByAction('inscription');
        $nombreConnexions = $logRepository->countByAction('connexion');

        // Récupérer les données par jour pour les graphiques
        $inscriptionsParJour = $logRepository->getActionsParJour('inscription');
        $connexionsParJour = $logRepository->getActionsParJour('connexion');

        // Récupérer tous les logs
        $logs = $logRepository->getAll();

        // Appeler la vue avec les données nécessaires
        self::afficheVue('admin.php', [
            'pagetitle' => 'Statistiques',
            'cheminVueBody' => 'admin/statistiques.php',
            'nombreInscriptions' => $nombreInscriptions,
            'nombreConnexions' => $nombreConnexions,
            'inscriptionsParJour' => $inscriptionsParJour,
            'connexionsParJour' => $connexionsParJour,
            'logs' => $logs
        ]);
    }

    // Promouvoir un utilisateur au rôle admin
    public static function promouvoirAdmin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $utilisateurId = $_POST['utilisateur_id'] ?? null;
            if ($utilisateurId) {
                $utilisateurRepository = new UtilisateurRepository();
                $utilisateur = $utilisateurRepository->select($utilisateurId);
                if ($utilisateur && $utilisateur->getRole() !== 'admin') {
                    $utilisateur->setRole('admin');
                    $utilisateurRepository->update($utilisateur);
                    MessageFlash::ajouter('success', 'Utilisateur promu au rôle d\'admin.');
                }
            }
            header('Location: ?action=tableauDeBord&controller=admin');
            exit();
        }
    }

    // Supprimer un utilisateur
    public static function supprimerUtilisateur() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $utilisateurId = $_POST['utilisateur_id'] ?? null;
            if ($utilisateurId) {
                $utilisateurRepository = new UtilisateurRepository();
                $utilisateurRepository->delete($utilisateurId);
                MessageFlash::ajouter('success', 'Utilisateur supprimé avec succès.');
            }
            header('Location: ?action=tableauDeBord&controller=admin');
            exit();
        }
    }

    // Méthode générique pour afficher une vue
    private static function afficheVue($vue, $parametres = []) {
        extract($parametres);
        require __DIR__ . "/../View/{$vue}";
    }
}

?>