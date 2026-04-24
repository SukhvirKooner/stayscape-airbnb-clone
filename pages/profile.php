<?php
require_once '../includes/config.php';

if (!isLoggedIn()) {
    redirect(SITE_URL . '/pages/login.php');
}

$user = getCurrentUser($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $bio = trim($_POST['bio'] ?? '');

    if (empty($name)) {
        $_SESSION['error'] = 'Name is required.';
    } else {
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, phone = ?, bio = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $phone, $bio, $user['id']);

        if ($stmt->execute()) {
            $_SESSION['user_name'] = $name;
            $_SESSION['success'] = 'Profile updated successfully!';
            redirect(SITE_URL . '/pages/profile.php');
        } else {
            $_SESSION['error'] = 'Failed to update profile.';
        }
        $stmt->close();
    }
}

$user_id = (int)$user['id'];
$booking_count = $conn->query("SELECT COUNT(*) as c FROM bookings WHERE guest_id = $user_id")->fetch_assoc()['c'];
$review_count = $conn->query("SELECT COUNT(*) as c FROM reviews WHERE user_id = $user_id")->fetch_assoc()['c'];

$pageTitle = 'Profile';
require_once '../includes/header.php';
?>

<div class="profile-container">
    <div class="profile-header">
        <div class="profile-avatar-large"><?= strtoupper(substr($user['full_name'], 0, 1)) ?></div>
        <div class="profile-info">
            <h1><?= sanitize($user['full_name']) ?></h1>
            <p>Member since <?= date('F Y', strtotime($user['created_at'])) ?></p>
            <p><?= $booking_count ?> trip<?= $booking_count !== 1 ? 's' : '' ?> &middot; <?= $review_count ?> review<?= $review_count !== 1 ? 's' : '' ?></p>
        </div>
    </div>

    <h2 style="margin-bottom:16px;">Edit Profile</h2>
    <form method="POST">
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" value="<?= sanitize($user['full_name']) ?>" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" value="<?= sanitize($user['email']) ?>" disabled style="background:var(--gray-lighter);cursor:not-allowed;">
        </div>
        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="tel" id="phone" name="phone" value="<?= sanitize($user['phone'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="bio">About</label>
            <textarea id="bio" name="bio" placeholder="Tell others about yourself..."><?= sanitize($user['bio'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="btn-primary">Save Changes</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
