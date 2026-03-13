<?php
session_start();
require_once '../../config/db.php';

// if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Réceptionniste') {
//     header('Location: ../../login.php');
//     exit();
// }

$res_id = $_GET['res_id'] ?? null;
if (!$res_id) { header('Location: reservation.php'); exit(); }

// Récupération des détails complets
$query = "SELECT r.*, c.nom_complet, ch.numero_chambre, ch.id as room_id, t.tarif_nuit, t.libelle 
          FROM reservations r
          JOIN clients c ON r.client_id = c.id
          JOIN chambres ch ON r.chambre_id = ch.id
          JOIN types_chambre t ON ch.type_id = t.id
          WHERE r.id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$res_id]);
$res = $stmt->fetch();

if (!$res) { die("Réservation introuvable."); }

// Calcul financier automatique
$start = new DateTime($res['date_arrivee']);
$end = new DateTime($res['date_depart']);
$nuits = $start->diff($end)->days ?: 1;
$total_sejour = $nuits * $res['tarif_nuit'];

// Extraction de la caution
preg_match('/Caution: (\d+)/', $res['preferences'], $matches);
$caution = isset($matches[1]) ? (int)$matches[1] : 0;

// TRAITEMENT DU CHECK-OUT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $frais_extra = $_POST['extras'] ?? 0; // Ex: Minibar, Restaurant, Blanchisserie
    $total_final = $total_sejour + $frais_extra;

    try {
        $pdo->beginTransaction();

        // 1. Clôturer la réservation et enregistrer le montant final (si tu as la colonne)
        $updateRes = $pdo->prepare("UPDATE reservations SET statut = 'Terminée' WHERE id = ?");
        $updateRes->execute([$res_id]);

        // 2. Libérer la chambre et changer son état pour le ménage
        // J'utilise 'À nettoyer' comme statut principal
        $updateRoom = $pdo->prepare("UPDATE chambres SET statut = 'À nettoyer' WHERE id = ?");
        $updateRoom->execute([$res['room_id']]);

        $pdo->commit();
        header("Location: facture.php?res_id=$res_id&extrassuccess=$frais_extracheckout");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Erreur critique lors du Check-out: " . $e->getMessage());
    }
}

?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Check-out Prestige | #<?php echo $res['numero_chambre']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #050505; color: #fff; font-family: 'Inter', sans-serif; }
        .gold-border { border: 1px solid rgba(212, 175, 55, 0.3); }
        .gold-text { color: #D4AF37; }
        .card-dark { background: #111; border-radius: 20px; }
    </style>
</head>
<body class="p-10 flex items-center justify-center min-h-screen">

    <div class="max-w-2xl w-full card-dark gold-border p-10 shadow-2xl">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-serif gold-text uppercase tracking-widest">Clôture de Séjour</h1>
            <p class="text-gray-500 italic mt-1">Départ de <?php echo htmlspecialchars($res['nom_complet']); ?></p>
        </div>

        <div class="space-y-6">
            <div class="flex justify-between border-b border-zinc-800 pb-4">
                <span class="text-gray-400">Hébergement (<?php echo $nuits; ?> nuits)</span>
                <span class="font-bold"><?php echo number_format($total_sejour, 0, ',', ' '); ?> FCFA</span>
            </div>

            <div class="flex justify-between border-b border-zinc-800 pb-4">
                <span class="text-gray-400">Garantie déposée (Caution)</span>
                <span class="text-green-500 font-bold"><?php echo number_format($caution, 0, ',', ' '); ?> FCFA</span>
            </div>

            <form method="POST" class="space-y-6 pt-4">
                <div>
                    <label class="text-[10px] uppercase text-gray-500 font-bold block mb-2">Frais supplémentaires (Minibar, Blanchisserie...)</label>
                    <input type="number" name="extras" value="0" min="0" 
                           class="w-full bg-zinc-900 border border-zinc-800 rounded-lg p-3 text-white outline-none focus:border-[#D4AF37] transition">
                </div>

                <div class="bg-zinc-900/80 p-6 rounded-xl border border-dashed border-zinc-700">
                    <div class="flex justify-between items-center">
                        <span class="text-lg gold-text uppercase font-serif tracking-widest">Solde Final</span>
                        <span class="text-3xl font-light tabular-nums" id="total_display">
                            <?php echo number_format($total_sejour, 0, ',', ' '); ?> FCFA
                        </span>
                    </div>
                </div>

                <div class="bg-zinc-800/30 p-4 rounded-xl border border-zinc-700">
                    <p class="text-[10px] text-zinc-500 uppercase font-bold">⚠️ Rappel Sécurité :</p>
                    <p class="text-xs text-zinc-400">La chambre <span class="gold-text">#<?php echo $res['numero_chambre']; ?></span> sera marquée <span class="text-yellow-500 uppercase font-bold">À nettoyer</span> immédiatement après validation.</p>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4">
                    <a href="registre.php" class="flex items-center justify-center text-gray-500 uppercase text-xs font-bold tracking-widest hover:text-white transition">
                        Annuler
                    </a>
                    <button type="submit" class="bg-[#D4AF37] text-black font-bold py-4 rounded-xl uppercase tracking-widest shadow-lg hover:scale-105 transition">
                        Solder & Libérer
                    </button>
                </div>
            </form>
        </div>
    </div>

    

    <script>
        const extrasInput = document.querySelector('input[name="extras"]');
        const totalDisplay = document.getElementById('total_display');
        const baseTotal = <?php echo (int)$total_sejour; ?>;

        extrasInput.addEventListener('input', (e) => {
            const val = parseInt(e.target.value) || 0;
            const newTotal = baseTotal + val;
            // Formatage propre avec espaces
            totalDisplay.innerText = newTotal.toLocaleString('fr-FR').replace(/\s/g, ' ') + " FCFA";
        });
    </script>


<?php include '../layout/footer.php'; ?>
</body>
</html>