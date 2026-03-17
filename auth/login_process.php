<?php
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT u.*, r.nom_role FROM utilisateurs u 
                           JOIN roles r ON u.role_id = r.id 
                           WHERE u.email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['mot_de_passe'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nom'] = $user['nom'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['user_role'] = $user['nom_role'];

        $base = rtrim(BASE_URL, '/');
        switch ($user['nom_role']) {
            case 'Admin':
            case 'Réceptionniste':
            case 'Manager':
            case 'Gouvernance ':
                header('Location: ' . $base . '/modules/admin/accueil.php');
                break;
            case 'Gérant de stock':
                header('Location: ' . $base . '/modules/stock/stock_dashboard.php');
                break;
            case 'Comptable':
                header('Location: ' . $base . '/modules/finances/comptable.php');
                break;
            case 'Technicien':
                header('Location: ' . $base . '/modules/maintenance/gestion_personnel.php');
                break;
            case 'Restauration & Bar':
                header('Location: ' . $base . '/modules/conciergerie/conciergerie_dash.php');
                break;
            case 'Service évènement ':
                header('Location: ' . $base . '/modules/maintenance/gestion_salles.php');
                break;
            default:
                header('Location: ' . $base . '/login.php?error=role');
                break;
        }
    } else {
        header('Location: ' . rtrim(BASE_URL, '/') . '/login.php?error=1');
    }
    exit;
}
