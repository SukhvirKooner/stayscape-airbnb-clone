<?php
require_once '../includes/config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['status' => 'login_required']);
    exit;
}

$property_id = (int)($_POST['property_id'] ?? 0);
$user_id = (int)$_SESSION['user_id'];

if ($property_id <= 0) {
    echo json_encode(['status' => 'error']);
    exit;
}

$check = $conn->prepare("SELECT id FROM wishlists WHERE user_id = ? AND property_id = ?");
$check->bind_param("ii", $user_id, $property_id);
$check->execute();
$exists = $check->get_result()->num_rows > 0;
$check->close();

if ($exists) {
    $stmt = $conn->prepare("DELETE FROM wishlists WHERE user_id = ? AND property_id = ?");
    $stmt->bind_param("ii", $user_id, $property_id);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['status' => 'removed']);
} else {
    $stmt = $conn->prepare("INSERT INTO wishlists (user_id, property_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $property_id);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['status' => 'added']);
}
