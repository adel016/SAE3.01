<?php

namespace App\Meteo\Controller;

use App\Meteo\Model\DataRepository\MeteothequeRepository;
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
        $dateDebut = $_POST['dateDebut'] ?? null;
        $dateFin = $_POST['dateFin'] ?? null;

        $logRepository = new LogRepository();

        // Récupérer les statistiques générales pour chaque action
        $nombreInscriptions = $logRepository->countByAction('inscription');
        $nombreConnexions = $logRepository->countByAction('connexion');
        $nombrePromotions = $logRepository->countByAction('promotion');
        $nombreModifications = $logRepository->countByAction('modification');
        $nombreAjoutsMeteotheque = $logRepository->countByAction('ajout_meteotheque');

        // Récupérer les données par jour pour les graphiques
        $inscriptionsParJour = $logRepository->getActionsParJour('inscription');
        $connexionsParJour = $logRepository->getActionsParJour('connexion');
        $promotionsParJour = $logRepository->getActionsParJour('promotion');
        $modificationsParJour = $logRepository->getActionsParJour('modification');
        $ajoutsMeteothequeParJour = $logRepository->getActionsParJour('ajout_meteotheque');

        // Récupérer tous les logs pour affichage dans un tableau
        $logs = $logRepository->getAll();

        // Appeler la vue avec toutes les données nécessaires
        self::afficheVue('admin.php', [
            'pagetitle' => 'Statistiques',
            'cheminVueBody' => 'admin/statistiques.php',
            'nombreInscriptions' => $nombreInscriptions,
            'nombreConnexions' => $nombreConnexions,
            'nombrePromotions' => $nombrePromotions,
            'nombreModifications' => $nombreModifications,
            'nombreAjoutsMeteotheque' => $nombreAjoutsMeteotheque,
            'inscriptionsParJour' => $inscriptionsParJour,
            'connexionsParJour' => $connexionsParJour,
            'promotionsParJour' => $promotionsParJour,
            'modificationsParJour' => $modificationsParJour,
            'ajoutsMeteothequeParJour' => $ajoutsMeteothequeParJour,
            'logs' => $logs
        ]);

        header('Location: ?action=StatistiquesEtLogs&controller=admin');
        exit();
    }

    // Promouvoir un utilisateur au rôle admin
    public static function promouvoirAdmin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $utilisateurId = $_POST['utilisateur_id'] ?? null;
            if ($utilisateurId) {
                $utilisateurRepository = new UtilisateurRepository();
                $logRepository = new LogRepository();
                
                $utilisateur = $utilisateurRepository->select($utilisateurId);
                if ($utilisateur && $utilisateur->getRole() !== 'admin') {
                    $utilisateur->setRole('admin');
                    $utilisateurRepository->update($utilisateur);
                    
                    // Ajouter un log pour la promotion
                    $logRepository->addLog($utilisateurId, 'promotion', "Utilisateur promu au rôle d'administrateur");

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
                $logRepository = new LogRepository();
                
                // Supprimer les logs liés à cet utilisateur
                $logRepository->delete($utilisateurId);
                
                // Supprimer l'utilisateur
                if ($utilisateurRepository->delete($utilisateurId)) {
                    // Ajouter un log pour l'action de suppression si nécessaire (administrateur)
                    MessageFlash::ajouter('success', 'Utilisateur supprimé avec succès.');
                } else {
                    MessageFlash::ajouter('error', 'Échec de la suppression de l\'utilisateur.');
                }
            }
            header('Location: ?action=tableauDeBord&controller=admin');
            exit();
        }
    }    

    // Recupere la meteotheque de l'utilisateur
    public static function getMeteotheque() {
        $userId = $_GET['user_id'] ?? null;
    
        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'Identifiant utilisateur manquant.']);
            return;
        }
    
        $meteothequeRepo = new MeteothequeRepository();
        $requetes = $meteothequeRepo->findByUserId((int) $userId);
    
        $formattedRequetes = array_map(function($requete) {
            return [
                'nomCollection' => $requete->getNomCollection(),
                'description' => $requete->getDescription(),
                'dateCreation' => $requete->getDateCreation(),
            ];
        }, $requetes);
    
        echo json_encode(['success' => true, 'requetes' => $formattedRequetes]);
    }

    // Méthode générique pour afficher une vue
    private static function afficheVue($vue, $parametres = []) {
        extract($parametres);
        require __DIR__ . "/../View/{$vue}";
    }
}

?>