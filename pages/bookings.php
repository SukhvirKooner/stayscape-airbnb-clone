<?php
require_once '../includes/config.php';

if (!isLoggedIn()) {
    redirect(SITE_URL . '/pages/login.php');
}

$user_id = (int)$_SESSION['user_id'];
$bookings = $conn->query("SELECT b.*, p.title, p.city, p.country, p.image, p.price_per_night FROM bookings b JOIN properties p ON b.property_id = p.id WHERE b.guest_id = $user_id ORDER BY b.created_at DESC");

$pageTitle = 'My Trips';
require_once '../includes/header.php';
?>

<div class="trips-container">
    <h1>Your trips</h1>

    <?php if ($bookings->num_rows > 0): ?>
        <div class="booking-list">
            <?php while ($booking = $bookings->fetch_assoc()): ?>
                <div class="booking-item">
                    <div class="booking-image">
                        <img src="<?= getImageUrl($booking['image']) ?>" alt="<?= sanitize($booking['title']) ?>" loading="lazy">
                    </div>
                    <div class="booking-details">
                        <h3><a href="<?= SITE_URL ?>/pages/property.php?id=<?= $booking['property_id'] ?>"><?= sanitize($booking['title']) ?></a></h3>
                        <p class="dates">
                            <i class="fa-regular fa-calendar"></i>
                            <?= date('M d', strtotime($booking['check_in'])) ?> - <?= date('M d, Y', strtotime($booking['check_out'])) ?>
                        </p>
                        <p class="location-text">
                            <i class="fa-solid fa-location-dot"></i>
                            <?= sanitize($booking['city']) ?>, <?= sanitize($booking['country']) ?>
                        </p>
                    </div>
                    <div class="booking-status">
                        <span class="status-badge <?= $booking['status'] ?>"><?= $booking['status'] ?></span>
                        <div class="booking-price-display"><?= formatPrice($booking['total_price']) ?></div>
                        <?php if ($booking['status'] === 'confirmed' || $booking['status'] === 'pending'): ?>
                            <form action="cancel-booking.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                                <button type="submit" class="cancel-btn">Cancel</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fa-solid fa-suitcase-rolling"></i>
            <h3>No trips yet</h3>
            <p>Time to dust off your bags and start planning your next adventure</p>
            <a href="<?= SITE_URL ?>">Start exploring</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
