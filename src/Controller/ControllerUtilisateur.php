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

    public static function readAll(): void {    
        // Vérifie si l'utilisateur est connecté
        if (!isset($_SESSION['utilisateur_id'])) {
            MessageFlash::ajouter('error', "Vous devez être connecté pour voir cette page.");
            header('Location: /Web/frontController.php?action=connexion&controller=utilisateur');
            exit();
        }
    
        $repository = new UtilisateurRepository();
    
        // Si l'utilisateur est un admin, il peut voir tous les utilisateurs
        if ($_SESSION['role'] === 'admin') {
            $utilisateurs = $repository->getAll();
        } else {
            // Sinon, ne récupérer que lui-même
            $utilisateurId = $_SESSION['utilisateur_id'];
            $utilisateur = $repository->select($utilisateurId);
            $utilisateurs = $utilisateur ? [$utilisateur] : [];
        }
    
        // Affiche la vue avec les utilisateurs filtrés
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
    
            if (!empty($nom) && !empty($prenom) && !empty($email) && !empty($motDePasse)) {
                // Hachage sécurisé du mot de passe
                $motDePasseHash = password_hash($motDePasse, PASSWORD_DEFAULT);
    
                // Log pour vérifier le hachage
                error_log("Mot de passe haché : $motDePasseHash");
    
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
                    MessageFlash::ajouter('success', "Inscription effectuée !");
                    header('Location: /Web/frontController.php');
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
            // Afficher le formulaire de connexion
            self::afficheVue('view.php', [
                'pagetitle' => 'CONNEXION',
                'cheminVueBody' => 'utilisateur/authentification.php'
            ]);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
                $motDePasse = $_POST['motdepasse'] ?? '';
    
                if (empty($email) || empty($motDePasse)) {
                    throw new \Exception("Veuillez remplir tous les champs.");
                }
    
                $repository = new UtilisateurRepository();
                $utilisateur = $repository->selectByEmail($email);
    
                if (!$utilisateur) {
                    throw new \Exception("Aucun utilisateur trouvé avec cet email.");
                }
    
                // Log le mot de passe haché récupéré
                error_log("Mot de passe haché récupéré : " . $utilisateur->getMotDePasse());
    
                if (!password_verify($motDePasse, $utilisateur->getMotDePasse())) {
                    throw new \Exception("Mot de passe incorrect.");
                }
    
                // Initialiser la session utilisateur
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
    
                $_SESSION['utilisateur_id'] = $utilisateur->getId();
                $_SESSION['nom'] = $utilisateur->getNom();
                $_SESSION['prenom'] = $utilisateur->getPrenom();
                $_SESSION['role'] = $utilisateur->getRole();
    
                MessageFlash::ajouter('success', "Bienvenue, " . htmlspecialchars($utilisateur->getPrenom()) . " !");
                header('Location: /Web/frontController.php');
                exit();
            } catch (\Exception $e) {
                MessageFlash::ajouter('error', $e->getMessage());
                self::afficheVue('view.php', [
                    'pagetitle' => 'CONNEXION',
                    'cheminVueBody' => 'utilisateur/authentification.php'
                ]);
            }
        }
    }                   

    public static function deconnexion() {
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
        $id = $_GET['id'] ?? null;

        // Seul un compte admin peut changer le role d'un utilisateur
        if ($_SESSION['role'] !== 'admin') {
            \App\Meteo\Lib\MessageFlash::ajouter('error', "Vous n'avez pas les permissions pour modifier les rôles.");
            header('Location: /Web/frontController.php?action=readAll&controller=utilisateur');
            exit();
        }        
    
        // Si la requête est GET, afficher le formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && $id) {
            $repository = new UtilisateurRepository();
            $utilisateur = $repository->select($id);
    
            if ($utilisateur) {
                self::afficheVue('view.php', [
                    'utilisateur' => $utilisateur,
                    'pagetitle' => "Changer le rôle d'un utilisateur",
                    'cheminVueBody' => 'utilisateur/changerRole.php'
                ]);
            } else {
                MessageFlash::ajouter('error', "Utilisateur introuvable.");
                header('Location: /Web/frontController.php?action=readAll&controller=utilisateur');
                exit();
            }
        }
        // Si la requête est POST, traiter la soumission du formulaire
        elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $nouveauRole = $_POST['role'] ?? null;
    
            if (!$id || !$nouveauRole) {
                MessageFlash::ajouter('error', "ID ou rôle manquant.");
                header('Location: /Web/frontController.php?action=readAll&controller=utilisateur');
                exit();
            }
    
            $repository = new UtilisateurRepository();
            $utilisateur = $repository->select($id);
    
            if (!$utilisateur) {
                MessageFlash::ajouter('error', "Utilisateur introuvable.");
                header('Location: /Web/frontController.php?action=readAll&controller=utilisateur');
                exit();
            }
    
            // Modifier le rôle dans l'objet utilisateur
            $utilisateur->setRole($nouveauRole);
    
            // Mettre à jour l'utilisateur dans la base de données
            if ($repository->update($utilisateur)) {
                MessageFlash::ajouter('success', "Rôle mis à jour avec succès.");
            } else {
                MessageFlash::ajouter('error', "Erreur lors de la mise à jour du rôle.");
            }
    
            header('Location: /Web/frontController.php?action=readAll&controller=utilisateur');
            exit();
        }
    }               

    public static function afficheVue(string $cheminVue, array $parametres = []): void {
        extract($parametres); // Crée des variables à partir du tableau $parametres
        require __DIR__ . '/../view/' . $cheminVue; // Charge la vue spécifiée
    }
}
