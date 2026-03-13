<?php
session_start();
require_once '../../config/db.php';

// if ($_SESSION['user_role'] !== 'Réceptionniste') {
//     header('Location: ../../login.php');
//     exit();
// }



// Traitement de la suppression (si demandée)
if (isset($_GET['delete_id'])) {
    // On revérifie si c'est bien l'admin
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin') {
        $id = $_GET['delete_id'];
        $stmt = $pdo->prepare("DELETE FROM chambres WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: chambres.php?msg=supprime");
        exit();
    } else {
        // Si quelqu'un essaie de supprimer sans être admin
        die("Erreur : Vous n'avez pas les droits nécessaires pour supprimer.");
    }
}



$query = "SELECT c.*, t.libelle, t.tarif_nuit FROM chambres c 
          JOIN types_chambre t ON c.type_id = t.id 
          ORDER BY c.numero_chambre ASC";
$chambres = $pdo->query($query)->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Chambres | Prestige</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #050505; color: #fff; font-family: 'Inter', sans-serif; }
        .gold-border { border: 1px solid rgba(212, 175, 55, 0.3); }
        .gold-text { color: #D4AF37; }
        .room-card { background: #111; transition: 0.3s; }
        .room-card:hover { border-color: #D4AF37; }
    </style>
</head>
<body class="p-8">

  
   


    <div class="flex justify-between items-center mb-10 border-b border-zinc-800 pb-5">
        <div>

        <h1 class="text-3xl font-serif gold-text uppercase tracking-widest">Patrimoine Hôtelier</h1>
        <p class="text-gray-500 italic">20 écrins de luxe à votre disposition au Bémar    hôtel </p>
        </div>

        <a href="ajouter_chambre.php" class="bg-[#D4AF37] text-black px-6 py-2 rounded-full font-bold">+ AJOUTER UNE UNITÉ</a>
        <a href="ajouter_categorie.php" class="bg-[#D4AF37] text-black px-6 py-2 rounded-full font-bold">+ AJOUTER UNE NOUVELLE CATEGORIE</a>
    </div>




     <?php include 'StatusBar.php'; ?>



    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">


        <?php foreach($chambres as $ch): 

            if($ch['type_id'] == 1){
              $folder = "suites";
            }else{
               $folder = "standard";
            }

           // image basée sur le numéro de chambre
           $imgURL = "../../assets/img/".$folder."/".$ch['numero_chambre'].".jpg";

           $statusColor = ($ch['statut'] == 'Libre') ? 'text-green-400' : 'text-red-500';
        ?>


            <div class="room-card rounded-2xl overflow-hidden gold-border p-4">
                <div class="h-40 rounded-xl overflow-hidden mb-4 relative">
                    <img src="<?php echo $imgURL; ?>" 
                         onerror="this.src='../../assets/img/default.jpg';" 
                         class="w-full h-full object-cover">
                    <div class="absolute top-2 right-2 bg-black/80 px-2 py-1 rounded text-[10px] font-bold <?php echo $statusColor; ?>">
                        ● <?php echo strtoupper($ch['statut']); ?>
                    </div>
                </div>
                    

                    <div class="space-y-3">

                    <div class="flex justify-between items-center">


                       <h3 class="text-xl font-bold">N° <?php echo $ch['numero_chambre']; ?></h3>
                        <span class="gold-text text-[10px] font-serif uppercase tracking-widest"><?php echo $ch['libelle']; ?></span>
                    </div>
                    

                     <p class="text-2xl font-light">
                        <?php echo number_format($ch['tarif_nuit'], 0, ',', ' '); ?> <span class="text-xs text-gray-500">FCFA</span>
                    </p>


                    <div class="grid grid-cols-2 gap-2 pt-2">
                          
                        <a href="modifier_chambre.php?id=<?php echo $ch['id']; ?>" 
                           class="bg-zinc-800 text-center py-2 rounded-lg text-[10px] uppercase font-bold hover:bg-zinc-700 transition">
                            Modifier
                        </a>


                           <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin'): ?>

                           <a href="?delete_id=<?php echo $ch['id']; ?>" 
                               onclick="return confirm('Voulez-vous vraiment supprimer définitivement cette chambre ?')"
                               class="text-[10px] bg-zinc-800 hover:bg-red-900 py-2 rounded uppercase font-bold text-center transition">
                                 Supprimer
                            </a>
                            <?php endif; ?>
                    </div>



                   
                  <a href="reservation_form.php?chambre_id=<?php echo $ch['id']; ?>" 
                   class="btn-reserve ...  block w-full border border-[#D4AF37] text-[#D4AF37] hover:bg-[#D4AF37] hover:text-black text-center py-2 rounded-lg text-xs font-bold transition">
                  RÉSERVER MAINTENANT
                 </a>

                  
                
                </div>

            </div>
        <?php endforeach; ?>
    </div>

    <?php include '../layout/footer3.php'; ?>
</body>
</html>