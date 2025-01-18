<?php

namespace App\Meteo\Controller;

use App\Meteo\Model\DataRepository\UtilisateurRepository;
use App\Meteo\Model\DataObject\Utilisateur;
use App\Meteo\Lib\MessageFlash;

class ControllerUtilisateur {
    public static function default() : void {
        self::afficheVue('view.php', [
            'pagetitle' => "ACCUEIL - METEOVISION",
            'cheminVueBody' => "accueil/index.php"
        ]);
    }

    public static function readAll() : void {
        $utilisateurs = (new UtilisateurRepository())->getAll(); //appel au modèle pour gérer la BD
        self::afficheVue('view.php', [
            'utilisateurs' => $utilisateurs,
            'pagetitle' => "Liste des utilisateurs",
            'cheminVueBody' => "utilisateur/list.php"
        ]);
    }

    public static function inscription() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Afficher le formulaire d'inscription
            self::afficheVue('view.php', [
                'pagetitle' => "Inscription",
                'cheminVueBody' => "utilisateur/authentification.php"
            ]);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Traiter les données du formulaire
            $nom = htmlspecialchars($_POST['nom'] ?? '');
            $prenom = htmlspecialchars($_POST['prenom'] ?? '');
            $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
            $motDePasse = $_POST['motdepasse'] ?? '';

            if ($nom && $prenom && $email && $motDePasse) {
                $motDePasseHash = password_hash($motDePasse, PASSWORD_DEFAULT);

                // Crée un nouvel utilisateur
                $utilisateur = new Utilisateur(
                    0,
                    $nom,
                    $prenom,
                    $email,
                    $motDePasseHash,
                    date('Y-m-d H:i:s')
                );

                $repository = new UtilisateurRepository();
                if ($repository->sauvegarder($utilisateur)) {
                    MessageFlash::ajouter('success', "Utilisateur ajouté avec succès !");
                    header('Location: /Web/frontController.php?action=readAll&controller=utilisateur');
                    exit();
                } else {
                    MessageFlash::ajouter('error', "Erreur lors de l'ajout de l'utilisateur.");
                }
            } else {
                MessageFlash::ajouter('error', "Veuillez remplir tous les champs.");
            }

            // Réaffiche le formulaire d'inscription avec les messages flash
            self::afficheVue('view.php', [
                'pagetitle' => "Inscription",
                'cheminVueBody' => "utilisateur/authentification.php"
            ]);
        }
    }

    public static function connexion() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Affiche le formulaire de connexion
            self::afficheVue('view.php', [
                'pagetitle' => 'CONNEXION',
                'cheminVueBody' => 'utilisateur/authentification.php'
            ]);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupère les données du formulaire
            $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
            $motDePasse = $_POST['motdepasse'] ?? '';
    
            // Vérifie si les champs sont remplis
            if (!empty($email) && !empty($motDePasse)) {
                // Récupération de l'utilisateur à partir du repository
                $repository = new UtilisateurRepository();
                $utilisateur = $repository->select($email);
    
                if ($utilisateur) {
                    // Vérifie le mot de passe
                    if (password_verify($motDePasse, $utilisateur->getMotDePasse())) {
                        // Initialise la session utilisateur
                        if (session_status() === PHP_SESSION_NONE) {
                            session_start();
                        }
                        $_SESSION['utilisateur_id'] = $utilisateur->getId();
                        $_SESSION['nom'] = $utilisateur->getNom();
    
                        // Redirection après connexion réussie
                        MessageFlash::ajouter('success', "Bienvenue, " . htmlspecialchars($utilisateur->getNom()) . " !");
                        header('Location: /Web/frontController.php');
                        exit();
                    } else {
                        MessageFlash::ajouter('error', "Mot de passe incorrect.");
                    }
                } else {
                    MessageFlash::ajouter('error', "Aucun utilisateur trouvé avec cet email.");
                }
            } else {
                MessageFlash::ajouter('error', "Veuillez remplir tous les champs.");
            }
    
            // Réaffiche le formulaire avec les messages flash en cas d'erreur
            self::afficheVue('view.php', [
                'pagetitle' => 'CONNEXION',
                'cheminVueBody' => 'utilisateur/authentification.php'
            ]);
        }
    }
    

    public static function deconnexion() {
        session_start();
        session_unset(); // Supprime toutes les variables de session
        session_destroy(); // Détruit la session
        MessageFlash::ajouter('success', "Vous avez été déconnecté.");
        header('Location: /Web/frontController.php'); // Redirige vers la page d'accueil
        exit();
    }

    public static function tableauDeBord() {
        session_start();
        if (!isset($_SESSION['utilisateur_id'])) {
            // Si l'utilisateur n'est pas connecté, redirigez vers la page de connexion
            MessageFlash::ajouter('error', "Veuillez vous connecter pour accéder au tableau de bord.");
            header('Location: /Web/frontController.php?action=connexion&controller=utilisateur');
            exit();
        }
    
        // Si l'utilisateur est connecté, affichez le tableau de bord
        self::afficheVue('view.php', [
            'pagetitle' => 'TABLEAU DE BORD',
            'cheminVueBody' => 'tableauDeBord/index.php'
        ]);
    }    

#################################
######### PARTIE ADMIN ##########
#################################

    public static function update() {
        $id = $_GET['id'] ?? null;

        // Si la requête est GET, afficher le formulaire pour l'utilisateur
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && $id) {
            $repository = new UtilisateurRepository();
            $utilisateur = $repository->select($id);

            if ($utilisateur) {
                self::afficheVue('view.php', [
                    'utilisateur' => $utilisateur,
                    'pagetitle' => "Modifier un utilisateur",
                    'cheminVueBody' => 'utilisateur/modifier.php'
                ]);
            } else {
                MessageFlash::ajouter('error', "Utilisateur introuvable.");
                header('Location: /Web/frontController.php?action=readAll&controller=utilisateur');
                exit();
            }
        } 
        // Si la requête est POST, traiter la modification
        elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
            $nom = htmlspecialchars($_POST['nom'] ?? '');
            $prenom = htmlspecialchars($_POST['prenom'] ?? '');
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

            // Validation des champs
            if ($nom && $prenom && $email) {
                $repository = new UtilisateurRepository();
                $utilisateur = $repository->select($id);

                if ($utilisateur) {
                    // Modifier directement les données
                    $utilisateur = new Utilisateur(
                        $utilisateur->getId(),
                        $nom,
                        $prenom,
                        $email,
                        $utilisateur->getMotDePasse(),
                        $utilisateur->getDateCreation(),
                        $utilisateur->getRole(),
                        $utilisateur->getEtatCompte()
                    );

                    // Mise à jour dans la base de données
                    if ($repository->update($utilisateur)) {
                        MessageFlash::ajouter('success', "Utilisateur modifié avec succès.");
                    } else {
                        MessageFlash::ajouter('error', "Échec de la modification.");
                    }
                } else {
                    MessageFlash::ajouter('error', "Utilisateur introuvable.");
                }
            } else {
                MessageFlash::ajouter('error', "Veuillez remplir tous les champs.");
            }

            // Redirection après traitement
            header('Location: /Web/frontController.php?action=readAll&controller=utilisateur');
            exit();
        }
    }

    public static function delete() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $repository = new UtilisateurRepository();
            
            if ($repository->delete($id)) {
                MessageFlash::ajouter('success', "Utilisateur supprimé avec succès.");
            } else {
                MessageFlash::ajouter('error', "Échec de la suppression.");
            }
        } else {
            MessageFlash::ajouter('error', "Utilisateur introuvable !");
        }

        header('Location: /Web/frontController.php?action=readAll&controller=utilisateur');
        exit;
    }

    public static function changerRole() {
        // Vérifiez si la requête est POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $nouveauRole = $_POST['role'] ?? null;
    
            // Validation des champs
            if (!$id || !$nouveauRole) {
                MessageFlash::ajouter('error', "ID ou rôle manquant.");
                header('Location: /Web/frontController.php?action=readAll&controller=utilisateur');
                exit();
            }
    
            $repository = new UtilisateurRepository();
            $utilisateur = $repository->select($id);
    
            // Vérifiez si l'utilisateur existe
            if (!$utilisateur) {
                MessageFlash::ajouter('error', "Utilisateur introuvable.");
                header('Location: /Web/frontController.php?action=readAll&controller=utilisateur');
                exit();
            }
    
            // Mise à jour du rôle
            $data = $utilisateur->formatTableau();
            $data['role'] = $nouveauRole;
    
            // Créez un nouvel objet utilisateur avec le rôle modifié
            $utilisateurModifie = new Utilisateur(
                $data['utilisateur_id'],
                $data['nom'],
                $data['prenom'],
                $data['email'],
                $data['mot_de_passe'],
                $data['date_creation'],
                $data['role'],
                $data['etat_compte']
            );
    
            // Sauvegardez dans la base de données
            if ($repository->update($utilisateurModifie)) {
                MessageFlash::ajouter('success', "Rôle mis à jour avec succès.");
            } else {
                MessageFlash::ajouter('error', "Erreur lors de la mise à jour du rôle.");
            }
        }
    
        // Redirection vers la liste des utilisateurs
        header('Location: /Web/frontController.php?action=readAll&controller=utilisateur');
        exit();
    }       

    public static function afficheVue(string $cheminVue, array $parametres = []): void {
        extract($parametres); // Crée des variables à partir du tableau $parametres
        require __DIR__ . '/../view/' . $cheminVue; // Charge la vue spécifiée
    }
}
