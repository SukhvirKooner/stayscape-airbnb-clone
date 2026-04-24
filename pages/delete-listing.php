<?php
require_once '../includes/config.php';

if (!isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(SITE_URL);
}

$property_id = (int)($_POST['property_id'] ?? 0);
$user_id = (int)$_SESSION['user_id'];

$stmt = $conn->prepare("DELETE FROM properties WHERE id = ? AND host_id = ?");
$stmt->bind_param("ii", $property_id, $user_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    $_SESSION['success'] = 'Listing deleted successfully.';
} else {
    $_SESSION['error'] = 'Could not delete listing.';
}
$stmt->close();

redirect(SITE_URL . '/pages/my-listings.php');
