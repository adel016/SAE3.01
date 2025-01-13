<?php

namespace App\Meteo\Controller;

use App\Meteo\Model\DataObject\Meteotheque;
use App\Meteo\Model\DataRepository\MeteothequeRepository;
use App\Meteo\Lib\MessageFlash;

class ControllerMeteotheque {
    
    // Affiche toutes les Meteotheques accessibles à l'utilisateur ou aux admins
    public static function readAll() : void {
        session_start();
        $userId = $_SESSION['utilisateur_id'] ?? null;
        $role = $_SESSION['role'] ?? null;

        if (!$userId) {
            MessageFlash::ajouter('error', "Vous devez être connecté pour voir les collections météo.");
            header("Location: /Web/frontController.php?action=connexion&controller=utilisateur");
            exit();
        }

        $repository = new MeteothequeRepository();
        if ($role === 'admin') {
            $meteotheques = $repository->getAll(); // Les admins voient toutes les meteotheques
        } else {
            $meteotheques = $repository->select($userId); // Utilisateur normal ne voit que sa meteotheque
        }

        self::afficheVue('view.php', [
            'pagetitle' => "Vos Meteotheques",
            'cheminVueBody' => "meteotheque/list.php",
            'meteotheques' => $meteotheques
        ]);
    }

    // Affiche le formulaire d'ajout d'une Meteotheque
    public static function create() : void {
        self::afficheVue('view.php', [
            'pagetitle' => "Créer une Meteotheque",
            'cheminVueBody' => "meteotheque/create.php"
        ]);
    }

    // Traite l'ajout d'une nouvelle Meteotheque
    public static function created() : void {
        session_start();
        $userId = $_SESSION['utilisateur_id'] ?? null;

        if ($userId && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = htmlspecialchars($_POST['nom_collection']);
            $description = htmlspecialchars($_POST['description']);
            $date = date('Y-m-d H:i:s');

            $meteotheque = new Meteotheque(0, $userId, $nom, $description, $date);
            $repository = new MeteothequeRepository();

            if ($repository->sauvegarder($meteotheque)) {
                MessageFlash::ajouter('success', "Meteotheque créée avec succès.");
                header("Location: /Web/frontController.php?action=readAll&controller=meteotheque");
                exit();
            } else {
                MessageFlash::ajouter('error', "Erreur lors de la création de la Meteotheque.");
            }
        }

        self::create();
    }

    // Supprime une Meteotheque (vérifie que l'utilisateur est propriétaire)
    public static function delete() : void {
        session_start();
        $userId = $_SESSION['utilisateur_id'] ?? null;
        $role = $_SESSION['role'] ?? null;

        $meteoId = $_GET['id'] ?? null;
        if ($meteoId) {
            $repository = new MeteothequeRepository();
            $meteotheque = $repository->select($meteoId);

            if ($meteotheque && ($meteotheque->getUtilisateurId() === $userId || $role === 'admin')) {
                if ($repository->delete($meteoId)) {
                    MessageFlash::ajouter('success', "Meteotheque supprimée avec succès.");
                } else {
                    MessageFlash::ajouter('error', "Erreur lors de la suppression.");
                }
            } else {
                MessageFlash::ajouter('error', "Vous n'avez pas le droit de supprimer cette Meteotheque.");
            }
        } else {
            MessageFlash::ajouter('error', "Aucune Meteotheque spécifiée.");
        }

        header("Location: /Web/frontController.php?action=readAll&controller=meteotheque");
        exit();
    }

    // Modifie une Meteotheque (propriétaire uniquement)
    public static function update() : void {
        session_start();
        $userId = $_SESSION['utilisateur_id'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
            $repository = new MeteothequeRepository();
            $meteotheque = $repository->select($_GET['id']);

            if ($meteotheque && $meteotheque->getUtilisateurId() === $userId) {
                self::afficheVue('view.php', [
                    'pagetitle' => "Modifier la Meteotheque",
                    'cheminVueBody' => "meteotheque/update.php",
                    'meteotheque' => $meteotheque
                ]);
                return;
            } else {
                MessageFlash::ajouter('error', "Vous n'avez pas le droit de modifier cette Meteotheque.");
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $repository = new MeteothequeRepository();
            $id = $_POST['meteo_id'] ?? null;
            $nom = htmlspecialchars($_POST['nom_collection']);
            $description = htmlspecialchars($_POST['description']);

            $meteotheque = $repository->select($id);
            if ($meteotheque && $meteotheque->getUtilisateurId() === $userId) {
                $updatedMeteotheque = new Meteotheque($id, $userId, $nom, $description, $meteotheque->getDateCreation());

                if ($repository->update($updatedMeteotheque)) {
                    MessageFlash::ajouter('success', "Meteotheque mise à jour avec succès.");
                } else {
                    MessageFlash::ajouter('error', "Erreur lors de la mise à jour.");
                }
            } else {
                MessageFlash::ajouter('error', "Vous n'avez pas le droit de modifier cette Meteotheque.");
            }
        }
        header("Location: /Web/frontController.php?action=readAll&controller=meteotheque");
        exit();
    }

    // Méthode pour afficher une vue
    private static function afficheVue(string $cheminVue, array $parametres = []) : void {
        extract($parametres);
        require __DIR__ . '/../view/' . $cheminVue;
    }
}
?>