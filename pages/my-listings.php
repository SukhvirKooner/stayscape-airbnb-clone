<?php
require_once '../includes/config.php';

if (!isLoggedIn()) {
    redirect(SITE_URL . '/pages/login.php');
}

$user_id = (int)$_SESSION['user_id'];
$listings = $conn->query("SELECT * FROM properties WHERE host_id = $user_id ORDER BY created_at DESC");

$pageTitle = 'My Listings';
require_once '../includes/header.php';
?>

<div class="listings-container">
    <div class="listings-header">
        <h1>My Listings</h1>
        <a href="<?= SITE_URL ?>/pages/host.php" class="btn-primary" style="width:auto; padding:12px 24px;">+ New Listing</a>
    </div>

    <?php if ($listings->num_rows > 0): ?>
        <?php while ($listing = $listings->fetch_assoc()): ?>
            <div class="listing-card">
                <div class="listing-card-image">
                    <img src="<?= getImageUrl($listing['image']) ?>" alt="<?= sanitize($listing['title']) ?>" loading="lazy">
                </div>
                <div class="listing-info">
                    <h3><a href="<?= SITE_URL ?>/pages/property.php?id=<?= $listing['id'] ?>"><?= sanitize($listing['title']) ?></a></h3>
                    <p><?= sanitize($listing['city']) ?>, <?= sanitize($listing['country']) ?></p>
                    <p><?= formatPrice($listing['price_per_night']) ?> / night</p>
                    <p><i class="fa-solid fa-star" style="color:var(--primary);font-size:0.8rem;"></i> <?= number_format($listing['rating'], 1) ?> (<?= $listing['review_count'] ?> reviews)</p>
                </div>
                <div class="listing-actions">
                    <form action="delete-listing.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this listing?');">
                        <input type="hidden" name="property_id" value="<?= $listing['id'] ?>">
                        <button type="submit" class="btn-sm btn-delete">Delete</button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty-state">
            <i class="fa-solid fa-house-circle-plus"></i>
            <h3>No listings yet</h3>
            <p>Ready to start hosting? List your space and start earning.</p>
            <a href="<?= SITE_URL ?>/pages/host.php">Create your first listing</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
