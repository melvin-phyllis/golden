<?php
// On récupère les demandes qui n'ont pas encore été traitées avoir acces  la demande d'attente
$demandes = $pdo->query("SELECT * FROM demandes_acces WHERE statut = 'en_attente' ORDER BY date_demande DESC")->fetchAll();
?>

<div class="mt-10">
    <h3 class="text-[#D4AF37] font-serif text-xl mb-6 uppercase tracking-widest flex items-center gap-3">
        <i class="fa-solid fa-bell animate-swing text-sm"></i> Demandes d'accès en attente
    </h3>

    <?php if (empty($demandes)): ?>
        <p class="text-zinc-600 text-[10px] uppercase tracking-widest italic bg-zinc-900/20 p-4 rounded-xl border border-zinc-800/50">
            Aucune nouvelle demande pour le moment.
        </p>
    <?php else: ?>
        <div class="grid grid-cols-1 gap-4">
            <?php foreach ($demandes as $d): ?>
                <div class="bg-zinc-900/40 border border-zinc-800 p-6 rounded-3xl flex justify-between items-center group hover:border-zinc-700 transition-all shadow-lg">
                    <div>
                        <p class="text-white font-bold text-sm"><?= htmlspecialchars($d['nom']) ?></p>
                        <p class="text-zinc-500 text-[9px] uppercase tracking-[0.2em] mt-1">
                            <span class="text-[#D4AF37] font-black italic"><?= htmlspecialchars($d['poste']) ?></span> • <?= htmlspecialchars($d['email']) ?>
                        </p>
                    </div>
                    
                    <div class="flex gap-3">
                        <a href="inscription_collaborateur.php?id_demande=<?= $d['id'] ?>&nom=<?= urlencode($d['nom']) ?>&email=<?= urlencode($d['email']) ?>" 
                           class="bg-[#D4AF37] text-black px-5 py-2.5 rounded-xl text-[10px] font-black uppercase hover:bg-yellow-500 hover:scale-105 transition-all shadow-lg shadow-yellow-900/10">
                            Accepter
                        </a>

                        <a href="gestion_demandes.php?action=rejeter&id=<?= $d['id'] ?>" 
                           onclick="return confirm('Voulez-vous vraiment rejeter cette demande ?')"
                           class="bg-zinc-800 text-zinc-400 px-5 py-2.5 rounded-xl text-[10px] font-black uppercase hover:bg-red-900 hover:text-white transition-all">
                            Rejeter
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; // Fermeture du IF ?>
</div>