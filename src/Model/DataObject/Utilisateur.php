<?php

namespace App\Meteo\Model\DataObject;

class Utilisateur {
    private int $utilisateurId;
    private string $nom;
    private string $prenom;
    private string $email;
    private string $motDePasse;
    private string $dateCreation;
    private string $role;
    private string $etatCompte;

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
        $this->motDePasse = password_hash($motDePasse, PASSWORD_DEFAULT); // Hash sécurisé
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

    public function getEtatCompte(): string {
        return $this->etatCompte;
    }

    public function getDateCreation(): string {
        return $this->dateCreation;
    }

    // Format en tableau pour usage dans les requêtes SQL
    public function formatTableau(): array {
        return [
            'utilisateur_id' => $this->utilisateurId,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'email' => $this->email,
            'mot_de_passe' => $this->motDePasse,
            'role' => $this->role,
            'etat_compte' => $this->etatCompte,
            'date_creation' => $this->dateCreation,
        ];
    }
}
