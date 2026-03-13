<?php
require_once '../../config/db.php';

// Récupération des utilisateurs avec leurs rôles
$sql = "SELECT u.*, r.nom_role FROM utilisateurs u 
        LEFT JOIN roles r ON u.role_id = r.id 
        ORDER BY u.id ASC";
$users = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration RH | Prestige</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#050505] text-white p-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-10">
            <h1 class="text-3xl font-serif text-[#D4AF37] uppercase tracking-widest">Gestion du Personnel</h1>
            <a href="../../inscription.php" class="bg-[#D4AF37] text-black px-6 py-3 rounded-xl font-bold text-xs uppercase">
                + Nouveau Collaborateur
            </a>
        </div>

        <div class="bg-zinc-900/30 rounded-[2rem] border border-zinc-800 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-zinc-900 text-[10px] uppercase text-zinc-500">
                    <tr>
                        <th class="p-5">Collaborateur</th>
                        <th class="p-5">Rôle & Accès</th>
                        <th class="p-5">Salaire Mensuel</th>
                        <th class="p-5">Planning</th>
                        <th class="p-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <?php foreach($users as $u): ?>
                    <tr class="border-t border-zinc-800 hover:bg-zinc-800/20 transition">
                        <td class="p-5">
                            <p class="font-bold"><?= $u['nom'] ?></p>
                            <p class="text-[10px] text-zinc-500"><?= $u['email'] ?></p>
                        </td>
                        <td class="p-5">
                            <span class="px-3 py-1 rounded-full text-[9px] font-bold uppercase border border-zinc-700">
                                <?= $u['nom_role'] ?>
                            </span>
                        </td>
                        <td class="p-5 font-mono text-[#D4AF37]">
                            <?= number_format($u['salaire_base'], 0, ',', ' ') ?> CFA
                        </td>
                        <td class="p-5 text-xs text-zinc-400 italic">
                            <?= $u['planning_horaire'] ?? 'Non défini' ?>
                        </td>
                        <td class="p-5 text-right space-x-2">
                            <a href="edit_user.php?id=<?= $u['id'] ?>" class="text-zinc-500 hover:text-white">⚙️</a>
                            <button onclick="confirmDelete(<?= $u['id'] ?>)" class="text-zinc-500 hover:text-red-500">🗑️</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>


    <?php include '../layout/footer.php'; ?>
</body>
</html>