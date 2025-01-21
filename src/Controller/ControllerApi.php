<?php

namespace App\Meteo\Controller;

class ControllerApi {
    public static function getSynopData() {
        $apiUrl = 'https://public.opendatasoft.com/api/explore/v2.1/catalog/datasets/donnees-synop-essentielles-omm/records?limit=100';

        // Récupérer les données depuis l'API
        $response = file_get_contents($apiUrl);

        // Vérifier si la récupération a échoué
        if ($response === false) {
            http_response_code(500);
            echo json_encode(["error" => "Impossible de récupérer les données de l'API."]);
            exit();
        }

        // Décoder les données JSON
        $data = json_decode($response, true);

        // Vérifier la structure des données
        if (!isset($data['results']) || !is_array($data['results'])) {
            http_response_code(500);
            echo json_encode(["error" => "Structure inattendue des données de l'API.", "details" => $data]);
            exit();
        }

        // Traiter les enregistrements
        $stations = array_map(function ($record) {
            // Extraire les données des stations
            $fields = $record;

            return [
                'latitude' => $fields['latitude'] ?? null,
                'longitude' => $fields['longitude'] ?? null,
                'ville' => $fields['libgeo'] ?? 'Inconnue',
                'region' => $fields['nom_reg'] ?? 'Inconnue',
                'temp' => isset($fields['tc']) ? round($fields['tc'], 1) : '--', // Température arrondie à 1 décimale
                'humidity' => isset($fields['u']) ? round($fields['u'], 1) : '--', // Humidité arrondie à 1 décimale
                'windSpeed' => isset($fields['ff']) ? round($fields['ff'], 1) : '--', // Vitesse du vent arrondie à 1 décimale
                'altitude' => isset($fields['altitude']) ? round($fields['altitude'], 1) : '--', // Altitude arrondie à 1 décimale
            ];
        }, $data['results']);

        // Retourner les données JSON
        header('Content-Type: application/json');
        echo json_encode($stations);
    }
}

?>