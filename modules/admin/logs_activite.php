<?php
require_once '../../config/db.php';

// Initialisation des variables de filtre
$filter_user = $_GET['user_id'] ?? '';
$filter_date = $_GET['date_log'] ?? '';

// Construction de la requête avec filtres dynamiques
$conditions = [];
$params = [];

if (!empty($filter_user)) {
    $conditions[] = "l.utilisateur_id = ?";
    $params[] = $filter_user;
}

if (!empty($filter_date)) {
    $conditions[] = "DATE(l.date_action) = ?";
    $params[] = $filter_date;
}

$where_clause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

$sql = "SELECT l.*, u.nom as operateur, r.nom_role 
        FROM logs_activite l 
        JOIN utilisateurs u ON l.utilisateur_id = u.id 
        JOIN roles r ON u.role_id = r.id 
        $where_clause
        ORDER BY l.date_action DESC LIMIT 100";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll();

// Pour le menu déroulant des utilisateurs
$liste_users = $pdo->query("SELECT id, nom FROM utilisateurs ORDER BY nom ASC")->fetchAll();
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique des Actions | Prestige</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#050505] text-white p-8">
    <div class="max-w-5xl mx-auto">
        
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-[#D4AF37] text-2xl font-serif uppercase tracking-widest">Journal d'Activité</h1>
                <p class="text-zinc-500 text-xs mt-1 uppercase">Surveillance des mouvements du système</p>
            </div>
            <button onclick="window.location.reload()" class="text-zinc-400 text-[10px] border border-zinc-800 px-4 py-2 rounded-lg hover:bg-zinc-800">Actualiser</button>
        </div>





<a href="export_logs_pdf.php?user_id=<?= $filter_user ?>&date_log=<?= $filter_date ?>" 
   class="bg-red-700 text-white px-6 py-2.5 rounded-xl font-bold text-[10px] uppercase hover:bg-red-800 transition">
    <i class="fa-solid fa-file-pdf mr-2"></i> Exporter PDF
</a>





<div class="bg-zinc-900/40 p-6 rounded-2xl border border-zinc-800 mb-6">
    <form method="GET" class="flex flex-wrap items-end gap-4">
        
        <div class="flex-1 min-w-[200px]">
            <label class="block text-[9px] uppercase text-zinc-500 mb-2 font-bold tracking-widest">Filtrer par Utilisateur</label>
            <select name="user_id" class="w-full bg-black border border-zinc-800 p-2.5 rounded-xl text-xs outline-none focus:border-[#D4AF37]">
                <option value="">Tous les utilisateurs</option>
                <?php foreach($liste_users as $u): ?>
                    <option value="<?= $u['id'] ?>" <?= $filter_user == $u['id'] ? 'selected' : '' ?>><?= $u['nom'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="flex-1 min-w-[200px]">
            <label class="block text-[9px] uppercase text-zinc-500 mb-2 font-bold tracking-widest">Filtrer par Date</label>
            <input type="date" name="date_log" value="<?= $filter_date ?>" class="w-full bg-black border border-zinc-800 p-2 text-xs rounded-xl outline-none focus:border-[#D4AF37]">
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-[#D4AF37] text-black px-6 py-2.5 rounded-xl font-bold text-[10px] uppercase hover:bg-yellow-600 transition">
                <i class="fa-solid fa-filter mr-2"></i> Filtrer
            </button>
            <a href="logs_activite.php" class="bg-zinc-800 text-white px-6 py-2.5 rounded-xl font-bold text-[10px] uppercase hover:bg-zinc-700 transition">
                Réinitialiser
            </a>
        </div>

    </form>
</div>


    


        <div class="bg-zinc-900/20 rounded-[2rem] border border-zinc-800 overflow-hidden shadow-2xl">
            <div class="overflow-x-auto">





<a href="delete_log.php?action=clear_all" 
   onclick="return confirm('VOULEZ-VOUS TOUT EFFACER ?')"
   class="text-red-500 text-[10px] font-bold uppercase border border-red-500/20 px-4 py-2 rounded-lg hover:bg-red-500 hover:text-white transition">
   Vider le journal
</a>









                <table class="w-full text-left">
                    <thead class="bg-zinc-900 text-[10px] uppercase text-zinc-500 font-bold">
                        <tr>
                            <th class="p-5">Date & Heure</th>
                            <th class="p-5">Utilisateur</th>
                            <th class="p-5">Action Effectuée</th>
                            <th class="p-5">Adresse IP</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm font-mono">
                        <?php foreach($logs as $log): ?>
                        <tr class="border-t border-zinc-800 hover:bg-white/5 transition">
                            <td class="p-5 text-zinc-500 whitespace-nowrap">
                                <?= date('d/m/Y H:i', strtotime($log['date_action'])) ?>
                            </td>
                            <td class="p-5">
                                <span class="text-[#D4AF37] font-bold"><?= $log['operateur'] ?></span>
                                <span class="block text-[9px] text-zinc-600 uppercase"><?= $log['nom_role'] ?></span>
                            </td>
                            <td class="p-5 text-zinc-300">
                                <?= htmlspecialchars($log['action']) ?>
                            </td>
                            <td class="p-5 text-zinc-600 text-[10px]">
                                <?= $log['adresse_ip'] ?? '127.0.0.1' ?>
                            </td>




                            <td class="p-5 text-right">
                               <a href="delete_log.php?id=<?= $log['id'] ?>" 
                               onclick="return confirm('Effacer cette trace ?')"
                               class="text-zinc-700 hover:text-red-500">
                                <i class="fa-solid fa-trash-can"></i>
                               </a>
                            </td>




                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8 flex justify-between items-center">
            <p class="text-zinc-600 text-[10px] uppercase">Affichage des 50 dernières actions critiques</p>
            <a href="admin_dash.php" class="text-[#D4AF37] text-[10px] font-bold uppercase hover:underline">Retour Administration</a>
        </div>
    </div>
</body>
</html>