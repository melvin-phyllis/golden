
<?php
session_start();
require_once '../../config/db.php';

$chambre_id = $_GET['chambre_id'] ?? null;
if (!$chambre_id) { header('Location: chambres.php'); exit(); }

// Infos chambre
$stmt = $pdo->prepare("SELECT c.*, t.libelle, t.tarif_nuit FROM chambres c JOIN types_chambre t ON c.type_id = t.id WHERE c.id = ?");
$stmt->execute([$chambre_id]);
$chambre = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom_client'];
    $tel = $_POST['telephone'];
    $arr = $_POST['date_arrivee']; 
    $dep = $_POST['date_depart'];
    $paiement = $_POST['mode_paiement'];
    $caution = $_POST['caution'];
    
    // --- CALCUL DU MONTANT TOTAL ---
    $datetime1 = new DateTime($arr);
    $datetime2 = new DateTime($dep);
    $interval = $datetime1->diff($datetime2);
    $nb_nuits = $interval->days;

    // Si le client repart le même jour, on compte au moins 1 nuit
    if ($nb_nuits <= 0) { $nb_nuits = 1; }

    $tarif_unitaire = $chambre['tarif_nuit'];
    $montant_total = $nb_nuits * $tarif_unitaire;
    // -------------------------------

    $type_piece = $_POST['type_piece']; 
    $num_piece = $_POST['num_piece'];   
    $accueil = $_POST['boisson']; 
    $sommeil = $_POST['oreillers']; 

    $prefs = "Paiement: $paiement | Caution: $caution FCFA | Accueil: $accueil | Sommeil: $sommeil | Nuits: $nb_nuits";

    try {
        $pdo->beginTransaction();

        // 1. Enregistrement client
        $insCl = $pdo->prepare("INSERT INTO clients (nom_complet, telephone, type_piece, num_piece) VALUES (?, ?, ?, ?)");
        $insCl->execute([$nom, $tel, $type_piece, $num_piece]);
        $client_id = $pdo->lastInsertId();

        // 2. Enregistrement réservation (On ajoute le montant_total)
        $insRes = $pdo->prepare("INSERT INTO reservations (client_id, chambre_id, date_arrivee, date_depart, statut, preferences, type_piece, num_piece, accueil_service, confort_sommeil, montant_total) VALUES (?, ?, ?, ?, 'Occupée', ?, ?, ?, ?, ?, ?)");
        $insRes->execute([$client_id, $chambre_id, $arr, $dep, $prefs, $type_piece, $num_piece, $accueil, $sommeil, $montant_total]);

        $res_id = $pdo->lastInsertId(); // ID pour la carte de bienvenue

        // 3. Mise à jour statut chambre
        $upCh = $pdo->prepare("UPDATE chambres SET statut = 'Occupée' WHERE id = ?");
        $upCh->execute([$chambre_id]);

        $pdo->commit();
        
        // Impression et Redirection
        echo "<script>
            window.open('print_welcome.php?id=$res_id', '_blank');
            window.location.href = 'reservations.php';
        </script>";
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Erreur : " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Enregistrement Prestige | N°<?php echo $chambre['numero_chambre']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #050505; color: #fff; font-family: 'Inter', sans-serif; }
        .gold-border { border: 1px solid rgba(212, 175, 55, 0.3); }
        .gold-text { color: #D4AF37; }
        .input-luxe { background: #111; border: 1px solid #333; color: white; padding: 12px; border-radius: 8px; width: 100%; outline: none; }
        .input-luxe:focus { border-color: #D4AF37; }
        /* Style pour l'icône du calendrier */
        input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1); cursor: pointer; }
    </style>
</head>
<body class="p-10 flex items-center justify-center min-h-screen">

    <form method="POST" class="max-w-4xl w-full grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <div class="md:col-span-1 space-y-4">
            
            <div class="gold-border p-6 rounded-3xl bg-zinc-900/50">

                <h3 class="gold-text text-[10px] uppercase font-bold tracking-widest mb-4">Unité sélectionnée</h3>




            <?php 
                 $folder = ($chambre['type_id'] == 1) ? "suites" : "standard";
                $img = "../../assets/img/$folder/" . $chambre['numero_chambre'] . ".jpg";
            ?>

            <div class="h-40 rounded-2xl overflow-hidden mb-4">

                <img src="<?php echo $img;?>"
                onerror="this.src='../../assets/img/default.jpg'"
                class="w-full h-full object-cover">

            </div>



                <p class="text-2xl font-serif">
                N° <?php echo $chambre['numero_chambre']; ?></p>


                <p class="text-gray-400 text-sm mb-4"><?php echo $chambre['libelle']; ?></p>




                <div class="mt-4 pt-4 border-t border-zinc-800">

                <p class="text-xl font-light"><?php echo number_format($chambre['tarif_nuit'], 0, ',', ' '); ?> <span class="text-xs">FCFA / NUIT</span></p>
            </div>
        </div>
        </div>
        

        <div class="md:col-span-2 bg-zinc-900 p-8 rounded-3xl gold-border shadow-2xl">
            <h2 class="text-2xl font-serif gold-text mb-8 uppercase tracking-widest">Fiche d'Arrivée au Bémar    hôtel </h2>
            
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="text-[10px] text-gray-500 uppercase font-bold block mb-2">Nom de l'hôte</label>
                    <input type="text" name="nom_client" class="input-luxe" placeholder="Ex: Jean-Luc Prestige" required>
                </div>
                <div>
                    <label class="text-[10px] text-gray-500 uppercase font-bold block mb-2">Téléphone</label>
                    <input type="tel" name="telephone" class="input-luxe" placeholder="+225 ..." required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="text-[10px] text-gray-500 uppercase font-bold block mb-2">Date d'Arrivée</label>
                    <input type="date" name="date_arrivee" class="input-luxe" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div>
                    <label class="text-[10px] text-gray-500 uppercase font-bold block mb-2">Date de Départ</label>
                    <input type="date" name="date_depart" class="input-luxe" value="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6 border-t border-zinc-800 pt-6">
                <div>
                    <label class="text-[10px] uppercase font-bold text-zinc-500 block mb-2">Document d'identité</label>
                    <select name="type_piece" class="input-luxe">
                        <option value="CNI">Carte Nationale d'Identité</option>
                        <option value="Passeport">Passeport</option>
                        <option value="Permis">Permis de Conduire</option>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] uppercase font-bold text-zinc-500 block mb-2">N° de la pièce</label>
                    <input type="text" name="num_piece" required placeholder="Ex: C01234567" class="input-luxe">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="text-[10px] text-gray-500 uppercase font-bold block mb-2">Mode de Règlement</label>
                    <select name="mode_paiement" class="input-luxe">
                        <option>Carte Bancaire</option>
                        <option>Espèces (Cash)</option>
                        <option>Virement</option>
                        <option>Wave money</option>
                        
                        <option>MTN money </option>
                        <option>Moov money</option>
                        <option>Orange money</option>

                    </select>
                </div>
                <div>
                    <label class="text-[10px] text-gray-500 uppercase font-bold block mb-2">Garantie / Caution (FCFA)</label>
                    <input type="number" name="caution" class="input-luxe" value="50000">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-8 border-t border-zinc-800 pt-6">
                 <div>
                    <label class="text-[10px] text-gray-500 uppercase font-bold block mb-2">Accueil (Service)</label>
                    <select name="boisson" class="input-luxe">
                        <option value="Champagne brut">Champagne brut</option>
                        <option value="Cocktail de bienvenue">Cocktail de bienvenue</option>
                        <option value="Eau minérale">Eau minérale</option>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] text-gray-500 uppercase font-bold block mb-2">Confort Sommeil</label>
                    <select name="oreillers" class="input-luxe">
                        <option value="Plumes d'oie">Plumes d'oie (Souple)</option>
                        <option value="Mémoire de forme">Mémoire de forme</option>
                        <option value="Ferme / Orthopédique">Ferme / Orthopédique</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="w-full bg-[#D4AF37] text-black font-bold py-4 rounded-xl uppercase tracking-widest hover:scale-[1.02] transition shadow-[0_0_20px_rgba(212,175,55,0.2)]">
                Confirmer l'Arrivée (Check-in)
            </button>
        </div>
    </form>




    <!--  le réceptionniste voie le prix changer sur l'écran dès qu'il change la date de départ, -->
    <script>
     const inputArr = document.querySelector('input[name="date_arrivee"]');
     const inputDep = document.querySelector('input[name="date_depart"]');
     const tarifNuit = <?php echo $chambre['tarif_nuit']; ?>;

    function calculerTotal() {
          const d1 = new Date(inputArr.value);
          const d2 = new Date(inputDep.value);
          const diffTime = Math.abs(d2 - d1);
          const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) || 1;
    
           // On met à jour un petit texte dans ton interface (si tu crées un span avec l'id 'total-preview')
          console.log("Total estimé : " + (diffDays * tarifNuit) + " FCFA");
    }

     inputArr.addEventListener('change', calculerTotal);
     inputDep.addEventListener('change', calculerTotal);
</script>


<?php include '../layout/footer.php'; ?>
</body>
</html>