<?php
require_once '../includes/config.php';

if (!isLoggedIn()) {
    redirect(SITE_URL . '/pages/login.php');
}

$user_id = (int)$_SESSION['user_id'];
$wishlists = $conn->query("SELECT p.* FROM wishlists w JOIN properties p ON w.property_id = p.id WHERE w.user_id = $user_id ORDER BY w.created_at DESC");

$pageTitle = 'Wishlists';
require_once '../includes/header.php';
?>

<div class="wishlist-container">
    <h1>Wishlists</h1>

    <?php if ($wishlists->num_rows > 0): ?>
        <div class="property-grid">
            <?php while ($prop = $wishlists->fetch_assoc()): ?>
                <a href="<?= SITE_URL ?>/pages/property.php?id=<?= $prop['id'] ?>" class="property-card">
                    <div class="card-image">
                        <?php if ($prop['image']): ?>
                            <img src="<?= SITE_URL ?>/uploads/<?= sanitize($prop['image']) ?>" alt="<?= sanitize($prop['title']) ?>">
                        <?php else: ?>
                            <div class="property-placeholder"><i class="fa-solid fa-house"></i></div>
                        <?php endif; ?>
                        <button class="wishlist-btn active" data-property-id="<?= $prop['id'] ?>">
                            <i class="fa-solid fa-heart"></i>
                        </button>
                    </div>
                    <div class="card-info">
                        <div class="card-header">
                            <span class="card-location"><?= sanitize($prop['city']) ?>, <?= sanitize($prop['country']) ?></span>
                            <?php if ($prop['rating'] > 0): ?>
                                <span class="card-rating"><i class="fa-solid fa-star"></i> <?= number_format($prop['rating'], 1) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="card-title"><?= sanitize($prop['title']) ?></div>
                        <div class="card-price"><strong><?= formatPrice($prop['price_per_night']) ?></strong> night</div>
                    </div>
                </a>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fa-regular fa-heart"></i>
            <h3>Create your first wishlist</h3>
            <p>As you search, tap the heart icon to save your favourite places to stay.</p>
            <a href="<?= SITE_URL ?>">Start exploring</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
