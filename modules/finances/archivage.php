<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. Connexion sécurisée
require_once '../../config/db.php';

if (!isset($pdo)) {
    die("<div class='p-10 text-red-500 bg-black'>Erreur critique : Connexion PDO absente.</div>");
}

try {
    // 2. Récupération fusionnée des ventes (Chambres + Salles)
    // On utilise les colonnes identifiées sur tes photos : date_arrivee, montant_total, acompte_paye
    $query = "
    (SELECT date_arrivee as date_ref, 'Hébergement' as type, montant_total as total, acompte_paye as paye, 'Client Hotel' as client 
     FROM reservations 
     WHERE MONTH(date_arrivee) = MONTH(CURRENT_DATE) 
     AND statut != 'Archivé') -- FILTRE POUR VIDER LE TABLEAU
    UNION ALL
    (SELECT date_reservation as date_ref, 'Location Salle' as type, montant_total as total, montant_total as paye, nom_client as client 
     FROM reservations_salles 
     WHERE MONTH(date_reservation) = MONTH(CURRENT_DATE)
     AND statut_reservation != 'Archivé') -- FILTRE POUR VIDER LE TABLEAU
    ORDER BY date_ref DESC";
    
    $ventes_globales = $pdo->query($query)->fetchAll();
    $count_trans = count($ventes_globales);

} catch (PDOException $e) {
    $ventes_globales = [];
    $count_trans = 0;
}
?>

<div class="bg-zinc-900/40 rounded-[2.5rem] border border-zinc-800 overflow-hidden shadow-2xl">



<!-- Cela oblige le comptable à imprimer son rapport avant de vider la vue. -->

<div class="flex justify-end mb-4">
    <form method="POST" onsubmit="return confirm('Attention : Cette action va archiver toutes les ventes du mois en cours. Assurez-vous d avoir imprimé votre rapport !');">
        <button type="submit" name="cloturer_mois" class="bg-red-900/20 border border-red-500/50 text-red-500 text-[9px] font-bold uppercase px-4 py-2 rounded-lg hover:bg-red-500 hover:text-white transition">
            <i class="fa-solid fa-box-archive mr-2"></i> Clôturer et Vider le mois
        </button>
    </form>
</div>

<?php
// TRAITEMENT DE LA CLÔTURE
if (isset($_POST['cloturer_mois'])) {
    try {
        // On marque les réservations et réservations de salles comme 'Archivées'
        // On suppose que tu as une colonne 'statut' (vue sur tes images)
        $pdo->query("UPDATE reservations SET statut = 'Archivé' WHERE MONTH(date_arrivee) = MONTH(CURRENT_DATE) AND statut != 'Archivé'");
        $pdo->query("UPDATE reservations_salles SET statut_reservation = 'Archivé' WHERE MONTH(date_reservation) = MONTH(CURRENT_DATE)");
        
        echo "<script>alert('Mois clôturé avec succès ! Les données sont archivées.'); window.location.href='compta_expert.php';</script>";
    } catch (PDOException $e) {
        echo "Erreur lors de l'archivage : " . $e->getMessage();
    }
}



?>

    <div class="p-8 border-b border-zinc-800 flex justify-between items-center bg-zinc-900/20">
        <div>
            <h3 class="text-white text-xs font-bold uppercase tracking-widest">Journal des Ventes Consolidé</h3>
            <p class="text-[9px] text-zinc-500 mt-1 uppercase">Mois de <?= date('F Y') ?></p>
        </div>
        <div class="text-right">
            <span class="text-[10px] text-zinc-500 font-mono">Transactions :</span>
            <span class="text-[#D4AF37] font-bold ml-2"><?= $count_trans ?></span>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[9px] uppercase tracking-[0.2em] text-zinc-500 border-b border-zinc-800">
                    <th class="p-6">Date</th>
                    <th class="p-6">Désignation / Client</th>
                    <th class="p-6">Pôle</th>
                    <th class="p-6">Statut Cash</th>
                    <th class="p-6 text-right">Montant (CFA)</th>
                </tr>
            </thead>
            <tbody class="text-xs">
                <?php if(empty($ventes_globales)): ?>
                    <tr>
                        <td colspan="5" class="p-10 text-center text-zinc-600 italic font-light">
                            Aucune transaction enregistrée pour ce mois.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($ventes_globales as $vente): ?>
                    <tr class="border-b border-zinc-800/30 hover:bg-white/[0.02] transition">
                        <td class="p-6 font-mono text-zinc-500"><?= date('d/m/y', strtotime($vente['date_ref'])) ?></td>
                        <td class="p-6">
                            <p class="text-white font-semibold italic"><?= htmlspecialchars($vente['client']) ?></p>
                        </td>
                        <td class="p-6">
                            <span class="px-3 py-1 rounded-lg bg-zinc-800/50 text-[9px] font-bold text-zinc-400 border border-zinc-700">
                                <?= $vente['type'] ?>
                            </span>
                        </td>
                        <td class="p-6">
                            <?php if($vente['paye'] >= $vente['total']): ?>
                                <span class="text-green-500 flex items-center gap-2"><i class="fa-solid fa-check-double text-[8px]"></i> Soldé</span>
                            <?php else: ?>
                                <span class="text-amber-500 flex items-center gap-2"><i class="fa-solid fa-clock text-[8px]"></i> Acompte</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-6 text-right font-mono font-bold text-white"><?= number_format($vente['total'], 0, ',', ' ') ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../layout/footer.php'; ?>