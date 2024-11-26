<?php

namespace App\Covoiturage\Model;

use App\Covoiturage\Model\Model;
use \PDOException;

class ModelUtilisateur {
    private int $utilisateurId;
    private string $nom;
    private string $email;
    private string $motDePasse;
    private string $dateCreation;
    private string $role;
    private string $etatCompte;

    public function __construct(int $utilisateurId, string $nom, string $email, string $motDePasse, string $dateCreation, string $role = 'utilisateur', string $etatCompte = 'en_attente') {
        $this->utilisateurId = $utilisateurId;
        $this->nom = $nom;
        $this->email = $email;
        $this->motDePasse = $motDePasse;
        $this->dateCreation = $dateCreation;
        $this->role = $role;
        $this->etatCompte = $etatCompte;
    }

    public function getRole() : string {
        return $this->role;
    }

    public function estAdministrateur() : bool {
        return $this->role === 'administrateur';
    }

    public static function construire(array $utilisateurFormatTableau) : ModelUtilisateur {
        return new ModelUtilisateur(
            $utilisateurFormatTableau['utilisateur_id'],
            $utilisateurFormatTableau['nom'],
            $utilisateurFormatTableau['email'],
            $utilisateurFormatTableau['mot_de_passe'],
            $utilisateurFormatTableau['date_creation'],
            $utilisateurFormatTableau['role'],
            $utilisateurFormatTableau['etat_compte']
        );
    }

    public static function getUtilisateurs() : array {
        try {
            $sql = "SELECT * FROM Utilisateurs";
            $pdo = Model::getPdo();
            $pdoStatement = $pdo->query($sql);

            // Creation d'un tableau pour stocker toutes les voitures
            $utilisateurs = [];
            foreach ($pdoStatement as $voitureFormatTableau) {
                $utilisateurs[] = ModelUtilisateur::construire($voitureFormatTableau) ;
            }
            return $utilisateurs;

        } catch (PDOException $e) {
            echo "Erreur lors de la recuperation de donnees : " . $e->getMessage();
        }
    }

    public static function getUtilisateurByID(string $utilisateur_id) : ?ModelUtilisateur {
        $sql = "SELECT * FROM Utilisateurs WHERE utilisateur_id = :utilisateurID";

        // Préparation de la requête
        $pdoStatement = Model::getPdo()->prepare($sql);
        $values = array(
            "utilisateurID" => $utilisateur_id
        );
        
        // On donne les valeurs et on exécute la requête
        $pdoStatement->execute($values);

        // On récupère les résultats comme précédemment
        // Note: fetch() renvoie false si pas de voiture correspondante
        $utilisateur = $pdoStatement->fetch();

        if ($utilisateur === false) {
            return null;
        }

        return static::construire($utilisateur);
    }

    public function sauvegarder() : bool {
        try {
            $sql = "INSERT INTO Utilisateurs (nom, email, mot_de_passe, date_creation, role, etat_compte) 
                    VALUES (:nom, :email, :motDePasse, :dateCreation, :role, :etatCompte)";
            $pdoStatement = Model::getPdo()->prepare($sql);
            $values = array(
                'nom' => $this->nom,
                'email' => $this->email,
                'motDePasse' => $this->motDePasse,
                'dateCreation' => $this->dateCreation,
                'role' => $this->role ?? 'utilisateur',
                'etatCompte' => $this->etatCompte ?? 'en_attente'
            );
            $pdoStatement->execute($values);
            return true;
        } catch (PDOException $e) {
            echo "Erreur lors de la sauvegarde : " . $e->getMessage();
            return false;
        }
    }

    public function changerEtatCompte(string $nouvelEtat) : bool {
        $etatsValides = ['actif', 'en_attente', 'suspendu'];
        if (!in_array($nouvelEtat, $etatsValides)) {
            echo "Erreur : État du compte invalide.";
            return false;
        }

        try {
            $sql = "UPDATE Utilisateurs SET etat_compte = :etat WHERE utilisateur_id = :utilisateurId";

            $pdoStatement = Model::getPdo()->prepare($sql);
            $values = array(
                'etat' => $nouvelEtat,
                'utilisateurId' => $this->utilisateurId
            );

            $pdoStatement->execute($values);

            $this->enregistrerLog("Changement de l'état du compte utilisateur à '{$nouvelEtat}'");
            return $pdoStatement->rowCount() > 0;

        } catch (PDOException $e) {
            echo "Erreur lors du changement d'état du compte : " . $e->getMessage();
            return false;
        }
    }

    public function modifier() : bool {
        try {
            $sql = "UPDATE Utilisateurs 
                    SET nom = :nom, 
                        email = :email, 
                        mot_de_passe = :motDePasse, 
                        date_creation = :dateCreation 
                    WHERE utilisateur_id = :utilisateurId";

            $pdoStatement = Model::getPdo()->prepare($sql);

            $values = array(
                'nom' => $this->nom,
                'email' => $this->email,
                'motDePasse' => $this->motDePasse,
                'dateCreation' => $this->dateCreation,
                'utilisateurId' => $this->utilisateurId
            );

            $pdoStatement->execute($values);
            return $pdoStatement->rowCount() > 0;

        } catch (PDOException $e) {
            echo "Erreur lors de la modification : " . $e->getMessage();
            return false;
        }
    }

    public static function deleteByID(int $utilisateurId) : bool {
        try {
            $sql = "DELETE FROM Utilisateurs WHERE utilisateur_id = :utilisateurId";
            $pdoStatement = Model::getPdo()->prepare($sql);
    
            $values = ['utilisateurId' => $utilisateurId];
            $pdoStatement->execute($values);
    
            return $pdoStatement->rowCount() > 0;
        } catch (PDOException $e) {
            echo "Erreur lors de la suppression : " . $e->getMessage();
            return false;
        }
    }
    

    private function enregistrerLog(string $action) {
        try {
            $sql = "INSERT INTO Logs (utilisateur_id, action, timestamp) VALUES (:utilisateurId, :action, NOW())";
            $pdoStatement = Model::getPdo()->prepare($sql);
            $values = array(
                'utilisateurId' => $this->utilisateurId,
                'action' => $action
            );
            $pdoStatement->execute($values);
        } catch (PDOException $e) {
            echo "Erreur lors de l'enregistrement dans les logs : " . $e->getMessage();
        }
    }
}
?>
