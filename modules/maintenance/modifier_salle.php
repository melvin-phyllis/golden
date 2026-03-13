<?php
session_start();
require_once '../../config/db.php';

// 1. Récupération de l'ID de la salle à modifier
if (!isset($_GET['id'])) { header("Location: gestion_salles.php"); exit(); }
$id = $_GET['id'];

// 2. Traitement de la mise à jour
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom_salle'];
    $type = $_POST['type_salle'];
    $capa = $_POST['capacite'];
    $prix_h = $_POST['tarif_heure'];
    $prix_j = $_POST['tarif_jour'];
    $statut = $_POST['statut'];

    $sql = "UPDATE salles SET nom_salle=?, type_salle=?, capacite=?, tarif_heure=?, tarif_jour=?, statut=? WHERE id=?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$nom, $type, $capa, $prix_h, $prix_j, $statut, $id])) {
        echo "<script>alert('Salle mise à jour avec succès !'); window.location.href='gestion_salles.php';</script>";
    }
}

// 3. Lecture des données actuelles de la salle
$salle = $pdo->query("SELECT * FROM salles WHERE id = $id")->fetch();

include '../layout/header.php';
?>

<div class="max-w-2xl mx-auto space-y-8">
    <div class="flex items-center gap-4">
        <a href="gestion_salles.php" class="text-zinc-500 hover:text-white"><i class="fa-solid fa-arrow-left"></i></a>
        <h2 class="text-white text-2xl font-serif">Modifier : <span class="text-[#D4AF37]"><?= $salle['nom_salle'] ?></span></h2>
    </div>

    <form method="POST" class="bg-zinc-900/40 p-10 rounded-[2.5rem] border border-zinc-800 space-y-6">
        <div class="grid grid-cols-2 gap-6">
            <div class="col-span-2 md:col-span-1">
                <label class="block text-[10px] uppercase font-bold text-zinc-500 mb-2">Nom de l'espace</label>
                <input type="text" name="nom_salle" value="<?= $salle['nom_salle'] ?>" class="w-full bg-black border border-zinc-800 rounded-xl p-4 text-white text-sm focus:border-[#D4AF37] outline-none">
            </div>
            <div class="col-span-2 md:col-span-1">
                <label class="block text-[10px] uppercase font-bold text-zinc-500 mb-2">Catégorie (Ex: Événementiel)</label>
                <input type="text" name="type_salle" value="<?= $salle['type_salle'] ?>" class="w-full bg-black border border-zinc-800 rounded-xl p-4 text-white text-sm focus:border-[#D4AF37] outline-none">
            </div>

            <div class="col-span-2">
                <label class="block text-[10px] uppercase font-bold text-zinc-500 mb-2">Capacité d'accueil (Pers.)</label>
                <input type="number" name="capacite" value="<?= $salle['capacite'] ?>" class="w-full bg-black border border-zinc-800 rounded-xl p-4 text-white text-sm focus:border-[#D4AF37] outline-none">
            </div>

            <div>
                <label class="block text-[10px] uppercase font-bold text-zinc-500 mb-2">Tarif par Heure (CFA)</label>
                <input type="number" name="tarif_heure" value="<?= $salle['tarif_heure'] ?>" class="w-full bg-black border border-zinc-800 rounded-xl p-4 text-[#D4AF37] text-sm focus:border-[#D4AF37] outline-none font-bold">
            </div>
            <div>
                <label class="block text-[10px] uppercase font-bold text-zinc-500 mb-2">Tarif par Journée (CFA)</label>
                <input type="number" name="tarif_jour" value="<?= $salle['tarif_jour'] ?>" class="w-full bg-black border border-zinc-800 rounded-xl p-4 text-[#D4AF37] text-sm focus:border-[#D4AF37] outline-none font-bold">
            </div>

            <div class="col-span-2">
                <label class="block text-[10px] uppercase font-bold text-zinc-500 mb-2">État actuel</label>
                <select name="statut" class="w-full bg-black border border-zinc-800 rounded-xl p-4 text-white text-sm focus:border-[#D4AF37] outline-none">
                    <option value="Disponible" <?= $salle['statut'] == 'Disponible' ? 'selected' : '' ?>>Disponible</option>
                    <option value="Maintenance" <?= $salle['statut'] == 'Maintenance' ? 'selected' : '' ?>>En Maintenance</option>
                    <option value="Réservé" <?= $salle['statut'] == 'Réservé' ? 'selected' : '' ?>>Réservé</option>
                </select>
            </div>
        </div>

        <button type="submit" class="w-full bg-[#D4AF37] text-black font-black uppercase text-[10px] tracking-widest py-5 rounded-2xl hover:bg-white transition duration-500">
            Enregistrer les modifications
        </button>
    </form>
</div>

<?php include '../layout/footer3.php'; ?>