<?php

namespace App\Meteo\Controller;

class TableauDeBordController {
    public static function apiRechercheAvancee() {
        header('Content-Type: application/json');

        try {
            $libgeo = isset($_GET['libgeo']) ? urlencode($_GET['libgeo']) : '';
            $region = isset($_GET['region']) ? urlencode($_GET['region']) : '';
            $nom_dept = isset($_GET['nom_dept']) ? urlencode($_GET['nom_dept']) : '';
            $codegeo = isset($_GET['codegeo']) ? urlencode($_GET['codegeo']) : '';
            $date_debut = isset($_GET['date_debut']) ? $_GET['date_debut'] : '';
            $date_fin = isset($_GET['date_fin']) ? $_GET['date_fin'] : '';

            if (!$date_fin && $date_debut) {
                $date_fin = date('Y-m-t') . 'T23:59:59Z';
            }
            if ($date_debut) {
                $date_debut .= 'T00:00:00Z';
            }

            $api_url = "https://public.opendatasoft.com/api/explore/v2.1/catalog/datasets/donnees-synop-essentielles-omm/records?limit=100";

            if ($libgeo) {
                $api_url .= "&refine=libgeo%3A%22$libgeo%22";
            }
            if ($region) {
                $api_url .= "&refine=nom_reg%3A%22$region%22";
            }
            if ($nom_dept) {
                $api_url .= "&refine=nom_dept%3A%22$nom_dept%22";
            }
            if ($codegeo) {
                $api_url .= "&refine=codegeo%3A%22$codegeo%22";
            }
            if ($date_debut && $date_fin) {
                $api_url .= "&where=date%20%3E%3D%20%22$date_debut%22%20AND%20date%20%3C%3D%20%22$date_fin%22";
            }

            $response = file_get_contents($api_url);

            if ($response === FALSE) {
                echo json_encode(["error" => "Erreur lors de la récupération des données de l'API."]);
                return;
            }

            echo $response;

        } catch (\Exception $e) {
            echo json_encode(["error" => "Une erreur est survenue : " . $e->getMessage()]);
        }
    }

    public static function index() {
        self::afficheVue('view.php', [
            'pagetitle' => 'MéteoVision - TDB Recherche Libre',
            'cheminVueBody' => 'dashboard/index.php'
        ]);
    }

    public static function rechercheAvancee() {
        self::afficheVue('view.php', [
            'pagetitle' => 'MéteoVision - TDB Recherche Avancée',
            'cheminVueBody' => 'dashboard/test_recherche.php'
        ]);
    }

    public static function afficheVue(string $cheminVue, array $parametres = []): void
    {
        extract($parametres);
        require __DIR__ . '/../view/' . $cheminVue; // Charge la vue specifiée
    }
}
?>