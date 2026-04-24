<?php
require_once '../includes/config.php';

if (!isLoggedIn()) {
    redirect(SITE_URL . '/pages/login.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(SITE_URL);
}

$property_id = (int)($_POST['property_id'] ?? 0);
$check_in = $_POST['check_in'] ?? '';
$check_out = $_POST['check_out'] ?? '';
$guests = (int)($_POST['guests'] ?? 1);
$user_id = (int)$_SESSION['user_id'];

if ($property_id <= 0 || empty($check_in) || empty($check_out)) {
    $_SESSION['error'] = 'Please fill in all booking details.';
    redirect(SITE_URL . "/pages/property.php?id=$property_id");
}

$checkInDate = new DateTime($check_in);
$checkOutDate = new DateTime($check_out);

if ($checkOutDate <= $checkInDate) {
    $_SESSION['error'] = 'Check-out must be after check-in.';
    redirect(SITE_URL . "/pages/property.php?id=$property_id");
}

$stmt = $conn->prepare("SELECT * FROM properties WHERE id = ?");
$stmt->bind_param("i", $property_id);
$stmt->execute();
$property = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$property) {
    $_SESSION['error'] = 'Property not found.';
    redirect(SITE_URL);
}

if ($property['host_id'] === $user_id) {
    $_SESSION['error'] = 'You cannot book your own property.';
    redirect(SITE_URL . "/pages/property.php?id=$property_id");
}

$nights = $checkInDate->diff($checkOutDate)->days;
$subtotal = $nights * $property['price_per_night'];
$service_fee = round($subtotal * 0.12, 2);
$total = $subtotal + $service_fee;

$stmt = $conn->prepare("INSERT INTO bookings (property_id, guest_id, check_in, check_out, guests, total_price, service_fee, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'confirmed')");
$stmt->bind_param("iissids", $property_id, $user_id, $check_in, $check_out, $guests, $total, $service_fee);

if ($stmt->execute()) {
    $_SESSION['success'] = 'Booking confirmed! You\'re going to ' . $property['city'] . '!';
    redirect(SITE_URL . '/pages/bookings.php');
} else {
    $_SESSION['error'] = 'Booking failed. Please try again.';
    redirect(SITE_URL . "/pages/property.php?id=$property_id");
}
$stmt->close();
