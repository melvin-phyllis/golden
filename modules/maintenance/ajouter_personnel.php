<?php
session_start();
require_once '../../config/db.php';

if (isset($_POST['enregistrer'])) {
    $nom = $_POST['nom_complet'];
    $fonction = $_POST['fonction'];
    $tel = $_POST['telephone'];

    // Mise à jour vers le nouveau nom de table : equipe_prestige
    $stmt = $pdo->prepare("INSERT INTO equipe_prestige (nom_complet, fonction, telephone) VALUES (?, ?, ?)");
    $stmt->execute([$nom, $fonction, $tel]);
    
    header("Location: gestion_personnel.php?success=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recrutement | Prestige</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #050505; color: #fff; }
        .gold-border { border: 1px solid rgba(212, 175, 55, 0.3); }
    </style>
</head>
<body class="p-10 flex justify-center">
    <div class="max-w-md w-full bg-zinc-900 p-8 rounded-3xl gold-border shadow-2xl">
        <h2 class="text-[#D4AF37] font-serif text-2xl mb-6 uppercase tracking-widest text-center">Fiche Employé</h2>
        
        <form method="POST" class="space-y-5">
            <div>
                <label class="text-[10px] uppercase text-zinc-500 ml-2">Nom Complet</label>
                <input type="text" name="nom_complet" required class="w-full bg-black border border-zinc-800 p-4 rounded-2xl outline-none focus:border-[#D4AF37] text-sm">
            </div>

            <div>
                <label class="text-[10px] uppercase text-zinc-500 ml-2">Fonction</label>
                <select name="fonction" class="w-full bg-black border border-zinc-800 p-4 rounded-2xl outline-none focus:border-[#D4AF37] text-sm">
                    <option>Ménage</option>
                    <option>Gouvernance</option>
                    <option>Maintenance</option>
                    <option>Réception</option>
                </select>
            </div>

            <div>
                <label class="text-[10px] uppercase text-zinc-500 ml-2">Téléphone (Urgence)</label>
                <input type="text" name="telephone" placeholder="+225 ..." required class="w-full bg-black border border-zinc-800 p-4 rounded-2xl outline-none focus:border-[#D4AF37] text-sm">
            </div>

            <button type="submit" name="enregistrer" class="w-full bg-[#D4AF37] text-black font-bold py-4 rounded-2xl uppercase text-xs tracking-widest hover:bg-yellow-600 transition">
                Enregistrer l'employé
            </button>
        </form>
    </div>

    <?php include '../layout/footer.php'; ?>
</body>
</html>