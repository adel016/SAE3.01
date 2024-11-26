<?php

namespace App\Covoiturage\Model;

use App\Covoiturage\Model\Model;
use \PDOException;

class ModelMeteotheque {
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

    public static function construire(array $meteothequeFormatTableau) : ModelMeteotheque {
        return new ModelMeteotheque(
            $meteothequeFormatTableau['meteo_id'],
            $meteothequeFormatTableau['utilisateur_id'],
            $meteothequeFormatTableau['nom_collection'],
            $meteothequeFormatTableau['description'],
            $meteothequeFormatTableau['date_creation']
        );
    }

    public static function getMeteotheques() {
        $pdo = Model::getPdo();
        $sql = "SELECT * FROM Meteotheques";
        $pdoStatement = $pdo->query($sql);

        $meteotheques = [];
        foreach ($pdoStatement as $meteothequeFormatTableau) {
            $meteotheques[] = ModelMeteotheque::construire($meteothequeFormatTableau);
        }
        return $meteotheques;
    }

    public function sauvegarder() : bool {
        try {
            $sql = "INSERT INTO Meteotheques (utilisateur_id, nom_collection, description, date_creation)
                    VALUES (:utilisateurId, :nomCollection, :description, :dateCreation)";
            $pdoStatement = Model::getPdo()->prepare($sql);
            $values = array(
                'utilisateurId' => $this->utilisateurId,
                'nomCollection' => $this->nomCollection,
                'description' => $this->description,
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
