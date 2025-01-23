<?php

namespace App\Meteo\Controller;

class ControllerApi {
    public static function getSynopData() {
        // Définir une plage de dates pour tester les données
        $dateDebut = date('Y-m-d', strtotime('-1 days')); // hier
        $dateFin = date('Y-m-d'); // Aujourd'hui

        // URL de l'API
        $apiUrl = "https://public.opendatasoft.com/api/explore/v2.1/catalog/datasets/donnees-synop-essentielles-omm/records?limit=100&where=date%20%3E%3D%20%22{$dateDebut}%22%20AND%20date%20%3C%3D%20%22{$dateFin}%22";

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
            $fields = $record; // Les données sont directement accessibles dans chaque résultat

            return [
                'latitude' => $fields['coordonnees']['lat'] ?? null,
                'longitude' => $fields['coordonnees']['lon'] ?? null,
                'ville' => $fields['libgeo'] ?? 'Inconnue',
                'region' => $fields['nom_reg'] ?? 'Inconnue',
                'temp' => isset($fields['t']) ? round($fields['t'] - 273.15, 1) : '--', // Convertir de Kelvin à Celsius
                'humidity' => isset($fields['u']) ? round($fields['u'], 1) : '--', // Humidité arrondie
                'windSpeed' => isset($fields['ff']) ? round($fields['ff'], 1) : '--', // Vitesse du vent arrondie
                'altitude' => isset($fields['altitude']) ? round($fields['altitude'], 1) : '--', // Altitude arrondie
            ];
        }, $data['results']);

        // Retourner les données JSON
        header('Content-Type: application/json');
        echo json_encode($stations);
    }
}

?>
