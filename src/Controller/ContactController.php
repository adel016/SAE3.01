<?php

namespace App\Meteo\Controller;

class ContactController {
    public static function show() {
        self::afficheVue('view.php', [
            'pagetitle' => 'MéteoVision - Contact',
            'cheminVueBody' => 'contact.php'
        ]);
    }

    private static function afficheVue($cheminVue, $parametres = []) {
        extract($parametres);
        require __DIR__ . "/../View/$cheminVue";
    }
}