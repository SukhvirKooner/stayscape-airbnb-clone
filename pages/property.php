<?php
require_once '../includes/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    redirect(SITE_URL);
}

$stmt = $conn->prepare("SELECT p.*, u.full_name as host_name, u.bio as host_bio, u.created_at as host_since FROM properties p JOIN users u ON p.host_id = u.id WHERE p.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$property = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$property) {
    $_SESSION['error'] = 'Property not found.';
    redirect(SITE_URL);
}

$reviews = $conn->query("SELECT r.*, u.full_name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.property_id = $id ORDER BY r.created_at DESC LIMIT 6");

$amenities = $property['amenities'] ? explode(',', $property['amenities']) : [];

$amenity_icons = [
    'WiFi' => 'fa-wifi',
    'Pool' => 'fa-person-swimming',
    'Kitchen' => 'fa-kitchen-set',
    'AC' => 'fa-snowflake',
    'Parking' => 'fa-square-parking',
    'Beach Access' => 'fa-umbrella-beach',
    'TV' => 'fa-tv',
    'Washer' => 'fa-jug-detergent',
    'Elevator' => 'fa-elevator',
    'Fan' => 'fa-fan',
    'Fireplace' => 'fa-fire',
    'Mountain View' => 'fa-mountain',
    'Heating' => 'fa-temperature-arrow-up',
    'Garden' => 'fa-seedling',
    'BBQ' => 'fa-fire-burner',
    'Staff' => 'fa-bell-concierge',
    'Courtyard' => 'fa-archway',
    'Rooftop' => 'fa-building',
    'Breakfast' => 'fa-mug-hot',
    'Nature View' => 'fa-tree',
    'Balcony' => 'fa-door-open',
    'Lake View' => 'fa-water',
    'Gym' => 'fa-dumbbell',
    'Workspace' => 'fa-laptop',
    'Chef' => 'fa-utensils',
    'Meals Included' => 'fa-plate-wheat',
    'Deck' => 'fa-ship',
    'Cruise' => 'fa-sailboat',
    'Restaurant Nearby' => 'fa-utensils',
    'Snorkeling' => 'fa-mask-snorkel',
    'Kayak' => 'fa-water',
    'Restaurant' => 'fa-utensils',
];

$pageTitle = $property['title'];
require_once '../includes/header.php';
?>

<div class="property-detail">
    <div class="property-header">
        <h1><?= sanitize($property['title']) ?></h1>
        <div class="property-meta">
            <?php if ($property['rating'] > 0): ?>
                <span class="rating"><i class="fa-solid fa-star"></i> <?= number_format($property['rating'], 1) ?></span>
                <span class="dot">&middot;</span>
                <span class="reviews-count"><?= $property['review_count'] ?> reviews</span>
                <span class="dot">&middot;</span>
            <?php endif; ?>
            <span class="location"><?= sanitize($property['city']) ?>, <?= sanitize($property['country']) ?></span>
        </div>
    </div>

    <div class="property-image-grid">
        <div class="main-image">
            <?php if ($property['image']): ?>
                <img src="<?= SITE_URL ?>/uploads/<?= sanitize($property['image']) ?>" alt="<?= sanitize($property['title']) ?>">
            <?php else: ?>
                <div class="property-placeholder">
                    <i class="fa-solid fa-house"></i>
                </div>
            <?php endif; ?>
        </div>
        <div class="property-placeholder small"><i class="fa-solid fa-image"></i></div>
        <div class="property-placeholder small"><i class="fa-solid fa-image"></i></div>
        <div class="property-placeholder small"><i class="fa-solid fa-image"></i></div>
        <div class="property-placeholder small"><i class="fa-solid fa-image"></i></div>
    </div>

    <div class="property-body">
        <div class="property-info">
            <h2><?= sanitize($property['property_type']) ?> hosted by <?= sanitize($property['host_name']) ?></h2>
            <div class="property-specs">
                <span><?= $property['max_guests'] ?> guests</span> &middot;
                <span><?= $property['bedrooms'] ?> bedroom<?= $property['bedrooms'] > 1 ? 's' : '' ?></span> &middot;
                <span><?= $property['beds'] ?> bed<?= $property['beds'] > 1 ? 's' : '' ?></span> &middot;
                <span><?= $property['bathrooms'] ?> bath<?= $property['bathrooms'] > 1 ? 's' : '' ?></span>
            </div>

            <div class="host-section">
                <div class="host-avatar"><?= strtoupper(substr($property['host_name'], 0, 1)) ?></div>
                <div class="host-info">
                    <h3>Hosted by <?= sanitize($property['host_name']) ?></h3>
                    <p>Host since <?= date('F Y', strtotime($property['host_since'])) ?></p>
                </div>
            </div>

            <div class="property-description">
                <?= nl2br(sanitize($property['description'])) ?>
            </div>

            <?php if (!empty($amenities)): ?>
                <div class="amenities-section">
                    <h3>What this place offers</h3>
                    <div class="amenities-grid">
                        <?php foreach ($amenities as $amenity): ?>
                            <?php $amenity = trim($amenity); ?>
                            <div class="amenity-item">
                                <i class="fa-solid <?= $amenity_icons[$amenity] ?? 'fa-check' ?>"></i>
                                <span><?= sanitize($amenity) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div>
            <div class="booking-card">
                <div class="booking-price-row">
                    <span class="price"><?= formatPrice($property['price_per_night']) ?></span>
                    <span class="per-night">night</span>
                </div>

                <?php if (isLoggedIn()): ?>
                    <form action="book.php" method="POST">
                        <input type="hidden" name="property_id" value="<?= $property['id'] ?>">
                        <input type="hidden" id="price-per-night" value="<?= $property['price_per_night'] ?>">
                        <div class="booking-form-group">
                            <div class="booking-dates">
                                <div class="booking-field">
                                    <label>CHECK-IN</label>
                                    <input type="date" name="check_in" id="booking-checkin" required min="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="booking-field">
                                    <label>CHECKOUT</label>
                                    <input type="date" name="check_out" id="booking-checkout" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                                </div>
                            </div>
                            <div class="booking-field">
                                <label>GUESTS</label>
                                <select name="guests">
                                    <?php for ($i = 1; $i <= $property['max_guests']; $i++): ?>
                                        <option value="<?= $i ?>"><?= $i ?> guest<?= $i > 1 ? 's' : '' ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="booking-submit">Reserve</button>
                        <p class="booking-note">You won't be charged yet</p>
                        <div class="price-breakdown" id="price-breakdown" style="display:none;"></div>
                    </form>
                <?php else: ?>
                    <a href="<?= SITE_URL ?>/pages/login.php" class="booking-submit" style="display:block; text-align:center;">Log in to book</a>
                    <p class="booking-note">Sign in to make a reservation</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($reviews->num_rows > 0): ?>
        <div class="reviews-section">
            <h3>
                <i class="fa-solid fa-star"></i>
                <?= number_format($property['rating'], 1) ?> &middot; <?= $property['review_count'] ?> reviews
            </h3>
            <div class="reviews-grid">
                <?php while ($review = $reviews->fetch_assoc()): ?>
                    <div class="review-card">
                        <div class="review-header">
                            <div class="review-avatar"><?= strtoupper(substr($review['full_name'], 0, 1)) ?></div>
                            <div>
                                <div class="review-author"><?= sanitize($review['full_name']) ?></div>
                                <div class="review-date"><?= date('F Y', strtotime($review['created_at'])) ?></div>
                            </div>
                        </div>
                        <div class="review-stars">
                            <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                                <i class="fa-solid fa-star"></i>
                            <?php endfor; ?>
                        </div>
                        <div class="review-text"><?= sanitize($review['comment']) ?></div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
