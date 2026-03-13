<?php
require_once '../../config/db.php';
session_start();

$backup_dir = '../../backups/';

// Créer le dossier s'il n'existe pas
if (!is_dir($backup_dir)) {
    mkdir($backup_dir, 0777, true);
}

// Récupérer la liste des fichiers de sauvegarde existants
$files = glob($backup_dir . "*.sql");
array_multisort(array_map('filemtime', $files), SORT_DESC, $files);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Sauvegardes Système | Prestige</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-[#050505] text-white p-8">
    <div class="max-w-4xl mx-auto">
        
        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-2xl font-serif text-[#D4AF37] uppercase tracking-widest">Base de Données</h1>
                <p class="text-zinc-500 text-[10px] uppercase">Gestion des points de restauration et archives</p>
            </div>
            <a href="run_backup.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-bold text-xs uppercase flex items-center gap-2 transition">
                <i class="fa-solid fa-plus-circle"></i> Créer une sauvegarde
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
            <div class="bg-zinc-900/40 p-6 rounded-3xl border border-zinc-800">
                <p class="text-zinc-500 text-[9px] uppercase font-bold mb-2">Dernière sauvegarde</p>
                <p class="text-xl font-mono">
                    <?= !empty($files) ? date("d/m/Y H:i", filemtime($files[0])) : "Aucune sauvegarde" ?>
                </p>
            </div>
            <div class="bg-zinc-900/40 p-6 rounded-3xl border border-zinc-800">
                <p class="text-zinc-500 text-[9px] uppercase font-bold mb-2">Espace utilisé</p>
                <p class="text-xl font-mono">
                    <?php
                    $size = 0;
                    foreach ($files as $f) { $size += filesize($f); }
                    echo round($size / 1024 / 1024, 2) . " MB";
                    ?>
                </p>
            </div>
        </div>

        <div class="bg-zinc-900/20 rounded-[2rem] border border-zinc-800 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-zinc-900 text-[10px] uppercase text-zinc-500">
                    <tr>
                        <th class="p-5">Nom du fichier</th>
                        <th class="p-5">Taille</th>
                        <th class="p-5">Date de création</th>
                        <th class="p-5 text-right">Action</th>
                    </tr>
                </thead>







              <tbody class="text-sm">
    <?php if(empty($files)): ?>
        <tr><td colspan="4" class="p-10 text-center text-zinc-600 italic">Aucune archive disponible.</td></tr>
    <?php endif; ?>

    <?php foreach($files as $file): 
        $filename = basename($file);
    ?>
    <tr class="border-t border-zinc-800 hover:bg-zinc-800/20 transition">
        <td class="p-5 font-mono text-xs"><?= $filename ?></td>
        <td class="p-5 text-zinc-500"><?= round(filesize($file) / 1024, 1) ?> KB</td>
        <td class="p-5 text-zinc-500"><?= date("d/m/Y H:i", filemtime($file)) ?></td>
        <td class="p-5 text-right space-x-4">
            <a href="<?= $file ?>" download class="text-[#D4AF37] hover:text-white transition" title="Télécharger">
                <i class="fa-solid fa-download"></i>
            </a>
            
            <button onclick="confirmDeleteBackup('<?= $filename ?>')" class="text-zinc-600 hover:text-red-500 transition" title="Supprimer">
                <i class="fa-solid fa-trash-can"></i>
            </button>
        </td>
    </tr>
    <?php endforeach; ?>
</tbody>

<script>
function confirmDeleteBackup(filename) {
    if(confirm("❗ ATTENTION : Voulez-vous vraiment supprimer définitivement cette sauvegarde (" + filename + ") ? Cette action est irréversible.")) {
        window.location.href = "delete_backup.php?file=" + filename;
    }
}
</script>










                

            </table>
        </div>

        <div class="mt-8 text-center">
            <p class="text-[9px] text-zinc-600 uppercase italic">Note : Les fichiers .sql peuvent être importés via phpMyAdmin en cas de panne totale.</p>
        </div>
    </div>
</body>
</html>





<!-- Tu as tout à fait raison, c'est l'élément qui manque pour sécuriser totalement tes données. Le backup_manager.php est la "tour de contrôle" de tes sauvegardes : il permet de voir les anciennes sauvegardes, d'en créer une nouvelle manuellement et de les télécharger en cas de problème. -->