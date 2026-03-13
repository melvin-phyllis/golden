<?php
session_start();
require_once '../../config/db.php';

// Sécurité
// if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Réceptionniste') {
//     header('Location: ../../login.php');
//     exit();
// }



if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../../login.php');
    exit("Accès refusé");
}



$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $libelle = $_POST['libelle'];
    $tarif = $_POST['tarif_nuit'];

    try {
        $stmt = $pdo->prepare("INSERT INTO types_chambre (libelle, tarif_nuit) VALUES (?, ?)");
        $stmt->execute([$libelle, $tarif]);
        $message = "La catégorie '$libelle' a été ajoutée avec succès.";
    } catch (PDOException $e) {
        $error = "Erreur : Cette catégorie existe déjà ou les données sont invalides.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouvelle Catégorie | Prestige</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #050505; color: #fff; font-family: 'Inter', sans-serif; }
        .gold-border { border: 1px solid rgba(212, 175, 55, 0.3); }
        .gold-text { color: #D4AF37; }
        .form-card { background: #111; border-radius: 2rem; }
        input { background: #1a1a1a !important; border: 1px solid #333 !important; color: white !important; }
        input:focus { border-color: #D4AF37 !important; outline: none; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-6">

    <div class="w-full max-w-md">
        <div class="text-center mb-10">
            <h1 class="text-3xl font-serif gold-text uppercase tracking-widest">Configuration</h1>
            <p class="text-gray-500 italic mt-2">Ajouter une nouvelle catégorie de prestige</p>
        </div>

        <?php if($message): ?>
            <div class="mb-6 p-4 rounded-xl bg-green-900/20 border border-green-500 text-green-400 text-center text-sm font-bold">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="form-card gold-border p-8">
            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-[10px] uppercase tracking-widest text-gray-500 mb-2 font-bold">Nom de la catégorie</label>
                    <input type="text" name="libelle" placeholder="Ex: Suite Présidentielle" required
                           class="w-full p-4 rounded-xl text-lg">
                </div>

                <div>
                    <label class="block text-[10px] uppercase tracking-widest text-gray-500 mb-2 font-bold">Tarif de base (FCFA)</label>
                    <input type="number" name="tarif_nuit" placeholder="Ex: 500000" required
                           class="w-full p-4 rounded-xl text-lg">
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-[#D4AF37] text-black font-bold py-4 rounded-xl uppercase tracking-widest hover:scale-105 transition">
                        Créer la catégorie
                    </button>
                    <a href="chambres.php" class="block text-center mt-6 text-gray-500 hover:text-white text-xs transition uppercase tracking-widest">
                        Retour au patrimoine
                    </a>
                </div>
            </form>
        </div>
    </div>


    <?php include '../layout/footer.php'; ?>
</body>
</html>