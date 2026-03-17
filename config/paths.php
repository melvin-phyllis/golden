<?php
/**
 * Chemins et URL de base du projet (compatible déploiement cPanel / sous-dossier).
 * En local sous XAMPP avec le projet dans htdocs/golden : BASE_URL = '/golden/'
 * Sur un hébergement à la racine du domaine : BASE_URL = '/'
 * Tu peux forcer une valeur avec la variable d'environnement BASE_URL.
 */
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}
if (!defined('BASE_URL')) {
    $base = getenv('BASE_URL');
    if ($base === false || $base === '') {
        // Détection automatique si le script est appelé depuis un sous-dossier (ex: /golden/)
        $script = $_SERVER['SCRIPT_NAME'] ?? '';
        if (preg_match('#^/([^/]+)/#', $script, $m) && $m[1] !== 'modules' && $m[1] !== 'auth' && $m[1] !== 'config') {
            $base = '/' . $m[1] . '/';
        } else {
            $base = '/';
        }
    }
    define('BASE_URL', rtrim($base, '/') . '/');
}
