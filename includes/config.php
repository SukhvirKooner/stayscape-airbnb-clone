<?php
session_start();

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'stayscape');
define('SITE_NAME', 'StayScape');
$protocol = 'http';
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    $protocol = 'https';
} elseif (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') {
    $protocol = 'https';
}
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
define('SITE_URL', $protocol . '://' . $host);

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

function redirect($url) {
    header("Location: $url");
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getCurrentUser($conn) {
    if (!isLoggedIn()) return null;
    $id = (int)$_SESSION['user_id'];
    $result = $conn->query("SELECT * FROM users WHERE id = $id");
    return $result->fetch_assoc();
}

function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function formatPrice($price) {
    return '₹' . number_format($price, 0);
}

function getImageUrl($image) {
    if (str_starts_with($image, 'http')) {
        return $image;
    }
    return SITE_URL . '/uploads/' . $image;
}
