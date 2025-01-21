<?php

    namespace App\Meteo\Model\DataObject;

    class Station {
        private int $stationId;
        private ?string $numero;
        private float $latitude;
        private float $longitude;
        private int $altitude;
        private string $region;
        private string $departement;
        private string $ville;
        private string $codeGeo;

        public function __construct(
            int $stationId,
            ?string $numero,
            float $latitude,
            float $longitude,
            int $altitude,
            string $region,
            string $departement,
            string $ville,
            string $codeGeo
        ) {
            $this->stationId = $stationId;
            $this->numero = $numero;
            $this->latitude = $latitude;
            $this->longitude = $longitude;
            $this->altitude = $altitude;
            $this->region = $region;
            $this->departement = $departement;
            $this->ville = $ville;
            $this->codeGeo = $codeGeo;
        }

        // Getters
        public function getStationId(): int { return $this->stationId; }
        public function getNumero(): ?string { return $this->numero; }
        public function getLatitude(): float { return $this->latitude; }
        public function getLongitude(): float { return $this->longitude; }
        public function getAltitude(): int { return $this->altitude; }
        public function getRegion(): string { return $this->region; }
        public function getDepartement(): string { return $this->departement; }
        public function getVille(): string { return $this->ville; }
        public function getCodeGeo(): string { return $this->codeGeo; }

        // Formatage pour le tableau
        public function formatTableau(): array {
            return [
                'station_id' => $this->stationId,
                'numero' => $this->numero,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'altitude' => $this->altitude,
                'region' => $this->region,
                'departement' => $this->departement,
                'ville' => $this->ville,
                'code_geo' => $this->codeGeo
            ];
        }
    }
?>