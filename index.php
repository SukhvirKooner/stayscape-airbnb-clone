<?php
require_once 'includes/config.php';
$pageTitle = 'Vacation Rentals, Homes, Experiences & Places';
require_once 'includes/header.php';

$categories = $conn->query("SELECT * FROM categories ORDER BY id");

$properties = $conn->query("SELECT p.*, u.full_name as host_name FROM properties p JOIN users u ON p.host_id = u.id WHERE p.is_active = 1 ORDER BY p.rating DESC, p.review_count DESC");

$wishlist_ids = [];
if (isLoggedIn()) {
    $uid = (int)$_SESSION['user_id'];
    $wResult = $conn->query("SELECT property_id FROM wishlists WHERE user_id = $uid");
    while ($w = $wResult->fetch_assoc()) {
        $wishlist_ids[] = $w['property_id'];
    }
}
?>

<div class="categories-bar">
    <div class="categories-scroll">
        <?php while ($cat = $categories->fetch_assoc()): ?>
            <div class="category-item" data-category-id="<?= $cat['id'] ?>">
                <i class="fa-solid <?= sanitize($cat['icon']) ?>"></i>
                <span><?= sanitize($cat['name']) ?></span>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<div class="main-content">
    <div class="property-grid">
        <?php while ($prop = $properties->fetch_assoc()): ?>
            <a href="<?= SITE_URL ?>/pages/property.php?id=<?= $prop['id'] ?>" class="property-card">
                <div class="card-image">
                    <img src="<?= getImageUrl($prop['image']) ?>" alt="<?= sanitize($prop['title']) ?>" loading="lazy">
                    <button class="wishlist-btn <?= in_array($prop['id'], $wishlist_ids) ? 'active' : '' ?>" data-property-id="<?= $prop['id'] ?>">
                        <i class="<?= in_array($prop['id'], $wishlist_ids) ? 'fa-solid' : 'fa-regular' ?> fa-heart"></i>
                    </button>
                    <?php if ($prop['rating'] >= 4.8): ?>
                        <span class="card-badge">Guest favourite</span>
                    <?php endif; ?>
                </div>
                <div class="card-info">
                    <div class="card-header">
                        <span class="card-location"><?= sanitize($prop['city']) ?>, <?= sanitize($prop['country']) ?></span>
                        <?php if ($prop['rating'] > 0): ?>
                            <span class="card-rating">
                                <i class="fa-solid fa-star"></i>
                                <?= number_format($prop['rating'], 1) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="card-title"><?= sanitize($prop['title']) ?></div>
                    <div class="card-type"><?= sanitize($prop['property_type']) ?></div>
                    <div class="card-price">
                        <strong><?= formatPrice($prop['price_per_night']) ?></strong> night
                    </div>
                </div>
            </a>
        <?php endwhile; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
