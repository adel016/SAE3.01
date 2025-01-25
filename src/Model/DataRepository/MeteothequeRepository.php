<?php

namespace App\Meteo\Model\DataRepository;

use App\Meteo\Model\DataObject\Meteotheques;

class MeteothequeRepository extends AbstractRepository {
    protected function getNomTable(): string {
        return "Meteotheques";
    }

    protected function getPrimaryKey(): string {
        return "meteo_id";
    }

    protected function getNomsColonnes(): array {
        return ['meteo_id', 'utilisateur_id', 'nom_collection', 'description', 'date_creation'];
    }

    // Méthode pour construire un objet Meteotheque à partir d'un tableau de données
    protected function construire(array $data): Meteotheques {
        return new Meteotheques(
            $data['meteo_id'],
            $data['utilisateur_id'],
            $data['nom_collection'],
            $data['description'],
            $data['date_creation']
        );
    }
}
