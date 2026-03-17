<?php
session_start();
require_once '../../config/db.php';

// if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Réceptionniste') {
//     header('Location: ../../login.php');
//     exit();
// }




if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../../login.php');
    exit("Accès refusé");
}

$types = $pdo->query("SELECT * FROM types_chambre")->fetchAll();
$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero = $_POST['numero_chambre'];
    $type_id = $_POST['type_id'];
    $statut = "Libre";

    try {
        // 1. Insertion en base de données
        $stmt = $pdo->prepare("INSERT INTO chambres (numero_chambre, type_id, statut) VALUES (?, ?, ?)");
        $stmt->execute([$numero, $type_id, $statut]);

        // 2. Gestion de l'image (Changement physique sur le disque)
        if (isset($_FILES['image_chambre']) && $_FILES['image_chambre']['error'] === 0) {
            
            // Déterminer le dossier de destination
            $folder = ($type_id == 1) ? 'suite' : 'standard';
            $prefixe = ($type_id == 1) ? 'suite_' : 'standard_';
            
            $uploadDir = ROOT_PATH . '/assets/img/' . $folder . '/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $fileName = $prefixe . $numero . ".jpg";
            $targetPath = $uploadDir . $fileName;

            // Déplacer le fichier téléchargé
            if (move_uploaded_file($_FILES['image_chambre']['tmp_name'], $targetPath)) {
                $message = "Chambre $numero ajoutée et image configurée avec succès.";
            } else {
                $message = "Chambre ajoutée, mais erreur lors du transfert de l'image.";
            }
        } else {
            $message = "Chambre $numero ajoutée (sans image personnalisée).";
        }
    } catch (PDOException $e) {
        $error = "Erreur : Ce numéro de chambre existe déjà.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Prestige | Nouvelle Unité</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #050505; color: #fff; font-family: 'Inter', sans-serif; }
        .gold-border { border: 1px solid rgba(212, 175, 55, 0.3); }
        .gold-text { color: #D4AF37; }
        .form-card { background: #111; border-radius: 2rem; }
        input, select { 
            background: #1a1a1a !important; 
            border: 1px solid #333 !important; 
            color: white !important;
        }
        input:focus { border-color: #D4AF37 !important; outline: none; }
        .btn-gold { background: #D4AF37; color: #000; font-weight: bold; transition: 0.3s; }
        .btn-gold:hover { background: #b8962d; transform: scale(1.02); }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-6">

    <div class="w-full max-w-xl">
        <div class="text-center mb-10">
            <h1 class="text-3xl font-serif gold-text uppercase tracking-widest">Acquisition Prestige Bémar hôtel </h1>
            <p class="text-gray-500 italic mt-2">Définissez une nouvelle unité sans toucher à la base de données</p>
        </div>

        <?php if($message): ?>
            <div class="mb-6 p-4 rounded-xl bg-green-900/20 border border-green-500 text-green-400 text-center text-sm font-bold">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if($error): ?>
            <div class="mb-6 p-4 rounded-xl bg-red-900/20 border border-red-500 text-red-400 text-center text-sm font-bold">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="form-card gold-border p-8 md:p-12">
            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] uppercase tracking-widest text-gray-500 mb-2 font-bold">Numéro d'unité</label>
                        <input type="text" name="numero_chambre" placeholder="Ex: 302" required
                               class="w-full p-4 rounded-xl text-lg">
                    </div>

                    <div>
                        <label class="block text-[10px] uppercase tracking-widest text-gray-500 mb-2 font-bold">Catégorie</label>
                        <select name="type_id" required class="w-full p-4 rounded-xl text-lg appearance-none">
                            <?php foreach($types as $t): ?>
                                <option value="<?php echo $t['id']; ?>"><?php echo $t['libelle']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] uppercase tracking-widest text-gray-500 mb-2 font-bold">Photographie de l'unité (JPG)</label>
                    <input type="file" name="image_chambre" accept="image/jpeg"
                           class="w-full p-3 rounded-xl border-dashed border-2 border-zinc-800 text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-zinc-800 file:text-white hover:file:bg-zinc-700">
                </div>

                <div class="pt-6">
                    <button type="submit" class="btn-gold w-full py-4 rounded-xl uppercase tracking-widest text-sm shadow-lg shadow-yellow-900/20">
                        Inscrire au Patrimoine Bémar hôtel 
                    </button>
                    <a href="chambres.php" class="block w-full text-center mt-6 text-gray-500 hover:text-white text-xs transition uppercase tracking-widest">
                        Annuler et retourner
                    </a>
                </div>
            </form>
        </div>
    </div>


    <?php include '../layout/footer.php'; ?>
</body>
</html>