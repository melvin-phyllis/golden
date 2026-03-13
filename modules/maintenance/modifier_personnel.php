<?php
session_start();
require_once '../../config/db.php';

// 1. On récupère l'ID de l'employé à modifier
if (!isset($_GET['id'])) {
    header("Location: gestion_personnel.php");
    exit();
}

$id = $_GET['id'];

// 2. Chargement des données actuelles de l'employé
$stmt = $pdo->prepare("SELECT * FROM equipe_prestige WHERE id = ?");
$stmt->execute([$id]);
$perso = $stmt->fetch();

if (!$perso) {
    header("Location: gestion_personnel.php");
    exit();
}

// 3. Traitement de la mise à jour
if (isset($_POST['modifier'])) {
    $nom = $_POST['nom_complet'];
    $fonction = $_POST['fonction'];
    $tel = $_POST['telephone'];
    $statut = $_POST['statut_emploi'];

    $update = $pdo->prepare("UPDATE equipe_prestige SET nom_complet = ?, fonction = ?, telephone = ?, statut_emploi = ? WHERE id = ?");
    $update->execute([$nom, $fonction, $tel, $statut, $id]);
    
    header("Location: gestion_personnel.php?msg=updated");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Employé | Prestige</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #050505; color: #fff; font-family: 'Inter', sans-serif; }
        .gold-border { border: 1px solid rgba(212, 175, 55, 0.3); }
    </style>
</head>
<body class="p-10 flex justify-center">
    <div class="max-w-md w-full bg-zinc-900 p-8 rounded-3xl gold-border shadow-2xl">
        <h2 class="text-[#D4AF37] font-serif text-xl mb-6 uppercase tracking-widest text-center">Fiche Collaborateur</h2>
        
        <form method="POST" class="space-y-5">
            <div>
                <label class="text-[10px] uppercase text-zinc-500 ml-2">Nom Complet</label>
                <input type="text" name="nom_complet" value="<?= htmlspecialchars($perso['nom_complet']) ?>" required 
                       class="w-full bg-black border border-zinc-800 p-4 rounded-2xl outline-none focus:border-[#D4AF37] text-sm">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] uppercase text-zinc-500 ml-2">Fonction</label>
                    <select name="fonction" class="w-full bg-black border border-zinc-800 p-4 rounded-2xl outline-none focus:border-[#D4AF37] text-sm">
                        <option <?= $perso['fonction'] == 'Ménage' ? 'selected' : '' ?>>Ménage</option>
                        <option <?= $perso['fonction'] == 'Gouvernance' ? 'selected' : '' ?>>Gouvernance</option>
                        <option <?= $perso['fonction'] == 'Maintenance' ? 'selected' : '' ?>>Maintenance</option>
                        <option <?= $perso['fonction'] == 'Réception' ? 'selected' : '' ?>>Réception</option>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] uppercase text-zinc-500 ml-2">Statut</label>
                    <select name="statut_emploi" class="w-full bg-black border border-zinc-800 p-4 rounded-2xl outline-none focus:border-[#D4AF37] text-sm">
                        <option value="Actif" <?= $perso['statut_emploi'] == 'Actif' ? 'selected' : '' ?>>Actif</option>
                        <option value="Inactif" <?= $perso['statut_emploi'] == 'Inactif' ? 'selected' : '' ?>>Inactif</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="text-[10px] uppercase text-zinc-500 ml-2">Téléphone</label>
                <input type="text" name="telephone" value="<?= htmlspecialchars($perso['telephone']) ?>" required 
                       class="w-full bg-black border border-zinc-800 p-4 rounded-2xl outline-none focus:border-[#D4AF37] text-sm">
            </div>

            <button type="submit" name="modifier" class="w-full bg-[#D4AF37] text-black font-bold py-4 rounded-2xl uppercase text-xs tracking-widest hover:bg-yellow-600 transition">
                Enregistrer les modifications
            </button>
            
            <div class="text-center mt-4">
                <a href="gestion_personnel.php" class="text-[10px] text-zinc-500 uppercase">Annuler et retourner</a>
            </div>
        </form>
    </div>


    <?php include '../layout/footer.php'; ?>
</body>
</html>