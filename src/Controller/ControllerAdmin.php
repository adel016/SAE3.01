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
            'pagetitle' => 'MéteoVision - TDB Admin',
            'cheminVueBody' => 'admin/AdminDashBoard.php',
            'utilisateurs' => $utilisateurs,
        ]);
    }

    public static function StatistiquesEtLogs() {
        $dateDebut = $_POST['dateDebut'] ?? null;
        $dateFin = $_POST['dateFin'] ?? null;
    
        $logRepository = new LogRepository();
    
        // Si des dates sont fournies, filtrer les logs et statistiques
        if ($dateDebut && $dateFin) {
            $dateDebut = date('Y-m-d', strtotime($dateDebut)); // Formatage sécurisé
            $dateFin = date('Y-m-d', strtotime($dateFin));
    
            $nombreInscriptions = $logRepository->countByAction('inscription', $dateDebut, $dateFin);
            $nombreConnexions = $logRepository->countByAction('connexion', $dateDebut, $dateFin);
            $nombrePromotions = $logRepository->countByAction('promotion', $dateDebut, $dateFin);
            $nombreModifications = $logRepository->countByAction('modification', $dateDebut, $dateFin);
            $nombreAjoutsMeteotheque = $logRepository->countByAction('ajout_meteotheque', $dateDebut, $dateFin);
    
            $inscriptionsParJour = $logRepository->getActionsParJour('inscription', $dateDebut, $dateFin);
            $connexionsParJour = $logRepository->getActionsParJour('connexion', $dateDebut, $dateFin);
            $promotionsParJour = $logRepository->getActionsParJour('promotion', $dateDebut, $dateFin);
            $modificationsParJour = $logRepository->getActionsParJour('modification', $dateDebut, $dateFin);
            $ajoutsMeteothequeParJour = $logRepository->getActionsParJour('ajout_meteotheque', $dateDebut, $dateFin);
    
            $logs = $logRepository->getLogsByDate($dateDebut, $dateFin);
        } else {
            // Si aucune date n'est fournie, récupérer toutes les données
            $nombreInscriptions = $logRepository->countByAction('inscription');
            $nombreConnexions = $logRepository->countByAction('connexion');
            $nombrePromotions = $logRepository->countByAction('promotion');
            $nombreModifications = $logRepository->countByAction('modification');
            $nombreAjoutsMeteotheque = $logRepository->countByAction('ajout_meteotheque');
    
            $inscriptionsParJour = $logRepository->getActionsParJour('inscription');
            $connexionsParJour = $logRepository->getActionsParJour('connexion');
            $promotionsParJour = $logRepository->getActionsParJour('promotion');
            $modificationsParJour = $logRepository->getActionsParJour('modification');
            $ajoutsMeteothequeParJour = $logRepository->getActionsParJour('ajout_meteotheque');
    
            $logs = $logRepository->getAll();
        }
    
        // Charger la vue avec les données filtrées
        self::afficheVue('admin.php', [
            'pagetitle' => 'MétéoVision - Statistiques',
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
    
        echo json_encode(['success' => true, 'requetes' => array_map(function($requete) {
            return [
                'nomCollection' => htmlspecialchars($requete['nomCollection']),
                'description' => htmlspecialchars($requete['description']),
                'dateCreation' => $requete['dateCreation']
            ];
        }, $formattedRequetes)]);
    }

    // Méthode générique pour afficher une vue
    private static function afficheVue($vue, $parametres = []) {
        extract($parametres);
        require __DIR__ . "/../View/{$vue}";
    }
}

?>