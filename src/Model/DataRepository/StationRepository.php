<?php

    namespace App\Meteo\Model\DataRepository;

    use App\Meteo\Model\DataObject\Stations;

    class StationRepository extends AbstractRepository {
        protected function getNomTable(): string {
            return "stations"; // Nom de votre table
        }

        protected function getPrimaryKey(): string {
            return "station_id";
        }

        protected function getNomsColonnes(): array {
            return ['station_id', 'numero', 'latitude', 'longitude', 'altitude', 'region', 'departement', 'ville', 'code_geo'];
        }

        protected function construire(array $data): Stations {
            return new Stations(
                $data['station_id'],
                $data['numero'] ?? null,
                $data['latitude'],
                $data['longitude'],
                $data['altitude'],
                $data['region'],
                $data['departement'],
                $data['ville'],
                $data['code_geo']
            );
        }

    }

?>