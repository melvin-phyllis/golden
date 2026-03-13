<?php
// Configuration
$host = 'localhost';
$user = 'root';
$pass = ''; // Ton mot de passe XAMPP (vide par défaut)
$dbname = 'management'; // Le nom de ta base de données
$backup_file = '../../backups/sauvegarde_' . date('Y-m-d_H-i-s') . '.sql';

// Créer le dossier backups s'il n'existe pas
if (!is_dir('../../backups')) {
    mkdir('../../backups', 0777, true);
}

// Commande pour Windows (XAMPP)
// Cette ligne demande à MySQL d'exporter toutes les tables
$command = "C:\xampp\mysql\bin\mysqldump.exe --user=$user --password=$pass --host=$host $dbname > $backup_file";

system($command, $output);

if ($output === 0) {
    echo "✅ Sauvegarde réussie ! Fichier : " . $backup_file;
} else {
    echo "❌ Erreur lors de la sauvegarde. Vérifiez le chemin de mysqldump.";
}
?>




