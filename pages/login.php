<?php
require_once '../includes/config.php';

if (isLoggedIn()) {
    redirect(SITE_URL);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'Please fill in all fields.';
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['success'] = 'Welcome back, ' . $user['full_name'] . '!';
            redirect(SITE_URL);
        } else {
            $_SESSION['error'] = 'Invalid email or password.';
        }
        $stmt->close();
    }
}

$pageTitle = 'Log in';
require_once '../includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h2>Log in</h2>
        </div>
        <div class="auth-body">
            <p style="text-align:center; margin-bottom:16px; color:var(--gray); font-size:0.9rem;">Welcome to <?= SITE_NAME ?></p>

            <form method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required value="<?= isset($_POST['email']) ? sanitize($_POST['email']) : '' ?>">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn-primary">Continue</button>
            </form>

            <div class="auth-divider">or</div>

            <p style="text-align:center; font-size:0.85rem; color:var(--gray);">
                Demo login: <strong>demo@stayscape.com</strong> / <strong>password123</strong>
            </p>
        </div>
        <div class="auth-footer">
            Don't have an account? <a href="register.php">Sign up</a>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
