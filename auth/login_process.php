<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Recherche de l'utilisateur avec son rôle
    $stmt = $pdo->prepare("SELECT u.*, r.nom_role FROM utilisateurs u 
                           JOIN roles r ON u.role_id = r.id 
                           WHERE u.email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['mot_de_passe'])) {
        // Enregistrement des informations en session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nom'] = $user['nom'];
        $_SESSION['user_role'] = $user['nom_role'];

        // Redirection élégante selon le rôle 
        switch ($user['nom_role']) {
            case 'Admin': header('Location: ../modules/admin/accueil.php'); break;

            case 'Réceptionniste': header('Location: ../modules/admin/accueil.php'); break;

            case 'Manager': header('Location: ../modules/admin/accueil.php'); break;

            case 'Gérant de stock': header('Location: ../modules/stock/stock_dashboard.php'); break;

            case 'Comptable': header('Location: ../modules/finances/comptable.php'); break;

            case 'Technicien': header('Location: ../modules/maintenance/gestion_personnel.php'); break;

            case 'Restauration & Bar': header('Location: ../modules/conciergerie/conciergerie_dash.php'); break;

            case 'Service évènement ': header('Location: ../modules/maintenance/gestion_salles.php'); break;

            case 'Gouvernance ': header('Location: ../modules/admin/accueil.php'); break;
            
            default: header('Location: ../login.php?error=role'); break;
        }
    } else {
        header('Location: ../login.php?error=1');
    }
}



// Exemple dans ton script de traitement du login :
if ($user && password_verify($password, $user['mot_de_passe'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nom'] = $user['nom']; // C'est cette ligne qui manque peut-être !
    $_SESSION['user_role'] = $user['role'];
    header('Location: modules/admin/accueil.php');
}
?>