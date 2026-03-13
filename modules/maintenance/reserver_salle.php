<?php
require_once '../../config/db.php';

// On récupère l'ID de la salle depuis l'URL
$salle_id = $_GET['id'] ?? null;
if (!$salle_id) { header("Location: gestion_salles.php"); exit(); }

$stmt = $pdo->prepare("SELECT * FROM salles WHERE id = ?");
$stmt->execute([$salle_id]);
$salle = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réserver <?= $salle['nom_salle'] ?> | Prestige</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#050505] text-white p-8">
    <div class="max-w-2xl mx-auto bg-zinc-900/50 p-8 rounded-[2.5rem] border border-zinc-800 shadow-2xl">
        <h2 class="text-[#D4AF37] font-serif text-2xl mb-2 uppercase tracking-widest">Réservation</h2>
        <p class="text-zinc-500 text-xs mb-8 uppercase tracking-widest">Salle : <?= $salle['nom_salle'] ?></p>

        <form action="traitement_reservation_salle.php" method="POST" class="space-y-6">
            <input type="hidden" name="salle_id" value="<?= $salle['id'] ?>">
            <input type="hidden" id="tarif_heure" value="<?= $salle['tarif_heure'] ?>">
            <input type="hidden" id="tarif_jour" value="<?= $salle['tarif_jour'] ?>">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] text-zinc-500 uppercase ml-2">Nom du Client / Entreprise</label>
                    <input type="text" name="nom_client" required class="w-full bg-black border border-zinc-800 p-4 rounded-xl outline-none focus:border-[#D4AF37]">
                </div>

                <div>
                    <label class="text-[10px] text-zinc-500 uppercase ml-2">Date de l'évènement</label>
                    <input type="date" name="date_res" required class="w-full bg-black border border-zinc-800 p-4 rounded-xl outline-none focus:border-[#D4AF37]">
                </div>
            </div>







<div class="mt-8 border-t border-zinc-800 pt-6">
    <p class="text-[#D4AF37] text-[10px] font-black uppercase tracking-widest mb-4">Identification du Client / Responsable</p>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <select name="type_piece" class="w-full bg-zinc-900 border border-zinc-800 rounded-xl p-4 text-white text-xs">
                <option value="CNI">CNI</option>
                <option value="Passeport">Passeport</option>
                <option value="Registre Commerce">RCCM (Si Entreprise)</option>
            </select>
        </div>
        <div>
            <input type="text" name="num_piece" placeholder="Numéro du document" class="w-full bg-zinc-900 border border-zinc-800 rounded-xl p-4 text-white text-xs">
        </div>
    </div>
</div>












            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] text-zinc-500 uppercase ml-2">Heure Début</label>
                    <input type="time" name="h_debut" required class="w-full bg-black border border-zinc-800 p-4 rounded-xl outline-none">
                </div>
                <div>
                    <label class="text-[10px] text-zinc-500 uppercase ml-2">Heure Fin</label>
                    <input type="time" name="h_fin" required class="w-full bg-black border border-zinc-800 p-4 rounded-xl outline-none">
                </div>
            </div>

            <hr class="border-zinc-800 my-6">

            <h3 class="text-[#D4AF37] text-xs uppercase font-bold mb-4 tracking-widest">Services Additionnels</h3>
            <div class="grid grid-cols-2 gap-4">
                <label class="flex items-center gap-3 bg-black p-4 rounded-xl border border-zinc-800 cursor-pointer hover:border-[#D4AF37]">
                    <input type="checkbox" name="opt_restau" value="1" class="accent-[#D4AF37]">
                    <span class="text-xs uppercase tracking-widest">Restauration (+20%)</span>
                </label>
                <label class="flex items-center gap-3 bg-black p-4 rounded-xl border border-zinc-800 cursor-pointer hover:border-[#D4AF37]">
                    <input type="checkbox" name="opt_equip" value="1" class="accent-[#D4AF37]">
                    <span class="text-xs uppercase tracking-widest">Équipements (Fixe)</span>
                </label>
            </div>

            <button type="submit" class="w-full bg-[#D4AF37] text-black font-bold py-5 rounded-2xl uppercase text-xs tracking-[0.2em] mt-8">
                Confirmer & Générer la Facture
            </button>
        </form>
    </div>

    <?php include '../layout/footer.php'; ?>
</body>
</html>