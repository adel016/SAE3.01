<?php

namespace App\Meteo\Controller;

use App\Meteo\Model\DataRepository\UtilisateurRepository;
use App\Meteo\Model\DataRepository\LogRepository;


class ControllerAdmin {
    // Afficher le tableau de bord des administrateurs
    public static function tableauDeBord() {
        self::verifierAdmin();

        $repository = new UtilisateurRepository();
        $utilisateurs = $repository->getAll();

        self::afficheVue('admin/AdminDashBoard.php', [
            'utilisateurs' => $utilisateurs,
        ]);
    }

    // Promouvoir un utilisateur au rôle d'admin
    public static function promouvoirAdmin() {
        self::verifierAdmin();

        $id = $_POST['utilisateur_id'] ?? null;

        if ($id) {
            $repository = new UtilisateurRepository();
            $repository->modifierRole($id, 'admin');

            header('Location: ' . \App\Meteo\Config\Conf::getBaseUrl() . '/Web/frontController.php?action=tableauDeBord&controller=admin');
            exit();
        }
    }

    // Supprimer un utilisateur
    public static function supprimerUtilisateur() {
        self::verifierAdmin();

        $id = $_POST['utilisateur_id'] ?? null;

        if ($id) {
            $repository = new UtilisateurRepository();
            $repository->delete($id);

            header('Location: ' . \App\Meteo\Config\Conf::getBaseUrl() . '/Web/frontController.php?action=tableauDeBord&controller=admin');
            exit();
        }
    }

    // Méthode privée pour vérifier si l'utilisateur est un administrateur
    private static function verifierAdmin() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: ' . \App\Meteo\Config\Conf::getBaseUrl() . '/Web/frontController.php?action=connexion&controller=utilisateur');
            exit();
        }
    }

    private static function afficheVue(string $cheminVue, array $parametres = []): void {
        $cheminComplet = __DIR__ . '/../View/' . $cheminVue; // Ajustement pour src\View
    
        if (!file_exists($cheminComplet)) {
            die("Erreur : Fichier introuvable -> " . $cheminComplet);
        }
    
        extract($parametres);
        require $cheminComplet;
    }

    public static function afficherStatistiques() {
        self::verifierAdmin();

        $repository = new LogRepository();
        $nombreInscriptions = $repository->countByAction('inscription');
        $nombreConnexions = $repository->countByAction('connexion');
        $logs = $repository->getAll();

        $inscriptionsParJour = $repository->getActionsParJour('inscription');
        $connexionsParJour = $repository->getActionsParJour('connexion');

        self::afficheVue('admin/statistiques.php', [
            'nombreInscriptions' => $nombreInscriptions,
            'nombreConnexions' => $nombreConnexions,
            'logs' => $logs,
            'inscriptionsParJour' => $inscriptionsParJour,
            'connexionsParJour' => $connexionsParJour,
        ]);
    }

    

}

?>
