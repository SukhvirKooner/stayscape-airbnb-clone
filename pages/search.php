<?php
require_once '../includes/config.php';

$location = trim($_GET['location'] ?? '');
$check_in = $_GET['check_in'] ?? '';
$check_out = $_GET['check_out'] ?? '';
$guests = (int)($_GET['guests'] ?? 0);
$category = (int)($_GET['category'] ?? 0);

$where = ["p.is_active = 1"];
$params = [];
$types = "";

if (!empty($location)) {
    $where[] = "(p.city LIKE ? OR p.country LIKE ? OR p.location LIKE ? OR p.title LIKE ?)";
    $search = "%$location%";
    $params = array_merge($params, [$search, $search, $search, $search]);
    $types .= "ssss";
}

if ($guests > 0) {
    $where[] = "p.max_guests >= ?";
    $params[] = $guests;
    $types .= "i";
}

if ($category > 0) {
    $where[] = "p.category_id = ?";
    $params[] = $category;
    $types .= "i";
}

$sql = "SELECT p.*, u.full_name as host_name FROM properties p JOIN users u ON p.host_id = u.id WHERE " . implode(" AND ", $where) . " ORDER BY p.rating DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$results = $stmt->get_result();

$wishlist_ids = [];
if (isLoggedIn()) {
    $uid = (int)$_SESSION['user_id'];
    $wResult = $conn->query("SELECT property_id FROM wishlists WHERE user_id = $uid");
    while ($w = $wResult->fetch_assoc()) {
        $wishlist_ids[] = $w['property_id'];
    }
}

$categories = $conn->query("SELECT * FROM categories ORDER BY id");

$searchTitle = !empty($location) ? "Results for \"$location\"" : ($category > 0 ? "Category results" : "All properties");
$pageTitle = 'Search';
require_once '../includes/header.php';
?>

<div class="categories-bar">
    <div class="categories-scroll">
        <?php while ($cat = $categories->fetch_assoc()): ?>
            <div class="category-item <?= $category === $cat['id'] ? 'active' : '' ?>" data-category-id="<?= $cat['id'] ?>">
                <i class="fa-solid <?= sanitize($cat['icon']) ?>"></i>
                <span><?= sanitize($cat['name']) ?></span>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<div class="main-content">
    <div class="search-results-header">
        <h2><?= sanitize($searchTitle) ?></h2>
        <p><?= $results->num_rows ?> stay<?= $results->num_rows !== 1 ? 's' : '' ?> found</p>
    </div>

    <?php if ($results->num_rows > 0): ?>
        <div class="property-grid">
            <?php while ($prop = $results->fetch_assoc()): ?>
                <a href="<?= SITE_URL ?>/pages/property.php?id=<?= $prop['id'] ?>" class="property-card">
                    <div class="card-image">
                        <img src="<?= getImageUrl($prop['image']) ?>" alt="<?= sanitize($prop['title']) ?>" loading="lazy">
                        <button class="wishlist-btn <?= in_array($prop['id'], $wishlist_ids) ? 'active' : '' ?>" data-property-id="<?= $prop['id'] ?>">
                            <i class="<?= in_array($prop['id'], $wishlist_ids) ? 'fa-solid' : 'fa-regular' ?> fa-heart"></i>
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
                        <div class="card-type"><?= sanitize($prop['property_type']) ?></div>
                        <div class="card-price"><strong><?= formatPrice($prop['price_per_night']) ?></strong> night</div>
                    </div>
                </a>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fa-solid fa-magnifying-glass"></i>
            <h3>No results found</h3>
            <p>Try adjusting your search or filters to find what you're looking for</p>
            <a href="<?= SITE_URL ?>">Explore all stays</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
