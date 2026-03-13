<?php
require_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $type = $_POST['type'];
    $capa = $_POST['capacite'];
    $h = $_POST['heure'];
    $j = $_POST['jour'];
    
    // Gestion de l'image
    $img = "default_salle.jpg";
    if (!empty($_FILES['image']['name'])) {
        $img = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "../../assets/img/salles/" . $img);
    }

    $stmt = $pdo->prepare("INSERT INTO salles (nom_salle, type_salle, capacite, tarif_heure, tarif_jour, image_salle) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nom, $type, $capa, $h, $j, $img]);
    
    header("Location: gestion_salles.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une Salle | Prestige</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white p-10">
    <div class="max-w-lg mx-auto bg-zinc-900 p-8 rounded-3xl border border-zinc-800">
        <h2 class="text-[#D4AF37] text-2xl font-serif mb-6 uppercase">Nouvelle Salle</h2>
        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="text" name="nom" placeholder="Nom de la salle" class="w-full bg-black p-4 rounded-xl border border-zinc-800 outline-none" required>
            <select name="type" class="w-full bg-black p-4 rounded-xl border border-zinc-800 outline-none">
                <option value="Conférence">Conférence</option>
                <option value="Séminaire">Séminaire</option>
                <option value="Évènementiel">Évènementiel</option>
            </select>
            <input type="number" name="capacite" placeholder="Capacité (pers.)" class="w-full bg-black p-4 rounded-xl border border-zinc-800 outline-none" required>
            <input type="number" name="heure" placeholder="Prix par Heure (CFA)" class="w-full bg-black p-4 rounded-xl border border-zinc-800 outline-none" required>
            <input type="number" name="jour" placeholder="Prix par Jour (CFA)" class="w-full bg-black p-4 rounded-xl border border-zinc-800 outline-none" required>
            <input type="file" name="image" class="text-xs text-zinc-500">
            <button type="submit" class="w-full bg-[#D4AF37] text-black font-bold py-4 rounded-xl uppercase">Enregistrer la salle</button>
        </form>
    </div>
</body>
</html>