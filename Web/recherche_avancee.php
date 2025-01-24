<?php
header('Content-Type: application/json');

// Vérifie les paramètres fournis
$libgeo = isset($_GET['libgeo']) ? urlencode($_GET['libgeo']) : ''; // Ville
$region = isset($_GET['region']) ? urlencode($_GET['region']) : ''; // Région
$nom_dept = isset($_GET['nom_dept']) ? urlencode($_GET['nom_dept']) : ''; // Département
$codegeo = isset($_GET['codegeo']) ? urlencode($_GET['codegeo']) : ''; // Code département
$date_debut = isset($_GET['date_debut']) ? $_GET['date_debut'] : ''; // Date début
$date_fin = isset($_GET['date_fin']) ? $_GET['date_fin'] : ''; // Date fin

// Par défaut, si pas de date_fin, utiliser la date actuelle
if (!$date_fin && $date_debut) {
    $date_fin = date('Y-m-t') . 'T23:59:59Z'; // Dernier jour du mois
}

// Ajouter "T00:00:00Z" à la date_debut si elle est définie
if ($date_debut) {
    $date_debut .= 'T00:00:00Z';
}

// Construire l'URL de l'API avec les filtres
$api_url = "https://public.opendatasoft.com/api/explore/v2.1/catalog/datasets/donnees-synop-essentielles-omm/records?limit=100";

if ($libgeo) {
    $api_url .= "&refine=libgeo%3A%22$libgeo%22"; // Filtrer par ville
}
if ($region) {
    $api_url .= "&refine=nom_reg%3A%22$region%22"; // Filtrer par région
}
if ($nom_dept) {
    $api_url .= "&refine=nom_dept%3A%22$nom_dept%22"; // Filtrer par département
}
if ($codegeo) {
    $api_url .= "&refine=codegeo%3A%22$codegeo%22"; // Filtrer par code département
}
if ($date_debut && $date_fin) {
    // Filtrer par plage de dates avec la clause "where"
    $api_url .= "&where=date%20%3E%3D%20%22$date_debut%22%20AND%20date%20%3C%3D%20%22$date_fin%22";
}

// Afficher l'URL générée pour débogage (facultatif, utile lors des tests)
error_log("URL générée : " . $api_url);

// Effectuer la requête
$response = file_get_contents($api_url);
if ($response === FALSE) {
    echo json_encode(["error" => "Erreur lors de la récupération des données."]);
    exit;
}

// Vérification supplémentaire pour la granularité (si nécessaire)
if (isset($_GET['granularite']) && $_GET['granularite'] === 'day') {
    // Récupérer les données pour une journée entière (intervalle 00:00 - 23:59)
    $date_debut = isset($_GET['date_debut']) ? $_GET['date_debut'] : '';
    if ($date_debut) {
        $api_url .= "&where=date%20%3E%3D%20%22{$date_debut}T00:00:00Z%22%20AND%20date%20%3C%3D%20{$date_debut}T23:59:59Z";
    }
}

// Afficher les résultats JSON
echo $response;
?>
