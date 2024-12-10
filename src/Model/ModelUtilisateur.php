<?php

namespace App\Meteo\Model;

use App\Meteo\Config\Conf;
use \PDOException;


class ModelUtilisateur {
    // Attributs privés représentant les colonnes de la table "utilisateurs"
    private int $utilisateurId;
    private string $nom;
    private string $prenom;
    private string $email;
    private string $motDePasse;
    private string $dateCreation;
    private string $role;
    private string $etatCompte;

    // Constructeur pour initialiser un utilisateur
    public function __construct(
        int $utilisateurId,
        string $nom,
        string $prenom,
        string $email,
        string $motDePasse,
        string $dateCreation,
        string $role = 'utilisateur',
        string $etatCompte = 'en_attente'
    ) {
        $this->utilisateurId = $utilisateurId;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->motDePasse = $motDePasse;
        $this->dateCreation = $dateCreation;
        $this->role = $role;
        $this->etatCompte = $etatCompte;
    }
    

    // ========================
    // Getters pour les attributs
    // ========================
    public function getRole(): string {
        return $this->role;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getMotDePasse(): string {
        return $this->motDePasse;
    }

    // Méthode pour vérifier si l'utilisateur est un administrateur
    public function estAdministrateur(): bool {
        return $this->role === 'administrateur';
    }

    // ========================
    // Méthodes statiques
    // ========================

    /**
     * Reconstruit un objet utilisateur à partir d'un tableau associatif
     */
    public static function construire(array $utilisateurFormatTableau): ModelUtilisateur {
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

    /**
     * Récupère tous les utilisateurs de la base de données
     */
    public static function getUtilisateurs(): array {
        try {
            $sql = "SELECT * FROM utilisateurs";
            $pdo = Conf::getPDO();
            $pdoStatement = $pdo->query($sql);

            $utilisateurs = [];
            foreach ($pdoStatement as $utilisateurFormatTableau) {
                $utilisateurs[] = ModelUtilisateur::construire($utilisateurFormatTableau);
            }
            return $utilisateurs;
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération des utilisateurs : " . $e->getMessage();
            return [];
        }
    }

    /**
     * Récupère un utilisateur par son ID
     */
    public static function getUtilisateurByID(int $utilisateurId): ?ModelUtilisateur {
        try {
            $sql = "SELECT * FROM utilisateurs WHERE utilisateur_id = :utilisateurId";
            $pdo = Conf::getPDO();
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['utilisateurId' => $utilisateurId]);

            $utilisateur = $stmt->fetch();
            if ($utilisateur === false) {
                return null;
            }

            return static::construire($utilisateur);
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération de l'utilisateur : " . $e->getMessage();
            return null;
        }
    }

    /**
     * Récupère un utilisateur par son email
     */
    public static function getUtilisateurByEmail(string $email): ?ModelUtilisateur {
        try {
            $sql = "SELECT * FROM utilisateurs WHERE email = :email";
            $pdo = Conf::getPDO();
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['email' => $email]);

            $utilisateur = $stmt->fetch();
            if ($utilisateur === false) {
                return null;
            }

            return static::construire($utilisateur);
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération de l'utilisateur par email : " . $e->getMessage();
            return null;
        }
    }

    // ========================
    // Méthodes d'instance
    // ========================

    /**
     * Enregistre un utilisateur dans la base de données
     */
    public function sauvegarder(): bool {
        try {
            $sql = "INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role, etat_compte) 
                    VALUES (:nom, :prenom, :email, :motDePasse, :role, :etatCompte)";
            $pdo = Conf::getPDO();
            $stmt = $pdo->prepare($sql);
    
            $values = [
                'nom' => $this->nom,
                'prenom' => $this->prenom,
                'email' => $this->email,
                'motDePasse' => $this->motDePasse,
                'role' => $this->role,
                'etatCompte' => $this->etatCompte
            ];
    
            // Debugging pour la requête et les valeurs
            echo "Requête SQL : $sql<br>";
            echo "Valeurs : ";
            print_r($values);
            echo "<br>";
    
            $stmt->execute($values);
            return true;
        } catch (\PDOException $e) {
            echo "Erreur lors de l'insertion : " . $e->getMessage() . "<br>";
            return false;
        }
    }
    
    
    
    /**
     * Modifie un utilisateur existant
     */
    public function modifier(): bool {
        try {
            $sql = "UPDATE utilisateurs 
                    SET nom = :nom, 
                        email = :email, 
                        mot_de_passe = :motDePasse, 
                        date_creation = :dateCreation, 
                        role = :role, 
                        etat_compte = :etatCompte 
                    WHERE utilisateur_id = :utilisateurId";

            $pdo = Conf::getPDO();
            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                'nom' => $this->nom,
                'email' => $this->email,
                'motDePasse' => $this->motDePasse,
                'dateCreation' => $this->dateCreation,
                'role' => $this->role,
                'etatCompte' => $this->etatCompte,
                'utilisateurId' => $this->utilisateurId
            ]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            echo "Erreur lors de la modification de l'utilisateur : " . $e->getMessage();
            return false;
        }
    }

    /**
     * Supprime un utilisateur par son ID
     */
    public static function deleteByID(int $utilisateurId): bool {
        try {
            $sql = "DELETE FROM utilisateurs WHERE utilisateur_id = :utilisateurId";
            $pdo = Conf::getPDO();
            $stmt = $pdo->prepare($sql);

            $stmt->execute(['utilisateurId' => $utilisateurId]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            echo "Erreur lors de la suppression de l'utilisateur : " . $e->getMessage();
            return false;
        }
    }
}
