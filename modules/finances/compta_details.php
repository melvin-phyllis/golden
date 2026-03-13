<div class="grid grid-cols-1 gap-10 mt-12">
        
        <div class="bg-zinc-900/40 rounded-[2.5rem] border border-zinc-800 overflow-hidden">
            <div class="p-8 border-b border-zinc-800 flex justify-between items-center">
                <h3 class="text-white text-xs font-bold uppercase tracking-widest">Rapport des Ventes (Journalier)</h3>
                <span class="text-[10px] text-zinc-500 italic font-mono">Total Transactions: <?= count($pdo->query("SELECT id FROM reservations WHERE MONTH(date_arrivee) = MONTH(CURRENT_DATE)")->fetchAll()) ?></span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[9px] uppercase tracking-[0.2em] text-zinc-500 border-b border-zinc-800">
                            <th class="p-6">Date</th>
                            <th class="p-6">Client / Source</th>
                            <th class="p-6">Activité</th>
                            <th class="p-6">Statut Paiement</th>
                            <th class="p-6 text-right">Montant Brut</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs">
                        <?php
                        // Fusion des ventes Chambres et Salles pour le rapport global
                        $ventes_globales = $pdo->query("
                            (SELECT date_arrivee as date, 'Hébergement' as type, montant_total, acompte_paye FROM reservations WHERE MONTH(date_arrivee) = MONTH(CURRENT_DATE))
                            UNION ALL
                            (SELECT date_reservation as date, 'Location Salle' as type, montant_total, 'Total' as acompte FROM reservations_salles WHERE MONTH(date_reservation) = MONTH(CURRENT_DATE))
                            ORDER BY date DESC
                        ")->fetchAll();

                        foreach($ventes_globales as $vente): 
                        ?>
                        <tr class="border-b border-zinc-800/50 hover:bg-white/5 transition">
                            <td class="p-6 font-mono text-zinc-500"><?= date('d/m/Y', strtotime($vente['date'])) ?></td>
                            <td class="p-6 text-white font-bold italic">Client Prestige</td>
                            <td class="p-6">
                                <span class="px-3 py-1 rounded-full bg-zinc-800 text-[9px] uppercase font-bold text-zinc-400 border border-zinc-700">
                                    <?= $vente['type'] ?>
                                </span>
                            </td>
                            <td class="p-6">
                                <?php if($vente['montant_total'] > 0): ?>
                                    <span class="text-green-500"><i class="fa-solid fa-circle-check mr-2"></i>Encaissé</span>
                                <?php else: ?>
                                    <span class="text-orange-500 italic">En attente</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-6 text-right font-bold text-white"><?= number_format($vente['montant_total'], 0, ',', ' ') ?> CFA</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-gradient-to-br from-zinc-900 to-black p-8 rounded-[2.5rem] border border-zinc-800">
                <h4 class="text-[#D4AF37] text-[10px] font-black uppercase tracking-widest mb-6">Analyse Cash-Flow</h4>
                <div class="space-y-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-zinc-500">Entrées de fonds (Revenus)</span>
                        <span class="text-green-500">+ <?= number_format($revenu_brut_total, 0, ',', ' ') ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-500">Sorties de fonds (Dépenses)</span>
                        <span class="text-red-500">- <?= number_format($total_depenses, 0, ',', ' ') ?></span>
                    </div>
                    <div class="pt-4 border-t border-zinc-800 flex justify-between font-bold text-lg">
                        <span class="text-white">Solde de Trésorerie</span>
                        <span class="<?= ($flux_tresorerie >= 0) ? 'text-[#D4AF37]' : 'text-red-600' ?>">
                            <?= number_format($flux_tresorerie, 0, ',', ' ') ?> CFA
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-zinc-900/10 p-8 rounded-[2.5rem] border border-dashed border-zinc-800 flex flex-col justify-center">
                <i class="fa-solid fa-quote-left text-[#D4AF37] mb-4 opacity-20 text-3xl"></i>
                <p class="text-zinc-500 text-xs italic leading-relaxed">
                    Ce rapport de bénéfice brut ne prend pas en compte les amortissements du mobilier de l'hôtel ni les taxes gouvernementales de fin d'exercice. Il s'agit d'une vision purement opérationnelle du "Cash-Flow" mensuel.
                </p>
            </div>
        </div>
    </div>