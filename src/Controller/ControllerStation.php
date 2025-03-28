<?php

namespace App\Meteo\Controller;

use App\Meteo\Model\DataRepository\StationRepository;

class ControllerStation {
    public static function getStationJSON () {
        header('Content-Type: application/json');
        $repository = new StationRepository();
        $stations = $repository->getAll(); // Récupère toutes les stations
        $stationsArray = array_map(fn($station) => $station->formatTableau(), $stations);
        echo json_encode($stationsArray);
        exit();
    }

    public static function afficheVue(string $cheminVue, array $parametres = []): void {
        extract($parametres);
        require __DIR__ . '/../view/' . $cheminVue;
    }
}

?>