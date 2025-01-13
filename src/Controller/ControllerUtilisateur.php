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
                'cheminVueBody' => "utilisateur/inscription.php"
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
                'cheminVueBody' => "utilisateur/inscription.php"
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
            $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
            $motDePasse = $_POST['motdepasse'] ?? '';

            if ($email && $motDePasse) {
                // Vérifie si l'utilisateur existe
                $repository = new UtilisateurRepository();
                $utilisateur = $repository->select($email); // Suppose que l'email est utilisé comme clé primaire

                if ($utilisateur) {
                    // Vérifie le mot de passe
                    if (password_verify($motDePasse, $utilisateur->getMotDePasse())) {
                        // Définir une session pour l'utilisateur
                        session_start();
                        $_SESSION['utilisateur_id'] = $utilisateur->getId();
                        $_SESSION['nom'] = $utilisateur->getNom();

                        MessageFlash::ajouter('success', "Connexion réussie. Bienvenue, " . htmlspecialchars($utilisateur->getNom()) . " !");
                        header('Location: /Web/frontController.php');
                        exit();
                    } else {
                        MessageFlash::ajouter('error', "Mot de passe incorrect.");
                    }
                } else {
                    MessageFlash::ajouter('error', "Aucun utilisateur trouvé avec cet email.");
                }
            } else {
                MessageFlash::ajouter('error', "Email ou mot de passe non fourni.");
            }

            // Réaffiche le formulaire avec les messages flash
            self::afficheVue('view.php', [
                'pagetitle' => 'CONNEXION',
                'cheminVueBody' => 'utilisateur/authentification.php'
            ]);
        }
    }

    public static function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['utilisateur_id'] ?? null;
            $nom = htmlspecialchars($_POST['nom']);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
            if ($id && $nom && $email) {
                $repository = new UtilisateurRepository();
                $utilisateur = new Utilisateur($id, $nom, '', $email, '', date('Y-m-d H:i:s'));
    
                if ($repository->update($utilisateur)) {
                    MessageFlash::ajouter('success', "Utilisateur modifié avec succès.");
                } else {
                    MessageFlash::ajouter('error', "Échec de la modification.");
                }
            }
        }
        header('Location: /Web/frontController.php?action=readAll&controller=utilisateur');
        exit;
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
        }
        header('Location: /Web/frontController.php?action=readAll&controller=utilisateur');
        exit;
    }    

    public static function afficheVue(string $cheminVue, array $parametres = []): void {
        extract($parametres); // Crée des variables à partir du tableau $parametres
        require __DIR__ . '/../view/' . $cheminVue; // Charge la vue spécifiée
    }
}
