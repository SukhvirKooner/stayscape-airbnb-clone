<?php
require_once '../includes/config.php';

if (!isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(SITE_URL);
}

$booking_id = (int)($_POST['booking_id'] ?? 0);
$user_id = (int)$_SESSION['user_id'];

$stmt = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ? AND guest_id = ?");
$stmt->bind_param("ii", $booking_id, $user_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    $_SESSION['success'] = 'Booking cancelled successfully.';
} else {
    $_SESSION['error'] = 'Could not cancel booking.';
}
$stmt->close();

redirect(SITE_URL . '/pages/bookings.php');
