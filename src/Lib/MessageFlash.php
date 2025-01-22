<?php

namespace App\Meteo\Lib;

class MessageFlash
{
    private static string $cleFlash = "_messagesFlash";

    // Demarrer la session
    private static function demarrerSession() : void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // 
    public static function isConnected(): bool {
        self::demarrerSession();
        return isset($_SESSION['utilisateur_id']);
    }
    

    // Ajouter un message flash
    public static function ajouter(string $type, string $message): void {
        self::demarrerSession();
        if (!isset($_SESSION[self::$cleFlash][$type])) {
            $_SESSION[self::$cleFlash][$type] = [];
        }
        $_SESSION[self::$cleFlash][$type][] = $message;
    }    

    // Vérifier si un type de message existe
    public static function contientMessage(string $type) : bool {
        self::demarrerSession();
        return !empty($_SESSION[self::$cleFlash][$type]);
    }

    // Lire tous les messages d'un type donné (et les supprimer)
    public static function lireMessages(string $type) : array {
        self::demarrerSession();
        $messages = $_SESSION[self::$cleFlash][$type] ?? [];
        unset($_SESSION[self::$cleFlash][$type]); // Supprime après lecture
        return $messages;
    }

    // Lire tous les messages flash (et les supprimer)
    public static function lireTousMessages() : array {
        self::demarrerSession();
        $messages = $_SESSION[self::$cleFlash] ?? [];
        unset($_SESSION[self::$cleFlash]); // Supprime les messsages après lecture
        return $messages;
    }
}
?>