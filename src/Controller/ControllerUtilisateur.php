<?php

namespace App\Meteo\Controller;

use App\Meteo\Model\DataRepository\UtilisateurRepository;
use App\Meteo\Model\DataRepository\MeteothequeRepository;
use App\Meteo\Model\DataRepository\LogRepository;
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
            header('Location: /SAE3.01/Web/frontController.php?action=connexion&controller=utilisateur');
            exit();
        }

        // Récupère les informations de l'utilisateur
        $repository = new UtilisateurRepository();
        $utilisateurId = $_SESSION['utilisateur_id'];
        $utilisateur = $repository->select($utilisateurId);

        if (!$utilisateur) {
            MessageFlash::ajouter('error', "Utilisateur introuvable.");
            header('Location: /SAE3.01/Web/frontController.php?action=connexion&controller=utilisateur');
            exit();
        }

        // Récupère les requêtes Meteothèque de l'utilisateur
        $meteothequeRepo = new MeteothequeRepository();
        $requetes = $meteothequeRepo->findByUserId($utilisateurId);

        // Affiche la vue avec les infos utilisateur et sa Meteothèque
        self::afficheVue('view.php', [
            'utilisateur' => $utilisateur,
            'requetes' => $requetes,
            'pagetitle' => "Profil utilisateur",
            'cheminVueBody' => "utilisateur/list.php"
        ]);
    }

    public static function inscription() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Afficher le formulaire d'inscription (inchangé)
            self::afficheVue('view.php', [
                'pagetitle' => "Inscription",
                'cheminVueBody' => "utilisateur/authentification.php"
            ]);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = htmlspecialchars($_POST['nom'] ?? '');
            $prenom = htmlspecialchars($_POST['prenom'] ?? '');
            $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
            $motDePasse = $_POST['motdepasse'] ?? '';
    
            if (!empty($nom) && !empty($prenom) && !empty($email) && !empty($motDePasse)) {    
                $utilisateur = new Utilisateur(
                    0,
                    $nom,
                    $prenom,
                    $email,
                    $motDePasse,
                    date('Y-m-d H:i:s')
                );
    
                $repository = new UtilisateurRepository();
                if ($repository->sauvegarder($utilisateur)) {
                    // Inscription réussie, maintenant connectons l'utilisateur
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }
    
                    // Récupérer l'ID de l'utilisateur nouvellement créé
                    $nouvelUtilisateur = $repository->selectByEmail($email);
    
                    $_SESSION['utilisateur_id'] = $nouvelUtilisateur->getId();
                    $_SESSION['nom'] = $nouvelUtilisateur->getNom();
                    $_SESSION['prenom'] = $nouvelUtilisateur->getPrenom();
                    $_SESSION['role'] = $nouvelUtilisateur->getRole();

                    // Ajouter une entrée dans les logs pour l'inscription
                    $logRepository = new LogRepository();
                    $logRepository->addLog($nouvelUtilisateur->getId(), 'inscription');
    
                    MessageFlash::ajouter('success', "Inscription réussie ! Vous êtes maintenant connecté.");
                    header('Location: /SAE3.01/Web/frontController.php');
                    exit();
                } else {
                    MessageFlash::ajouter('error', "Erreur lors de l'inscription.");
                }
            } else {
                MessageFlash::ajouter('error', "Tous les champs sont obligatoires.");
            }
        }
        // Si on arrive ici, c'est qu'il y a eu une erreur
        self::afficheVue('view.php', [
            'pagetitle' => "Inscription",
            'cheminVueBody' => "utilisateur/authentification.php"
        ]);
    }    
       
    public static function connexion() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Affiche le formulaire de connexion
            self::afficheVue('view.php', [
                'pagetitle' => 'CONNEXION',
                'cheminVueBody' => 'utilisateur/authentification.php'
            ]);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
            $motDePasse = trim($_POST['motdepasse'] ?? '');
        
            try {
                if (empty($email) || empty($motDePasse)) {
                    throw new \Exception("Veuillez remplir tous les champs.");
                }
    
                $repository = new UtilisateurRepository();
                $utilisateur = $repository->selectByEmail($email);
    
                if (!$utilisateur) {
                    throw new \Exception("Aucun utilisateur trouvé avec cet email.");
                }
    
                $motDePasse = $utilisateur->getMotDePasse();
    
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
    
                $_SESSION['utilisateur_id'] = $utilisateur->getId();
                $_SESSION['nom'] = $utilisateur->getNom();
                $_SESSION['prenom'] = $utilisateur->getPrenom();
                $_SESSION['role'] = $utilisateur->getRole();

                // Ajouter une entrée dans les logs pour la connexion
                $logRepository = new LogRepository();
                $logRepository->addLog($utilisateur->getId(), 'connexion');
    
                MessageFlash::ajouter('success', "Bienvenue, " . htmlspecialchars($utilisateur->getPrenom()) . " !");
                header('Location: /SAE3.01/Web/frontController.php');
                exit();
            } catch (\Exception $e) {
                MessageFlash::ajouter('error', $e->getMessage());
                self::afficheVue('view.php', [
                    'pagetitle' => 'CONNEXION',
                    'cheminVueBody' => 'utilisateur/authentification.php'
                ]);
            }
        } else {
            self::afficheVue('view.php', [
                'pagetitle' => 'CONNEXION',
                'cheminVueBody' => 'utilisateur/authentification.php'
            ]);
        }
    }                      

    public static function deconnexion() {
        session_unset(); // Supprime toutes les variables de session
        session_destroy(); // Détruit la session
        MessageFlash::ajouter('success', "Revenez-nous voir !");
        header('Location: /SAE3.01/Web/frontController.php'); // Redirige vers la page d'accueil
        exit();
    }  

#################################
######### PARTIE ADMIN ##########
#################################

    public static function update() {
        $id = $_GET['id'] ?? null;

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
                header('Location: /SAE3.01/Web/frontController.php?action=readAll&controller=utilisateur');
                exit();
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
            $nom = htmlspecialchars($_POST['nom'] ?? '');
            $prenom = htmlspecialchars($_POST['prenom'] ?? '');
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

            if ($nom && $prenom && $email) {
                $repository = new UtilisateurRepository();
                $utilisateur = $repository->select($id);

                if ($utilisateur) {
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

                    $logRepository = new LogRepository();
                    $logRepository->addLog($id, 'modification', "Modification des informations de l'utilisateur");

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

            header('Location: /SAE3.01/Web/frontController.php?action=readAll&controller=utilisateur');
            exit();
        }
    }

    public static function delete() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $repository = new UtilisateurRepository();
            $meteothequeRepository = new MeteothequeRepository();
            $logRepository = new LogRepository();
    
            // Supprimer les entrées dans la table `meteotheques` associées à l'utilisateur
            $meteothequeRepository->delete($id);
    
            // Vérifie si l'utilisateur connecté est bien celui qu'il souhaite supprimer
            if (isset($_SESSION['utilisateur_id']) && $_SESSION['utilisateur_id'] == $id) {
                if ($repository->delete($id)) {    
                    self::deconnexion();
                    MessageFlash::ajouter('success', "Votre compte a été supprimé avec succès.");
    
                    // Ajouter un log pour la suppression
                    $logRepository->addLog($id, 'suppression', "Suppression de l'utilisateur par lui-même");
    
                    header('Location: /SAE3.01/Web/frontController.php');
                    exit;
                } else {
                    MessageFlash::ajouter('error', "Échec de la suppression du compte.");
                }
            } else {
                MessageFlash::ajouter('error', "Vous ne pouvez pas supprimer ce compte.");
            }
        } else {
            MessageFlash::ajouter('error', "Utilisateur introuvable !");
        }
    
        header('Location: /SAE3.01/Web/frontController.php');
        exit();
    }                      

    public static function afficheVue(string $cheminVue, array $parametres = []): void {
        extract($parametres); // Crée des variables à partir du tableau $parametres
        require __DIR__ . '/../view/' . $cheminVue; // Charge la vue spécifiée
    }
}

?>