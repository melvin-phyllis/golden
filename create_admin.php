<?php
/**
 * Crée le premier compte administrateur (utilise config/db.local.php).
 * À lancer une seule fois en CLI : php create_admin.php
 * Supprimer ce fichier après utilisation (sécurité).
 */

if (php_sapi_name() !== 'cli') {
    die('Exécuter en ligne de commande : php create_admin.php');
}

$configDir = __DIR__ . '/config';
if (!file_exists($configDir . '/db.local.php')) {
    fwrite(STDERR, "Créez d'abord config/db.local.php (voir README).\n");
    exit(1);
}

require_once $configDir . '/db.php';

$count = (int) $pdo->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
if ($count > 0) {
    echo "Des utilisateurs existent déjà. Utilisez la page de connexion.\n";
    exit(0);
}

$email = 'admin@ya-consulting.com';
$password = 'Admin123!';
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, role_id, salaire_base, planning_horaire, statut) VALUES (?, ?, ?, 1, 0, 'Lun-Ven 8h-17h', 'Actif')");
$stmt->execute(['Administrateur', $email, $hash]);

echo "Admin créé.\n";
echo "Connexion : $email / $password\n";
echo "Supprimez ce fichier après utilisation : rm create_admin.php\n";
