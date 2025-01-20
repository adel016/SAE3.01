<?php

namespace App\Meteo\Controller;

use App\Meteo\Model\DataRepository\StationRepository;

class ControllerStation {
    public static function readAll() {
        $repository = new StationRepository();
        $stations = $repository->getAll();

        // Affiche la vue avec les stations
        self::afficheVue('view.php', [
            'stations' => $stations,
            'pagetitle' => "Liste des stations",
            'cheminVueBody' => "station/list.php"
        ]);
    }

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