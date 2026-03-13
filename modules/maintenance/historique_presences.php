<?php
session_start();
require_once '../../config/db.php';

// 1. Filtrage par mois (par défaut le mois actuel)
$mois_actuel = isset($_GET['mois']) ? $_GET['mois'] : date('Y-m');

// 2. Requête pour calculer les statistiques par employé
$sql = "SELECT p.nom_complet, p.fonction,
        COUNT(CASE WHEN pr.statut_presence = 'Présent' THEN 1 END) as total_presents,
        COUNT(CASE WHEN pr.statut_presence = 'Absent' THEN 1 END) as total_absents,
        COUNT(CASE WHEN pr.statut_presence = 'Repos' THEN 1 END) as total_repos
        FROM equipe_prestige p
        LEFT JOIN equipe_presences pr ON p.id = pr.employe_id AND pr.date_jour LIKE ?
        GROUP BY p.id
        ORDER BY total_presents DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$mois_actuel . '%']);
$stats = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Présences | Prestige</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #050505; color: #fff; font-family: 'Inter', sans-serif; }
        .gold-border { border: 1px solid rgba(212, 175, 55, 0.2); }
        .stat-card { background: linear-gradient(145deg, #111, #080808); border: 1px solid #222; }
    </style>
</head>
<body class="p-8">
    <div class="max-w-6xl mx-auto">
        
        <header class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-serif text-[#D4AF37] uppercase tracking-widest">Bilan Mensuel</h1>
                <p class="text-zinc-500 text-xs">Analyse des présences pour le mois de <?= date('F Y', strtotime($mois_actuel)) ?></p>
            </div>
            <form method="GET" class="flex gap-2">
                <input type="month" name="mois" value="<?= $mois_actuel ?>" 
                       class="bg-black border border-zinc-700 text-xs p-2 rounded-xl outline-none focus:border-[#D4AF37]">
                <button type="submit" class="bg-zinc-800 px-4 py-2 rounded-xl text-[10px] uppercase font-bold">Filtrer</button>
            </form>
        </header>

        <div class="bg-zinc-900/50 rounded-3xl overflow-hidden gold-border shadow-2xl">
            <table class="w-full text-left">
                <thead class="bg-zinc-800/80 text-[#D4AF37] uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="p-6">Collaborateur</th>
                        <th class="p-6">Fonction</th>
                        <th class="p-6 text-center">Jours Présents</th>
                        <th class="p-6 text-center">Absences</th>
                        <th class="p-6 text-center">Repos</th>
                        <th class="p-6 text-right">Assiduité</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    <?php foreach($stats as $s): 
                        $total_jours = $s['total_presents'] + $s['total_absents'];
                        $pourcentage = ($total_jours > 0) ? round(($s['total_presents'] / $total_jours) * 100) : 0;
                    ?>
                    <tr class="hover:bg-zinc-800/20 transition">
                        <td class="p-6 font-bold text-sm uppercase"><?= $s['nom_complet'] ?></td>
                        <td class="p-6 text-zinc-500 text-xs uppercase"><?= $s['fonction'] ?></td>
                        <td class="p-6 text-center font-mono text-green-500 font-bold"><?= $s['total_presents'] ?></td>
                        <td class="p-6 text-center font-mono text-red-500"><?= $s['total_absents'] ?></td>
                        <td class="p-6 text-center font-mono text-blue-400"><?= $s['total_repos'] ?></td>
                        <td class="p-6 text-right">
                            <span class="text-xs font-bold <?= $pourcentage > 80 ? 'text-green-400' : 'text-yellow-500' ?>">
                                <?= $pourcentage ?>%
                            </span>
                            <div class="w-24 bg-zinc-800 h-1.5 rounded-full mt-1 ml-auto">
                                <div class="bg-[#D4AF37] h-full rounded-full" style="width: <?= $pourcentage ?>%"></div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-10 flex justify-center gap-6">
            <a href="gestion_personnel.php" class="text-zinc-500 text-[10px] uppercase border-b border-zinc-800 pb-1">Retour Supervision</a>
            <button onclick="window.print()" class="text-[#D4AF37] text-[10px] uppercase border-b border-[#D4AF37] pb-1">Imprimer le rapport</button>
        </div>
    </div>


    <?php include '../layout/footer.php'; ?>
</body>
</html>