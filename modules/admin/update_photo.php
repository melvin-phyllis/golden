<?php
require_once '../../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['photo_profile'])) {
    $user_id = $_SESSION['user_id'];
    $folder = "../../uploads/profiles/";
    
    if (!is_dir($folder)) mkdir($folder, 0777, true);

    $file_ext = pathinfo($_FILES['photo_profile']['name'], PATHINFO_EXTENSION);
    $filename = "user_" . $user_id . "_" . time() . "." . $file_ext;
    $target = $folder . $filename;

    if (move_uploaded_file($_FILES['photo_profile']['tmp_name'], $target)) {
        $stmt = $pdo->prepare("UPDATE utilisateurs SET photo = ? WHERE id = ?");
        $stmt->execute([$target, $user_id]);
        header("Location: mon_profil.php?upload=success");
    }
}