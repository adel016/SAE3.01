<?php

namespace App\Meteo\Lib;

class Psr4AutoloaderClass
{
    /**
     * Tableau associatif où la clé est un préfixe de namespace
     * et la valeur est un tableau de répertoires de base pour les classes de ce namespace.
     *
     * @var array
     */
    protected array $prefixes = array();

    /**
     * Enregistre cet autoloader avec la pile SPL autoload.
     */
    public function register(): void
    {
        spl_autoload_register([$this, 'loadClass']);
    }

    /**
     * Ajoute un répertoire de base pour un préfixe de namespace.
     *
     * @param string $prefix Préfixe de namespace.
     * @param string $base_dir Répertoire de base des fichiers de classes.
     * @param bool $prepend Si vrai, ajoute le répertoire au début du tableau.
     */
    public function addNamespace(string $prefix, string $base_dir, bool $prepend = false): void
    {
        // Normalise le préfixe de namespace
        $prefix = trim($prefix, '\\') . '\\';

        // Normalise le répertoire de base avec un séparateur de répertoire final
        $base_dir = rtrim($base_dir, DIRECTORY_SEPARATOR) . '/';

        // Initialise le tableau pour ce préfixe de namespace
        if (!isset($this->prefixes[$prefix])) {
            $this->prefixes[$prefix] = [];
        }

        // Retient le répertoire de base pour le préfixe
        if ($prepend) {
            array_unshift($this->prefixes[$prefix], $base_dir);
        } else {
            $this->prefixes[$prefix][] = $base_dir;
        }
    }

    /**
     * Charge le fichier de classe correspondant à un nom de classe donné.
     *
     * @param string $class Nom complet de la classe.
     * @return bool|string Nom du fichier chargé ou false si échec.
     */
    public function loadClass(string $class)
    {
        // Le préfixe courant
        $prefix = $class;

        // Recherche de manière récursive dans les namespaces pour trouver un fichier
        while (false !== $pos = strrpos($prefix, '\\')) {
            $prefix = substr($class, 0, $pos + 1);
            $relative_class = substr($class, $pos + 1);

            // Tente de charger un fichier correspondant au préfixe et à la classe relative
            $mapped_file = $this->loadMappedFile($prefix, $relative_class);
            if ($mapped_file) {
                return $mapped_file;
            }

            // Réduit le préfixe pour la prochaine itération
            $prefix = rtrim($prefix, '\\');
        }

        // Aucune correspondance trouvée
        return false;
    }

    /**
     * Charge le fichier mappé pour un préfixe de namespace et une classe relative.
     *
     * @param string $prefix Préfixe de namespace.
     * @param string $relative_class Classe relative.
     * @return bool|string Nom du fichier chargé ou false si échec.
     */
    protected function loadMappedFile(string $prefix, string $relative_class)
    {
        if (!isset($this->prefixes[$prefix])) {
            return false;
        }

        // Parcourt les répertoires de base pour ce préfixe
        foreach ($this->prefixes[$prefix] as $base_dir) {
            $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
            if ($this->requireFile($file)) {
                return $file;
            }
        }

        return false;
    }

    /**
     * Vérifie si un fichier existe et le charge.
     *
     * @param string $file Nom du fichier.
     * @return bool True si le fichier est chargé avec succès, false sinon.
     */
    protected function requireFile(string $file): bool
    {
        if (file_exists($file)) {
            require $file;
            return true;
        }
        error_log("Autoloader : Impossible de charger le fichier $file");
        return false;
    }
}
