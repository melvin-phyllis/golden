<?php
require_once __DIR__ . '/config/paths.php';
session_start();
session_unset();
session_destroy();
header('Location: ' . rtrim(BASE_URL, '/') . '/login.php');
exit;
