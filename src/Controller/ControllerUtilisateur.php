<?php

namespace App\Covoiturage\Controller;
use App\Covoiturage\Model\ModelUtilisateur;

class ControllerUtilisateur {
    // Afficher la liste de tous les utilisateurs
    public static function readAll() : void {
        $utilisateurs = ModelUtilisateur::getUtilisateurs(); // Récupère tous les utilisateurs depuis le modèle
        self::afficheVue('view.php', [
            'utilisateurs' => $utilisateurs,
            'pagetitle' => "Liste des utilisateurs",
            'cheminVueBody' => "utilisateur/list.php"
        ]);
    }

    // Afficher les détails d'un utilisateur
    public static function read() : void {
        $utilisateurId = $_GET['utilisateur_id'] ?? null;

        if ($utilisateurId) {
            $utilisateur = ModelUtilisateur::getUtilisateurByID((int)$utilisateurId); // Méthode à ajouter dans ModelUtilisateur
            if ($utilisateur) {
                self::afficheVue('view.php', [
                    'utilisateur' => $utilisateur,
                    'pagetitle' => "Détails de l'utilisateur",
                    'cheminVueBody' => "utilisateur/details.php"
                ]);
            } else {
                self::afficheVue('view.php', [
                    'pagetitle' => "Erreur",
                    'cheminVueBody' => "utilisateur/error.php",
                    'message' => "Utilisateur introuvable."
                ]);
            }
        } else {
            self::afficheVue('view.php', [
                'pagetitle' => "Erreur",
                'cheminVueBody' => "utilisateur/error.php",
                'message' => "ID utilisateur manquant."
            ]);
        }
    }

    // Afficher le formulaire de création d'utilisateur
    public static function create() : void {
        self::afficheVue('view.php', [
            'pagetitle' => "Créer un utilisateur",
            'cheminVueBody' => "utilisateur/create.php"
        ]);
    }

    // Traitement de la création d'utilisateur
    public static function created() : void {
        $nom = $_POST['nom'] ?? null;
        $email = $_POST['email'] ?? null;
        $motDePasse = $_POST['mot_de_passe'] ?? null;

        if ($nom && $email && $motDePasse) {
            $utilisateur = new ModelUtilisateur(0, $nom, $email, md5($motDePasse), date('Y-m-d H:i:s'), 'utilisateur', 'en_attente');
            $utilisateur->sauvegarder();

            // Affichage de la liste des utilisateurs
            $utilisateurs = ModelUtilisateur::getUtilisateurs();
            self::afficheVue('view.php', [
                'utilisateurs' => $utilisateurs,
                'pagetitle' => "CONFIRMATION",
                'cheminVueBody' => "utilisateur/created.php"
            ]);
        } else {
            // En cas d'erreur, affichage d'une vue d'erreur
            self::afficheVue('view.php', [
                'pagetitle' => "Erreur",
                'cheminVueBody' => "utilisateur/error.php",
                'message' => "Informations manquantes pour créer l'utilisateur."
            ]);
        }
    }

    // Supprimer un utilisateur
    public static function delete() : void {
        $utilisateurId = $_GET['utilisateur_id'] ?? null;

        if ($utilisateurId) {
            $supp = ModelUtilisateur::deleteByID((int)$utilisateurId); // Méthode à ajouter dans ModelUtilisateur
            if ($supp) {
                $message = "<p>L'utilisateur avec l'ID " . htmlspecialchars($utilisateurId) . " a été supprimé.</p>";
            } else {
                $message = "<p>Erreur : Impossible de supprimer l'utilisateur avec l'ID " . htmlspecialchars($utilisateurId) . ".</p>";
            }
            self::afficheVue('view.php', [
                'utilisateurs' => ModelUtilisateur::getUtilisateurs(),
                'pagetitle' => "Liste des utilisateurs",
                'cheminVueBody' => "utilisateur/list.php",
                'message' => $message
            ]);
        } else {
            self::afficheVue('view.php', [
                'pagetitle' => "Erreur",
                'cheminVueBody' => "utilisateur/error.php",
                'message' => "ID utilisateur non spécifié."
            ]);
        }
    }

    // Modifier un utilisateur
    public static function update() : void {
        $utilisateurId = $_GET['utilisateur_id'] ?? null;
        $nom = $_GET['nom'] ?? null;
        $email = $_GET['email'] ?? null;
        $motDePasse = $_GET['mot_de_passe'] ?? null;

        if ($utilisateurId && $nom && $email && $motDePasse) {
            $utilisateur = new ModelUtilisateur((int)$utilisateurId, $nom, $email, md5($motDePasse), date('Y-m-d H:i:s'), 'utilisateur', 'actif');
            $utilisateur->modifier(); // Méthode dans ModelUtilisateur

            // Affichage de la liste mise à jour
            $utilisateurs = ModelUtilisateur::getUtilisateurs();
            self::afficheVue('view.php', [
                'utilisateurs' => $utilisateurs,
                'pagetitle' => "CONFIRMATION",
                'cheminVueBody' => "utilisateur/updated.php"
            ]);
        } else {
            // En cas d'erreur, affichage d'une vue d'erreur
            self::afficheVue('view.php', [
                'pagetitle' => "Erreur",
                'cheminVueBody' => "utilisateur/error.php",
                'message' => "Informations manquantes pour modifier l'utilisateur."
            ]);
        }
    }

    // Méthode privée pour afficher une vue
    private static function afficheVue(string $cheminVue, array $parametres = []) : void {
        extract($parametres); // Crée des variables à partir du tableau $parametres
        require __DIR__ . '/../View/' . $cheminVue; // Charge la vue générique ou spécifique
    }
}
?>
