<?php

namespace App\Meteo\Controller;

class TableauDeBordController
{
    public static function index()
    {
        require __DIR__ . '/../View/dashboard/index.html';
    }

    public static function rechercheAvancee()
    {
        require __DIR__ . '/../View/dashboard/test_recherche.html';
    }
}
