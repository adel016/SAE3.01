<?php

namespace App\Meteo\Controller;

class ControllerApi {
    public static function getSynopData() {
        // Obtenir la date et l'heure actuelles
        $dateActuelle = date('Y-m-d\TH:i:s');
    
        // URL de l'API avec la date actuelle
        $apiUrl = "https://public.opendatasoft.com/api/explore/v2.1/catalog/datasets/donnees-synop-essentielles-omm/records?limit=100&where=date%20%3C%3D%20%22{$dateActuelle}%22&order_by=date%20DESC";
    
        // Récupérer les données depuis l'API
        $response = file_get_contents($apiUrl);
    
        if ($response === false) {
            http_response_code(500);
            echo json_encode(["error" => "Impossible de récupérer les données de l'API."]);
            exit();
        }
    
        $data = json_decode($response, true);
    
        if (!isset($data['results']) || !is_array($data['results'])) {
            http_response_code(500);
            echo json_encode(["error" => "Structure inattendue des données de l'API.", "details" => $data]);
            exit();
        }
    
        $stations = array_map(function ($record) {
            $fields = $record;
            return [
                'latitude' => $fields['coordonnees']['lat'] ?? null,
                'longitude' => $fields['coordonnees']['lon'] ?? null,
                'ville' => $fields['libgeo'] ?? 'Inconnue',
                'region' => $fields['nom_reg'] ?? 'Inconnue',
                'temp' => isset($fields['t']) ? round($fields['t'] - 273.15, 1) : '--',
                'humidity' => isset($fields['u']) ? round($fields['u'], 1) : '--',
                'windSpeed' => isset($fields['ff']) ? round($fields['ff'], 1) : '--',
                'altitude' => isset($fields['altitude']) ? round($fields['altitude'], 1) : '--',
                'date' => $fields['date'] ?? '--', // Ajout de la date d'observation
            ];
        }, $data['results']);
    
        header('Content-Type: application/json');
        echo json_encode($stations);
    }
}    

?>
