<?php

namespace App\Meteo\Controller;

class ControllerContact {
    
    // Afficher la page Contact
    public static function show() {
        self::afficheVue('view.php', [
            'pagetitle' => 'MéteoVision - Contact',
            'cheminVueBody' => 'accueil/contact.php'
        ]);
    }

    private static function afficheVue($cheminVue, $parametres = []) {
        extract($parametres);
        require __DIR__ . "/../View/$cheminVue";
    }
}

?>