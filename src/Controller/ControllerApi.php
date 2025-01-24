<?php

namespace App\Meteo\Controller;

class ControllerApi {
    public static function default() {
        self::afficheVue('view.php', [
            'pagetitle' => 'ACCUEIL - OBSERVATIONS',
            'cheminVueBody' => 'observations/carteThermique.php'
        ]);
    }

    public static function getSynopData() {
        $dateDebut = date('Y-m-d\T00:00:00', strtotime('-1 days'));
        $dateFin = date('Y-m-d\T23:59:59', strtotime('-1 days'));

        $apiUrl = "https://public.opendatasoft.com/api/explore/v2.1/catalog/datasets/donnees-synop-essentielles-omm/records?limit=100&where=date%20%3E%3D%20%22{$dateDebut}%22%20AND%20date%20%3C%3D%20%22{$dateFin}%22";
        $response = file_get_contents($apiUrl);

        if ($response === false) {
            http_response_code(500);
            echo json_encode(["error" => "Impossible de récupérer les données de l'API."]);
            exit();
        }

        $data = json_decode($response, true);

        if (!isset($data['results']) || !is_array($data['results']) || count($data['results']) === 0) {
            http_response_code(500);
            echo json_encode(["error" => "Structure inattendue des données de l'API.", "details" => $data]);
            exit();
        }

        $stations = array_map(function ($record) {
            $fields = $record;
            return [
                'latitude' => $fields['coordonnees']['lat'] ?? null,
                'longitude' => $fields['coordonnees']['lon'] ?? null,
                'ville' => $fields['nom'] ?? 'Inconnue',
                'region' => $fields['nom_reg'] ?? 'Inconnue',
                'temp' => isset($fields['t']) ? round($fields['t'] - 273.15, 1) : '--',
                'humidity' => isset($fields['u']) ? round($fields['u'], 1) : '--',
                'windSpeed' => isset($fields['ff']) ? round($fields['ff'], 1) : '--',
                'altitude' => isset($fields['altitude']) ? round($fields['altitude'], 1) : '--',
                'date' => $fields['date'] ?? '--',
            ];
        }, $data['results']);

        header('Content-Type: application/json');
        echo json_encode($stations);
    }

    public static function getHeatmapDataByRegion($regionName = null) {
        $dateDebut = date('Y-m-d\T00:00:00', strtotime('-1 days'));
        $dateFin = date('Y-m-d\T23:59:59', strtotime('-1 days'));
    
        $apiUrl = "https://public.opendatasoft.com/api/explore/v2.1/catalog/datasets/donnees-synop-essentielles-omm/records?limit=100&where=date%20%3E%3D%20%22{$dateDebut}%22%20AND%20date%20%3C%3D%20%22{$dateFin}%22";
    
        $response = file_get_contents($apiUrl);
    
        if ($response === false) {
            http_response_code(500);
            echo json_encode(["error" => "Impossible de récupérer les données de l'API."]);
            exit();
        }
    
        $data = json_decode($response, true);
    
        if (!isset($data['results']) || !is_array($data['results']) || count($data['results']) === 0) {
            http_response_code(500);
            echo json_encode(["error" => "Structure inattendue des données de l'API.", "details" => $data]);
            exit();
        }
    
        $stations = array_filter($data['results'], function ($record) use ($regionName) {
            return $regionName === null || (isset($record['nom_reg']) && strcasecmp($record['nom_reg'], $regionName) === 0);
        });
    
        $heatmapData = array_map(function ($record) {
            return [
                'lat' => $record['coordonnees']['lat'] ?? null,
                'lon' => $record['coordonnees']['lon'] ?? null,
                'value' => isset($record['t']) ? round($record['t'] - 273.15, 1) : null
            ];
        }, $stations);
    
        header('Content-Type: application/json');
        echo json_encode($heatmapData);
    }    

    public static function afficheVue(string $cheminVue, array $parametres = []): void {
        extract($parametres);
        require __DIR__ . '/../view/' . $cheminVue;
    }
}

?>
