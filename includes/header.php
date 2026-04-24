<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? sanitize($pageTitle) . ' | ' : '' ?><?= SITE_NAME ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="<?= SITE_URL ?>" class="logo">
                <i class="fa-solid fa-house-chimney"></i>
                <span><?= SITE_NAME ?></span>
            </a>

            <div class="search-bar">
                <form action="<?= SITE_URL ?>/pages/search.php" method="GET" class="search-form">
                    <div class="search-field">
                        <label>Where</label>
                        <input type="text" name="location" placeholder="Search destinations" value="<?= isset($_GET['location']) ? sanitize($_GET['location']) : '' ?>">
                    </div>
                    <div class="search-divider"></div>
                    <div class="search-field">
                        <label>Check in</label>
                        <input type="date" name="check_in" value="<?= isset($_GET['check_in']) ? sanitize($_GET['check_in']) : '' ?>">
                    </div>
                    <div class="search-divider"></div>
                    <div class="search-field">
                        <label>Check out</label>
                        <input type="date" name="check_out" value="<?= isset($_GET['check_out']) ? sanitize($_GET['check_out']) : '' ?>">
                    </div>
                    <div class="search-divider"></div>
                    <div class="search-field">
                        <label>Guests</label>
                        <input type="number" name="guests" placeholder="Add guests" min="1" max="20" value="<?= isset($_GET['guests']) ? (int)$_GET['guests'] : '' ?>">
                    </div>
                    <button type="submit" class="search-btn">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </form>
            </div>

            <div class="nav-right">
                <?php if (isLoggedIn()): ?>
                    <a href="<?= SITE_URL ?>/pages/host.php" class="host-link">Become a Host</a>
                    <div class="user-menu">
                        <button class="user-menu-btn" id="userMenuBtn">
                            <i class="fa-solid fa-bars"></i>
                            <i class="fa-solid fa-circle-user"></i>
                        </button>
                        <div class="user-dropdown" id="userDropdown">
                            <a href="<?= SITE_URL ?>/pages/profile.php"><i class="fa-solid fa-user"></i> Profile</a>
                            <a href="<?= SITE_URL ?>/pages/bookings.php"><i class="fa-solid fa-calendar-check"></i> My Trips</a>
                            <a href="<?= SITE_URL ?>/pages/wishlist.php"><i class="fa-solid fa-heart"></i> Wishlists</a>
                            <a href="<?= SITE_URL ?>/pages/messages.php"><i class="fa-solid fa-message"></i> Messages</a>
                            <hr>
                            <a href="<?= SITE_URL ?>/pages/host.php"><i class="fa-solid fa-house"></i> Host Your Place</a>
                            <a href="<?= SITE_URL ?>/pages/my-listings.php"><i class="fa-solid fa-list"></i> My Listings</a>
                            <hr>
                            <a href="<?= SITE_URL ?>/pages/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Log out</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?= SITE_URL ?>/pages/host.php" class="host-link">Become a Host</a>
                    <div class="user-menu">
                        <button class="user-menu-btn" id="userMenuBtn">
                            <i class="fa-solid fa-bars"></i>
                            <i class="fa-solid fa-circle-user"></i>
                        </button>
                        <div class="user-dropdown" id="userDropdown">
                            <a href="<?= SITE_URL ?>/pages/login.php"><i class="fa-solid fa-right-to-bracket"></i> Log in</a>
                            <a href="<?= SITE_URL ?>/pages/register.php"><i class="fa-solid fa-user-plus"></i> Sign up</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </nav>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
