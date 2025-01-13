<?php

namespace App\Meteo\Model\DataRepository;

use App\Meteo\Model\DataObject\Utilisateur;

class UtilisateurRepository extends AbstractRepository {
    protected function getNomTable(): string {
        return "utilisateurs";
    }

    protected function getPrimaryKey(): string {
        return "utilisateur_id";
    }

    protected function getNomsColonnes(): array {
        return ['utilisateur_id', 'nom', 'prenom', 'email', 'mot_de_passe', 'role', 'etat_compte', 'date_creation'];
    }

    // Méthode pour construire un objet Utilisateur à partir d'un tableau de données
    protected function construire(array $data): Utilisateur {
        return new Utilisateur(
            $data['utilisateur_id'],
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['mot_de_passe'],
            $data['date_creation'],
            $data['role'],
            $data['etat_compte']
        );
    }
}
