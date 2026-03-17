<?php
require_once __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['photo_profile'])) {
    $user_id = $_SESSION['user_id'];
    $folder = ROOT_PATH . '/uploads/profiles/';
    if (!is_dir($folder)) mkdir($folder, 0755, true);

    $file_ext = pathinfo($_FILES['photo_profile']['name'], PATHINFO_EXTENSION);
    $filename = "user_" . $user_id . "_" . time() . "." . $file_ext;
    $targetPath = $folder . $filename;
    $photoUrl = 'uploads/profiles/' . $filename;

    if (move_uploaded_file($_FILES['photo_profile']['tmp_name'], $targetPath)) {
        $stmt = $pdo->prepare("UPDATE utilisateurs SET photo = ? WHERE id = ?");
        $stmt->execute([$photoUrl, $user_id]);
        header("Location: mon_profil.php?upload=success");
    }
}