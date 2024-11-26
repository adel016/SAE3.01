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

    public function __construct(int $utilisateurId, string $nom, string $email, string $motDePasse, string $dateCreation) {
        $this->utilisateurId = $utilisateurId;
        $this->nom = $nom;
        $this->email = $email;
        $this->motDePasse = $motDePasse;
        $this->dateCreation = $dateCreation;
    }

    public static function construire(array $utilisateurFormatTableau) : ModelUtilisateur {
        return new ModelUtilisateur(
            $utilisateurFormatTableau['utilisateur_id'],
            $utilisateurFormatTableau['nom'],
            $utilisateurFormatTableau['email'],
            $utilisateurFormatTableau['mot_de_passe'],
            $utilisateurFormatTableau['date_creation']
        );
    }

    public static function getUtilisateurs() {
        $pdo = Model::getPdo();
        $sql = "SELECT * FROM Utilisateurs";
        $pdoStatement = $pdo->query($sql);

        $utilisateurs = [];
        foreach ($pdoStatement as $utilisateurFormatTableau) {
            $utilisateurs[] = ModelUtilisateur::construire($utilisateurFormatTableau);
        }
        return $utilisateurs;
    }

    public function sauvegarder() : bool {
        try {
            $sql = "INSERT INTO Utilisateurs (nom, email, mot_de_passe, date_creation) 
                    VALUES (:nom, :email, :motDePasse, :dateCreation)";
            $pdoStatement = Model::getPdo()->prepare($sql);
            $values = array(
                'nom' => $this->nom,
                'email' => $this->email,
                'motDePasse' => $this->motDePasse,
                'dateCreation' => $this->dateCreation
            );
            $pdoStatement->execute($values);
            return true;
        } catch (PDOException $e) {
            echo "Erreur lors de la sauvegarde : " . $e->getMessage();
            return false;
        }
    }
}
?>
