<?php

namespace App\Meteo\Model\DataObject;

class Meteotheque {
    private int $meteoId;
    private int $utilisateurId;
    private string $nomCollection;
    private string $description;
    private string $dateCreation;

    public function __construct(int $meteoId, int $utilisateurId, string $nomCollection, string $description, string $dateCreation) {
        $this->meteoId = $meteoId;
        $this->utilisateurId = $utilisateurId;
        $this->nomCollection = $nomCollection;
        $this->description = $description;
        $this->dateCreation = $dateCreation;
    }

    // Getters
    public function getMeteoId(): int {
        return $this->meteoId;
    }

    public function getUtilisateurId(): int {
        return $this->utilisateurId;
    }

    public function getNomCollection(): string {
        return $this->nomCollection;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getDateCreation(): string {
        return $this->dateCreation;
    }

    // Format en tableau pour usage dans les requêtes SQL
    public function formatTableau(): array {
        return [
            'meteo_id' => $this->meteoId,
            'utilisateur_id' => $this->utilisateurId,
            'nom_collection' => $this->nomCollection,
            'description' => $this->description,
            'date_creation' => $this->dateCreation,
        ];
    }
}
