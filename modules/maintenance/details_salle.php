<?php
session_start();
require_once '../../config/db.php';

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: gestion_salles.php"); exit(); }

// 1. Récupérer les infos de la salle
$stmt = $pdo->prepare("SELECT * FROM salles WHERE id = ?");
$stmt->execute([$id]);
$salle = $stmt->fetch();

// 2. Récupérer les réservations à venir pour cette salle
$stmt_res = $pdo->prepare("SELECT * FROM reservations_salles WHERE salle_id = ? AND date_reservation >= CURDATE() ORDER BY date_reservation ASC");
$stmt_res->execute([$id]);
$reservations = $stmt_res->fetchAll();

if (!$salle) { echo "Salle introuvable"; exit(); }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails <?= $salle['nom_salle'] ?> | Prestige</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#050505] text-white p-8">
    <div class="max-w-6xl mx-auto">
        
        <a href="gestion_salles.php" class="text-zinc-500 text-xs uppercase tracking-widest hover:text-[#D4AF37] transition">← Retour au catalogue</a>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 mt-6">
            
            <div class="space-y-6">
                <div class="rounded-[2.5rem] overflow-hidden border border-zinc-800 h-80 shadow-2xl">
                    <img src="<?= htmlspecialchars(BASE_URL . 'assets/img/salles/' . $salle['image_salle']) ?>" class="w-full h-full object-cover">
                </div>
                
                <div class="bg-zinc-900/50 p-6 rounded-[2rem] border border-zinc-800">
                    <h3 class="text-[#D4AF37] text-[10px] uppercase font-bold mb-4 tracking-widest">État Opérationnel</h3>
                    <form action="maj_statut_salle.php" method="POST" class="flex gap-2">
                        <input type="hidden" name="salle_id" value="<?= $salle['id'] ?>">
                        <select name="nouveau_statut" class="flex-1 bg-black border border-zinc-700 p-3 rounded-xl text-xs outline-none focus:border-[#D4AF37]">
                            <option value="Disponible" <?= $salle['statut'] == 'Disponible' ? 'selected' : '' ?>>Disponible</option>
                            <option value="Occupée" <?= $salle['statut'] == 'Occupée' ? 'selected' : '' ?>>Occupée</option>
                            <option value="Maintenance" <?= $salle['statut'] == 'Maintenance' ? 'selected' : '' ?>>En Maintenance</option>
                        </select>
                        <button type="submit" class="bg-zinc-800 px-4 rounded-xl hover:bg-zinc-700">OK</button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-8">
                <div>
                    <h1 class="text-4xl font-serif text-[#D4AF37] uppercase"><?= $salle['nom_salle'] ?></h1>
                    <p class="text-zinc-500 text-sm mt-1 uppercase tracking-widest"><?= $salle['type_salle'] ?> — Capacité : <?= $salle['capacite'] ?> Personnes</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-zinc-900/30 p-5 rounded-2xl border border-zinc-800">
                        <p class="text-[9px] text-zinc-500 uppercase mb-1">Tarif Horaire</p>
                        <p class="text-xl font-bold font-mono"><?= number_format($salle['tarif_heure'], 0, ',', ' ') ?> <span class="text-xs text-[#D4AF37]">CFA</span></p>
                    </div>
                    <div class="bg-zinc-900/30 p-5 rounded-2xl border border-zinc-800">
                        <p class="text-[9px] text-zinc-500 uppercase mb-1">Forfait Journée</p>
                        <p class="text-xl font-bold font-mono"><?= number_format($salle['tarif_jour'], 0, ',', ' ') ?> <span class="text-xs text-[#D4AF37]">CFA</span></p>
                    </div>
                </div>

                <div class="bg-zinc-900/30 p-8 rounded-[2rem] border border-zinc-800">
                    <h2 class="text-lg font-bold mb-6 flex items-center gap-3">
                        📅 Prochaines Réservations
                    </h2>
                    
                    <?php if(empty($reservations)): ?>
                        <p class="text-zinc-600 text-sm italic">Aucun évènement prévu pour le moment.</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach($reservations as $res): ?>
                                <div class="flex justify-between items-center p-4 bg-black/40 rounded-xl border-l-4 border-[#D4AF37]">
                                    <div>
                                        <p class="text-xs font-bold uppercase"><?= $res['nom_client'] ?></p>
                                        <p class="text-[10px] text-zinc-500"><?= date('d/m/Y', strtotime($res['date_reservation'])) ?> | <?= $res['heure_debut'] ?> - <?= $res['heure_fin'] ?></p>
                                    </div>
                                    <a href="facture_salle.php?id=<?= $res['id'] ?>" class="text-[10px] bg-zinc-800 px-3 py-1 rounded-lg uppercase font-bold hover:bg-[#D4AF37] hover:text-black transition">Facture</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="pt-10 flex justify-end">
                    <button onclick="confirmDelete()" class="text-red-900 text-[10px] uppercase font-bold hover:text-red-500 transition tracking-widest">
                        Supprimer cette salle du catalogue
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete() {
            if(confirm("Êtes-vous sûr de vouloir supprimer cette salle ? Cette action est irréversible.")) {
                window.location.href = "supprimer_salle.php?id=<?= $salle['id'] ?>";
            }
        }
    </script>
</body>
</html>