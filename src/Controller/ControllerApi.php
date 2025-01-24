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
        // Plage de dates pour hier
        $dateDebut = date('Y-m-d\T00:00:00', strtotime('-1 days')); // Hier à minuit
        $dateFin = date('Y-m-d\T23:59:59', strtotime('-1 days')); // Hier à 23:59
    
        // Construire l'URL de l'API
        $apiUrl = "https://public.opendatasoft.com/api/explore/v2.1/catalog/datasets/donnees-synop-essentielles-omm/records?limit=100&where=date%20%3E%3D%20%22{$dateDebut}%22%20AND%20date%20%3C%3D%20%22{$dateFin}%22";
        
        // Requête API
        $response = file_get_contents($apiUrl);
    
        if ($response === false) {
            http_response_code(500);
            echo json_encode(["error" => "Impossible de récupérer les données de l'API."]);
            exit();
        }
    
        $data = json_decode($response, true);
    
        // Vérifiez si des résultats sont disponibles
        if (!isset($data['results']) || !is_array($data['results']) || count($data['results']) === 0) {
            http_response_code(500);
            echo json_encode(["error" => "Structure inattendue des données de l'API.", "details" => $data]);
            exit();
        }
    
        // Traiter les résultats
        $stations = array_map(function ($record) {
            $fields = $record; // Utilisation correcte de `record` et `fields`
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
    
        // Retourner les données
        header('Content-Type: application/json');
        echo json_encode($stations);
    }
    
    public static function getHeatmapDataByRegion($regionName = null) {
        // Plage de dates pour hier
        $dateDebut = date('Y-m-d\T00:00:00', strtotime('-1 days')); // Hier à minuit
        $dateFin = date('Y-m-d\T23:59:59', strtotime('-1 days')); // Hier à 23:59
    
        // Construire l'URL de l'API avec les dates filtrées
        $apiUrl = "https://public.opendatasoft.com/api/explore/v2.1/catalog/datasets/donnees-synop-essentielles-omm/records?limit=100&where=date%20%3E%3D%20%22{$dateDebut}%22%20AND%20date%20%3C%3D%20%22{$dateFin}%22";
    
        // Afficher l'URL pour déboguer
        echo "API URL : " . htmlspecialchars($apiUrl) . "\n";
    
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
            $fields = $record['record']['fields'];
            return $regionName === null || (isset($fields['nom_reg']) && strcasecmp($fields['nom_reg'], $regionName) === 0);
        });
    
        $heatmapData = array_map(function ($record) {
            $fields = $record['record']['fields'];
            return [
                'lat' => $fields['coordonnees']['lat'] ?? null,
                'lon' => $fields['coordonnees']['lon'] ?? null,
                'value' => isset($fields['t']) ? round($fields['t'] - 273.15, 1) : null
            ];
        }, $stations);
    
        header('Content-Type: application/json');
        echo json_encode($heatmapData);
    }    

    public static function afficheVue(string $cheminVue, array $parametres = []): void {
        extract($parametres); // Crée des variables à partir du tableau $parametres
        require __DIR__ . '/../view/' . $cheminVue; // Charge la vue spécifiée
    }
}

?>