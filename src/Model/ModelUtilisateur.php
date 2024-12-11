<?php

namespace App\Meteo\Model;

use App\Meteo\Config\Conf;
use \PDOException;

class ModelUtilisateur {
    // Attributs correspondant aux colonnes de la table "utilisateurs"
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

    // Getters
    public function getId(): int {
        return $this->utilisateurId;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function getPrenom(): string {
        return $this->prenom;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getMotDePasse(): string {
        return $this->motDePasse;
    }

    public function getRole(): string {
        return $this->role;
    }

    public function estAdministrateur(): bool {
        return $this->role === 'administrateur';
    }

    // Reconstruire un utilisateur à partir d'un tableau associatif
    public static function construire(array $data): ModelUtilisateur {
        return new self(
            $data['utilisateur_id'],
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['mot_de_passe'],
            $data['date_creation'],
            $data['role'],
            $data['etat_compte']
        );
    }

    // Récupérer tous les utilisateurs
    public static function getUtilisateurs(): array {
        try {
            $sql = "SELECT * FROM utilisateurs";
            $pdo = Conf::getPDO();
            $stmt = $pdo->query($sql);

            $utilisateurs = [];
            foreach ($stmt as $row) {
                $utilisateurs[] = self::construire($row);
            }
            return $utilisateurs;
        } catch (PDOException $e) {
            throw new \Exception("Erreur lors de la récupération des utilisateurs : " . $e->getMessage());
        }
    }

    // Récupérer un utilisateur par ID
    public static function getUtilisateurByID(int $id): ?ModelUtilisateur {
        try {
            $sql = "SELECT * FROM utilisateurs WHERE utilisateur_id = :id";
            $pdo = Conf::getPDO();
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id' => $id]);

            $data = $stmt->fetch();
            return $data ? self::construire($data) : null;
        } catch (PDOException $e) {
            throw new \Exception("Erreur lors de la récupération de l'utilisateur : " . $e->getMessage());
        }
    }

    // Récupérer un utilisateur par email
    public static function getUtilisateurByEmail(string $email): ?ModelUtilisateur {
        try {
            $sql = "SELECT * FROM utilisateurs WHERE email = :email";
            $pdo = Conf::getPDO();
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['email' => $email]);

            $data = $stmt->fetch();
            return $data ? self::construire($data) : null;
        } catch (PDOException $e) {
            throw new \Exception("Erreur lors de la récupération de l'utilisateur par email : " . $e->getMessage());
        }
    }

    // Enregistrer un utilisateur
    public function sauvegarder(): bool {
        try {
            $sql = "INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role, etat_compte) 
                    VALUES (:nom, :prenom, :email, :mot_de_passe, :role, :etat_compte)";
            $pdo = Conf::getPDO();
            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                'nom' => $this->nom,
                'prenom' => $this->prenom,
                'email' => $this->email,
                'mot_de_passe' => $this->motDePasse,
                'role' => $this->role,
                'etat_compte' => $this->etatCompte
            ]);

            return true;
        } catch (PDOException $e) {
            throw new \Exception("Erreur lors de l'insertion : " . $e->getMessage());
        }
    }

    // Supprimer un utilisateur par ID
    public static function deleteByID(int $id): bool {
        try {
            $sql = "DELETE FROM utilisateurs WHERE utilisateur_id = :id";
            $pdo = Conf::getPDO();
            $stmt = $pdo->prepare($sql);

            $stmt->execute(['id' => $id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new \Exception("Erreur lors de la suppression : " . $e->getMessage());
        }
    }
}
