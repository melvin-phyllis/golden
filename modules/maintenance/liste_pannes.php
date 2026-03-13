
<?php
session_start();
require_once '../../config/db.php';

// Récupération des pannes avec le numéro de chambre (Jointure avec la table chambres)
$sql = "SELECT m.*, ch.numero_chambre 
        FROM maintenance m 
        LEFT JOIN chambres ch ON m.chambre_id = ch.id 
        ORDER BY m.priorite DESC, m.date_signalement DESC";
$pannes = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Maintenance | Prestige</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #050505; color: #fff; font-family: 'Inter', sans-serif; }
        .gold-border { border: 1px solid rgba(212, 175, 55, 0.2); }
        .priorite-Urgente { color: #ef4444; font-weight: bold; }
        .statut-Terminé { opacity: 0.5; }
    </style>
</head>
<body class="p-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-serif text-[#D4AF37] uppercase tracking-widest">Suivi Maintenance</h1>
            <div class="flex gap-3">
                <a href="gestion_personnel.php" class="bg-zinc-800 text-white px-4 py-2 rounded-xl text-[10px] font-bold uppercase">Personnel</a>
                <a href="portail_equipe.php" class="bg-[#D4AF37] text-black px-6 py-2 rounded-xl text-xs font-bold uppercase">Ménagère</a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4">
            <?php if(empty($pannes)): ?>
                <p class="text-center text-zinc-600 italic py-10">Aucun problème signalé pour le moment.</p>
            <?php endif; ?>

            <?php foreach($pannes as $panne): ?>
            <div class="bg-zinc-900 p-6 rounded-3xl gold-border flex justify-between items-center <?= ($panne['statut'] == 'Terminé') ? 'statut-Terminé' : '' ?>">
                <div class="flex items-center gap-4">
                    <?php if(!empty($panne['image_preuve'])): ?>
                        <a href="../../uploads/maintenance/<?= $panne['image_preuve'] ?>" target="_blank" class="block w-16 h-16 rounded-xl overflow-hidden border border-zinc-700">
                            <img src="../../uploads/maintenance/<?= $panne['image_preuve'] ?>" class="w-full h-full object-cover hover:scale-110 transition">
                        </a>
                    <?php else: ?>
                        <div class="w-16 h-16 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-600">
                            <span class="text-xs">No img</span>
                        </div>
                    <?php endif; ?>

                    <div>
                        <span class="text-[10px] uppercase text-zinc-500">
                            <?= ($panne['chambre_id'] == 0) ? 'Parties Communes' : 'Chambre ' . $panne['numero_chambre'] ?>
                        </span>
                        <h3 class="text-lg font-bold <?= ($panne['priorite'] == 'Urgente') ? 'priorite-Urgente' : '' ?>">
                            <?= htmlspecialchars($panne['description_probleme']) ?>
                        </h3>
                        <p class="text-[10px] text-zinc-400">Signalé le : <?= date('d/m H:i', strtotime($panne['date_signalement'])) ?></p>
                    </div>
                </div>
                
                <div class="flex items-center gap-4">
                    <span class="text-[10px] px-3 py-1 border border-zinc-700 rounded-full uppercase"><?= $panne['statut'] ?></span>
                    
                    <?php if($panne['statut'] != 'Terminé'): ?>
                        <form action="maj_statut_panne.php" method="POST">
                            <input type="hidden" name="id" value="<?= $panne['id'] ?>">
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg text-[10px] font-bold uppercase hover:bg-green-700">Réparé</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>


    <?php include '../layout/footer.php'; ?>
</body>
</html>