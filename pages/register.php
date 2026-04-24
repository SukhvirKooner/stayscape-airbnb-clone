<?php
require_once '../includes/config.php';

if (isLoggedIn()) {
    redirect(SITE_URL);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        $_SESSION['error'] = 'Please fill in all required fields.';
    } elseif ($password !== $confirm) {
        $_SESSION['error'] = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $_SESSION['error'] = 'Password must be at least 6 characters.';
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $_SESSION['error'] = 'Email already registered.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $phone, $hashed);

            if ($stmt->execute()) {
                $_SESSION['user_id'] = $conn->insert_id;
                $_SESSION['user_name'] = $name;
                $_SESSION['success'] = 'Welcome to ' . SITE_NAME . ', ' . $name . '!';
                redirect(SITE_URL);
            } else {
                $_SESSION['error'] = 'Registration failed. Please try again.';
            }
        }
        $stmt->close();
    }
}

$pageTitle = 'Sign up';
require_once '../includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h2>Sign up</h2>
        </div>
        <div class="auth-body">
            <p style="text-align:center; margin-bottom:16px; color:var(--gray); font-size:0.9rem;">Welcome to <?= SITE_NAME ?></p>

            <form method="POST">
                <div class="form-group">
                    <label for="full_name">Full Name *</label>
                    <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" required value="<?= isset($_POST['full_name']) ? sanitize($_POST['full_name']) : '' ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required value="<?= isset($_POST['email']) ? sanitize($_POST['email']) : '' ?>">
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" value="<?= isset($_POST['phone']) ? sanitize($_POST['phone']) : '' ?>">
                </div>
                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" id="password" name="password" placeholder="Create a password (min 6 chars)" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password *</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                </div>
                <button type="submit" class="btn-primary">Sign up</button>
            </form>
        </div>
        <div class="auth-footer">
            Already have an account? <a href="login.php">Log in</a>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
