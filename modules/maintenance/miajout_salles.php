<?php
session_start();
require_once '../../config/db.php';

// Récupération des salles
$stmt = $pdo->query("SELECT * FROM salles ORDER BY id DESC");
$salles = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../layout/header.php';
?>

<div class="max-w-7xl mx-auto space-y-10">

    <!-- TITRE -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-serif text-white">
         Catalogue des Salles Bemar
        </h1><br>
       <p class="text-zinc-500 text-xs mt-1 uppercase">Jour de fête: au Bemar nous somme au top avec les événements. <br>  planification et Facturation des Salles</p>
        <a href="ajouter_salle.php" 
        class="bg-[#D4AF37] text-black px-6 py-3 rounded-xl font-bold hover:bg-white transition">
            <i class="fa fa-plus"></i> Nouvelle salle
        </a>
    </div>

    <!-- TABLE -->
    <div class="bg-zinc-900/40 border border-zinc-800 rounded-3xl p-6">

        <table class="w-full text-sm text-white">

            <thead class="text-zinc-400 border-b border-zinc-800">
                <tr class="text-left">
                    <th class="p-3">Image</th>
                    <th class="p-3">Nom</th>
                    <th class="p-3">Type</th>
                    <th class="p-3">Capacité</th>
                    <th class="p-3">Tarif Heure</th>
                    <th class="p-3">Tarif Jour</th>
                    <th class="p-3">Statut</th>
                    <th class="p-3">Actions</th>
                </tr>
            </thead>

            <tbody>

            <?php foreach ($salles as $salle): ?>

                <tr class="border-b border-zinc-800 hover:bg-zinc-800/40 transition">

                    <!-- IMAGE -->
                    <td class="p-3">
                        <img src="<?= htmlspecialchars(BASE_URL . 'assets/img/salles/' . $salle['image_salle']) ?>" 
                        class="w-16 h-12 object-cover rounded-lg">
                    </td>

                    <!-- NOM -->
                    <td class="p-3 font-semibold">
                        <?= htmlspecialchars($salle['nom_salle']) ?>
                    </td>

                    <!-- TYPE -->
                    <td class="p-3">
                        <?= htmlspecialchars($salle['type_salle']) ?>
                    </td>

                    <!-- CAPACITE -->
                    <td class="p-3">
                        <?= $salle['capacite'] ?> pers.
                    </td>

                    <!-- TARIF HEURE -->
                    <td class="p-3 text-[#D4AF37] font-bold">
                        <?= number_format($salle['tarif_heure'],0,'',' ') ?> CFA
                    </td>

                    <!-- TARIF JOUR -->
                    <td class="p-3 text-[#D4AF37] font-bold">
                        <?= number_format($salle['tarif_jour'],0,'',' ') ?> CFA
                    </td>

                    <!-- STATUT -->
                    <td class="p-3">

                        <?php if ($salle['statut'] == "Disponible"): ?>

                            <span class="bg-green-500/20 text-green-400 px-3 py-1 rounded-full text-xs">
                                Disponible
                            </span>

                        <?php elseif ($salle['statut'] == "Maintenance"): ?>

                            <span class="bg-red-500/20 text-red-400 px-3 py-1 rounded-full text-xs">
                                Maintenance
                            </span>

                        <?php else: ?>

                            <span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full text-xs">
                                Réservé
                            </span>

                        <?php endif; ?>

                    </td>

                    <!-- ACTIONS -->
                    <td class="p-3 flex gap-3">

                        <a href="modifier_salle.php?id=<?= $salle['id'] ?>" 
                        class="text-blue-400 hover:text-white">
                            <i class="fa fa-edit"></i>
                        </a>

                        <a href="supprimer_salle.php?id=<?= $salle['id'] ?>" 
                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette salle et tout son historique ?')" 
                        class="text-red-400 hover:text-white">
                            <i class="fa fa-trash"></i>
                        </a>


                    </td>

                </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</div>

<?php include '../layout/footer3.php'; ?>