<?php
session_start();
require_once '../../config/db.php';

// --- LOGIQUE DE SUPPRESSION ---
if (isset($_GET['annuler_id'])) {
    $id_a_supprimer = $_GET['annuler_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM commandes WHERE id = ?");
        $stmt->execute([$id_a_supprimer]);
        // Redirection pour rafraîchir la liste sans l'ID dans l'URL
        header("Location: liste_commandes.php?msg=supprime");
        exit();
    } catch (PDOException $e) {
        $error = "Impossible d'annuler cette commande.";
    }
}

// --- RÉCUPÉRATION DES COMMANDES ---
$sql = "SELECT c.*, ch.numero_chambre 
        FROM commandes c 
        LEFT JOIN chambres ch ON c.chambre_id = ch.id 
        ORDER BY c.date_commande DESC";
$commandes = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique des Ventes | Prestige</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #050505; color: #fff; font-family: 'Inter', sans-serif; }
        .gold-border { border: 1px solid rgba(212, 175, 55, 0.2); }
        .btn-annuler { color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); transition: 0.3s; }
        .btn-annuler:hover { background: #ef4444; color: white; }
    </style>
</head>
<body class="p-8">
    <div class="max-w-6xl mx-auto">
        
        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-serif text-[#D4AF37] uppercase tracking-widest">Journal de Caisse Bémar</h1>
                <p class="text-zinc-500 text-xs mt-1">Suivi en temps réel des consommations bar et restaurant</p>
            </div>
            <a href="pos_bar.php" class="bg-zinc-900 border border-zinc-700 px-6 py-2 rounded-xl text-[10px] uppercase font-bold tracking-wider hover:border-[#D4AF37] transition">
                ← Retour au POS
            </a>
        </div>

        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'supprime'): ?>
            <div class="bg-red-500/10 border border-red-500/50 text-red-500 p-4 rounded-xl mb-6 text-xs text-center">
                La commande a été annulée avec succès.
            </div>
        <?php endif; ?>

        <div class="bg-zinc-900/50 rounded-3xl overflow-hidden gold-border shadow-2xl">
            <table class="w-full text-left text-sm">
                <thead class="bg-zinc-800/80 text-[#D4AF37] uppercase text-[10px] tracking-tighter">
                    <tr>
                        <th class="p-5">Date & Heure</th>
                        <th class="p-5">Affectation / Client</th>
                        <th class="p-5">Paiement</th>
                        <th class="p-5 text-right">Montant Total</th>
                        <th class="p-5 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    <?php if(empty($commandes)): ?>
                        <tr><td colspan="5" class="p-10 text-center text-zinc-600 italic">Aucune vente enregistrée pour le moment.</td></tr>
                    <?php endif; ?>

                    <?php foreach($commandes as $com): ?>
                    <tr class="hover:bg-zinc-800/30 transition">
                        <td class="p-5 text-zinc-400 font-mono text-xs">
                            <?= date('d/m/Y | H:i', strtotime($com['date_commande'])) ?>
                        </td>
                        <td class="p-5 font-bold">
                            <?php if($com['chambre_id']): ?>
                                <span class="text-blue-400">CHAMBRE <?= $com['numero_chambre'] ?></span>
                            <?php else: ?>
                                <span class="text-zinc-500 uppercase text-[10px]">Client de passage</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-5 italic text-zinc-500 text-xs">
                            <?= $com['statut_paiement'] ?>
                        </td>
                        <td class="p-5 text-right font-serif text-[#D4AF37] text-lg">
                            <?= number_format($com['total_commande'], 0, ',', ' ') ?> <small class="text-[10px]">FCFA</small>
                        </td>
                        <td class="p-5 text-center">
                            <a href="liste_commandes.php?annuler_id=<?= $com['id'] ?>" 
                               onclick="return confirm('Êtes-vous sûr de vouloir annuler cette commande ?')"
                               class="btn-annuler px-4 py-1.5 rounded-lg text-[10px] uppercase font-bold">
                                Annuler
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="mt-8 flex justify-end">
            <div class="bg-[#D4AF37] text-black px-8 py-4 rounded-2xl shadow-xl">
                <p class="text-[10px] uppercase font-bold opacity-70">Total Caisse Actuel</p>
                <p class="text-2xl font-serif font-bold">
                    <?php 
                        $total_caisse = array_sum(array_column($commandes, 'total_commande'));
                        echo number_format($total_caisse, 0, ',', ' ');
                    ?> FCFA
                </p>
            </div>
        </div>
    </div>
    
    <?php include '../layout/footer.php'; ?>
</body>
</html>