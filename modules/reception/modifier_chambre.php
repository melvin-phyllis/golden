<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../../login.php');
    exit("Accès refusé");
}


// Récupération des données actuelles
$stmt = $pdo->prepare("SELECT c.*, t.tarif_nuit, t.id as type_id FROM chambres c JOIN types_chambre t ON c.type_id = t.id WHERE c.id = ?");
$stmt->execute([$id]);
$room = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nouveau_num = $_POST['numero_chambre'];
    $nouveau_prix = $_POST['tarif_nuit'];
    $type_id = $room['type_id'];

    // 1. Mise à jour de la chambre (numéro)
    $updateRoom = $pdo->prepare("UPDATE chambres SET numero_chambre = ? WHERE id = ?");
    $updateRoom->execute([$nouveau_num, $id]);

    // 2. Mise à jour du prix dans types_chambre (car tarif_nuit appartient à cette table)
    $updatePrice = $pdo->prepare("UPDATE types_chambre SET tarif_nuit = ? WHERE id = ?");
    $updatePrice->execute([$nouveau_prix, $type_id]);

    // 3. Gestion de la nouvelle image
    if (isset($_FILES['nouvelle_image']) && $_FILES['nouvelle_image']['error'] === 0) {
        $folder = ($type_id == 1) ? 'suite' : 'standard';
        $prefixe = ($type_id == 1) ? 'suite_' : 'standard_';
        
        // On supprime l'ancienne image si le numéro a changé
        $oldPath = "../../assets/img/$folder/{$prefixe}{$room['numero_chambre']}.jpg";
        if(file_exists($oldPath)) unlink($oldPath);

        // On enregistre la nouvelle
        move_uploaded_file($_FILES['nouvelle_image']['tmp_name'], "../../assets/img/$folder/{$prefixe}{$nouveau_num}.jpg");
    }

    header('Location: chambres.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier l'Unité | Prestige</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #050505; color: #fff; font-family: 'Inter', sans-serif; }
        .gold-border { border: 1px solid rgba(212, 175, 55, 0.3); }
        .gold-text { color: #D4AF37; }
        input { background: #1a1a1a !important; border: 1px solid #333 !important; color: white !important; padding: 12px; border-radius: 8px; width: 100%; }
        input:focus { border-color: #D4AF37 !important; outline: none; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md bg-zinc-900 p-10 rounded-3xl gold-border shadow-2xl">
        <h2 class="text-2xl font-serif gold-text uppercase text-center mb-8 tracking-widest">Édition Prestige Bémar hôtel </h2>
        
        <form method="POST" enctype="multipart/form-data" class="space-y-6">
            <div>
                <label class="block text-xs text-gray-500 uppercase mb-2">Numéro de Chambre</label>
                <input type="text" name="numero_chambre" value="<?php echo $room['numero_chambre']; ?>" required>
            </div>

            <div>
                <label class="block text-xs text-gray-500 uppercase mb-2">Prix par Nuit (FCFA)</label>
                <input type="number" name="tarif_nuit" value="<?php echo $room['tarif_nuit']; ?>" required>
            </div>

            <div>
                <label class="block text-xs text-gray-500 uppercase mb-2">Changer l'image (JPG)</label>
                <input type="file" name="nouvelle_image" accept="image/jpeg" class="text-xs text-gray-400">
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-[#D4AF37] text-black font-bold py-4 rounded-xl uppercase tracking-widest hover:bg-[#b8962d] transition">
                    Enregistrer les modifications
                </button>
                <a href="chambres.php" class="block text-center mt-4 text-gray-500 text-xs uppercase tracking-widest">Annuler</a>
            </div>
        </form>
    </div>


    <?php include '../layout/footer.php'; ?>
</body>
</html>