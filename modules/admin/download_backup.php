<?php
require_once __DIR__ . '/../../config/db.php';

$filename = isset($_GET['file']) ? basename($_GET['file']) : '';
if ($filename === '' || strpos($filename, '.sql') === false) {
    header('HTTP/1.1 400 Bad Request');
    exit;
}
$file_path = ROOT_PATH . '/backups/' . $filename;
if (!file_exists($file_path) || !is_readable($file_path)) {
    header('HTTP/1.1 404 Not Found');
    exit;
}
header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($file_path));
readfile($file_path);
exit;
