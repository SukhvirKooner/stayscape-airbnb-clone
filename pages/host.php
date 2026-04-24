<?php
require_once '../includes/config.php';

if (!isLoggedIn()) {
    $_SESSION['error'] = 'Please log in to list your property.';
    redirect(SITE_URL . '/pages/login.php');
}

$categories = $conn->query("SELECT * FROM categories ORDER BY name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price_per_night'] ?? 0);
    $location = trim($_POST['location'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $max_guests = (int)($_POST['max_guests'] ?? 1);
    $bedrooms = (int)($_POST['bedrooms'] ?? 1);
    $bathrooms = (int)($_POST['bathrooms'] ?? 1);
    $beds = (int)($_POST['beds'] ?? 1);
    $property_type = trim($_POST['property_type'] ?? 'Entire place');
    $amenities_arr = $_POST['amenities'] ?? [];
    $amenities = implode(',', $amenities_arr);
    $user_id = (int)$_SESSION['user_id'];

    if (empty($title) || empty($description) || $price <= 0 || empty($city) || empty($country)) {
        $_SESSION['error'] = 'Please fill in all required fields.';
    } else {
        $image_name = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['image/jpeg', 'image/png', 'image/webp'];
            if (in_array($_FILES['image']['type'], $allowed)) {
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $image_name = uniqid('prop_') . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $image_name);
            }
        }

        $stmt = $conn->prepare("INSERT INTO properties (host_id, category_id, title, description, price_per_night, location, city, country, max_guests, bedrooms, bathrooms, beds, property_type, amenities, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissdsssiiiisss", $user_id, $category_id, $title, $description, $price, $location, $city, $country, $max_guests, $bedrooms, $bathrooms, $beds, $property_type, $amenities, $image_name);

        if ($stmt->execute()) {
            $conn->query("UPDATE users SET is_host = 1 WHERE id = $user_id");
            $_SESSION['success'] = 'Your property has been listed!';
            redirect(SITE_URL . '/pages/my-listings.php');
        } else {
            $_SESSION['error'] = 'Failed to create listing. Please try again.';
        }
        $stmt->close();
    }
}

$pageTitle = 'Host Your Place';
require_once '../includes/header.php';
?>

<div class="host-container">
    <h1>List your place on <?= SITE_NAME ?></h1>
    <p class="subtitle">Share your space and start earning</p>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Property Title *</label>
            <input type="text" id="title" name="title" placeholder="Give your place a catchy title" required value="<?= isset($_POST['title']) ? sanitize($_POST['title']) : '' ?>">
        </div>

        <div class="form-group">
            <label for="description">Description *</label>
            <textarea id="description" name="description" placeholder="Describe your property, the space, neighbourhood, and what makes it special..." required><?= isset($_POST['description']) ? sanitize($_POST['description']) : '' ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="property_type">Property Type</label>
                <select id="property_type" name="property_type">
                    <option value="Entire place">Entire place</option>
                    <option value="Entire apartment">Entire apartment</option>
                    <option value="Entire villa">Entire villa</option>
                    <option value="Entire cottage">Entire cottage</option>
                    <option value="Private room">Private room</option>
                    <option value="Shared room">Shared room</option>
                    <option value="Treehouse">Treehouse</option>
                    <option value="Houseboat">Houseboat</option>
                    <option value="Entire loft">Entire loft</option>
                    <option value="Entire bungalow">Entire bungalow</option>
                </select>
            </div>
            <div class="form-group">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id">
                    <option value="">Select category</option>
                    <?php while ($cat = $categories->fetch_assoc()): ?>
                        <option value="<?= $cat['id'] ?>"><?= sanitize($cat['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="price_per_night">Price per Night (₹) *</label>
            <input type="number" id="price_per_night" name="price_per_night" placeholder="Enter price per night" min="100" required value="<?= isset($_POST['price_per_night']) ? (int)$_POST['price_per_night'] : '' ?>">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="location">Address / Area</label>
                <input type="text" id="location" name="location" placeholder="e.g. Near Juhu Beach" value="<?= isset($_POST['location']) ? sanitize($_POST['location']) : '' ?>">
            </div>
            <div class="form-group">
                <label for="city">City *</label>
                <input type="text" id="city" name="city" placeholder="e.g. Mumbai" required value="<?= isset($_POST['city']) ? sanitize($_POST['city']) : '' ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="country">Country *</label>
            <input type="text" id="country" name="country" placeholder="e.g. India" required value="<?= isset($_POST['country']) ? sanitize($_POST['country']) : '' ?>">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="max_guests">Max Guests</label>
                <input type="number" id="max_guests" name="max_guests" min="1" max="20" value="<?= isset($_POST['max_guests']) ? (int)$_POST['max_guests'] : 2 ?>">
            </div>
            <div class="form-group">
                <label for="bedrooms">Bedrooms</label>
                <input type="number" id="bedrooms" name="bedrooms" min="1" max="20" value="<?= isset($_POST['bedrooms']) ? (int)$_POST['bedrooms'] : 1 ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="beds">Beds</label>
                <input type="number" id="beds" name="beds" min="1" max="20" value="<?= isset($_POST['beds']) ? (int)$_POST['beds'] : 1 ?>">
            </div>
            <div class="form-group">
                <label for="bathrooms">Bathrooms</label>
                <input type="number" id="bathrooms" name="bathrooms" min="1" max="20" value="<?= isset($_POST['bathrooms']) ? (int)$_POST['bathrooms'] : 1 ?>">
            </div>
        </div>

        <div class="form-group">
            <label>Amenities</label>
            <div class="amenities-checkboxes">
                <?php
                $all_amenities = ['WiFi','Pool','Kitchen','AC','Parking','Beach Access','TV','Washer','Fireplace','Mountain View','Garden','BBQ','Gym','Workspace','Balcony','Heating','Elevator','Breakfast'];
                foreach ($all_amenities as $a):
                ?>
                    <label class="amenity-checkbox">
                        <input type="checkbox" name="amenities[]" value="<?= $a ?>">
                        <?= $a ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="image">Property Photo</label>
            <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp">
        </div>

        <button type="submit" class="btn-primary" style="margin-top:8px;">Create Listing</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
