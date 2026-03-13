<?php
require_once '../../config/db.php';
session_start();

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: users_list.php"); exit(); }

// 1. Récupérer les données actuelles de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

// 2. Récupérer les rôles pour la liste déroulante
$roles = $pdo->query("SELECT * FROM roles")->fetchAll();

if (!$user) { echo "Utilisateur introuvable"; exit(); }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Collaborateur | Prestige</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#050505] text-white p-8 font-sans">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-serif text-[#D4AF37] mb-8 uppercase tracking-widest">Fiche Collaborateur : <?= $user['nom'] ?></h1>

        <form action="update_user_process.php" method="POST" class="bg-zinc-900/40 p-10 rounded-[2.5rem] border border-zinc-800 space-y-6">
            <input type="hidden" name="id" value="<?= $user['id'] ?>">

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] uppercase text-zinc-500 mb-2">Nom Complet</label>
                    <input type="text" name="nom" value="<?= $user['nom'] ?>" class="w-full bg-black border border-zinc-800 p-3 rounded-xl outline-none focus:border-[#D4AF37]">
                </div>
                <div>
                    <label class="block text-[10px] uppercase text-zinc-500 mb-2">Rôle / Accès</label>
                    <select name="role_id" class="w-full bg-black border border-zinc-800 p-3 rounded-xl outline-none focus:border-[#D4AF37]">
                        <?php foreach($roles as $r): ?>
                            <option value="<?= $r['id'] ?>" <?= $user['role_id'] == $r['id'] ? 'selected' : '' ?>><?= $r['nom_role'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] uppercase text-zinc-500 mb-2">Salaire de Base (CFA)</label>
                    <input type="number" name="salaire_base" value="<?= $user['salaire_base'] ?>" class="w-full bg-black border border-zinc-800 p-3 rounded-xl outline-none focus:border-[#D4AF37]">
                </div>
                <div>
                    <label class="block text-[10px] uppercase text-zinc-500 mb-2">Planning Horaire</label>
                    <input type="text" name="planning_horaire" value="<?= $user['planning_horaire'] ?>" placeholder="ex: 08h-17h" class="w-full bg-black border border-zinc-800 p-3 rounded-xl outline-none focus:border-[#D4AF37]">
                </div>
            </div>

            <div>
                <label class="block text-[10px] uppercase text-zinc-500 mb-2">Statut du compte</label>
                <select name="statut" class="w-full bg-black border border-zinc-800 p-3 rounded-xl outline-none focus:border-[#D4AF37]">
                    <option value="Actif" <?= isset($user['statut']) && $user['statut'] == 'Actif' ? 'selected' : '' ?>>Compte Actif</option>
                    <option value="Suspendu" <?= isset($user['statut']) && $user['statut'] == 'Suspendu' ? 'selected' : '' ?>>Suspendre l'accès</option>
                </select>
            </div>

            <div class="pt-6 flex justify-between items-center">
                <a href="users_list.php" class="text-zinc-500 text-xs uppercase hover:text-white">Annuler</a>
                <button type="submit" class="bg-[#D4AF37] text-black px-10 py-3 rounded-full font-bold uppercase text-[10px] tracking-widest hover:bg-yellow-600 transition shadow-lg">
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</body>
</html>