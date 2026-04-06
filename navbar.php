<?php
$isLoggedIn = isset($_SESSION['user_id']);
$cart_count = 0;

if ($isLoggedIn) {
    $db = Database::getInstance();
    $result = $db->query("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?", [$_SESSION['user_id']]);
    $cart_count = $result->fetch()['total'] ?? 0;
}
?>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="<?php echo APP_URL; ?>">
            <i class="fas fa-shopping-bag me-2"></i>EasyBuy<span class="text-success">.ug</span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarContent">
            <form class="d-flex mx-auto w-50" action="<?php echo APP_URL; ?>/shop.php" method="GET">
                <div class="input-group">
                    <input class="form-control" type="search" name="search" placeholder="Search products..." 
                           value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
            
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo APP_URL; ?>/shop.php">
                        <i class="fas fa-store"></i> Shop
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link position-relative" href="<?php echo APP_URL; ?>/cart.php">
                        <i class="fas fa-shopping-cart"></i> Cart
                        <?php if($cart_count > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?php echo $cart_count; ?>
                        </span>
                        <?php endif; ?>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo APP_URL; ?>/wishlist.php">
                        <i class="fas fa-heart"></i> Wishlist
                    </a>
                </li>
                
                <?php if($isLoggedIn): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?php echo APP_URL; ?>/account.php">
                            <i class="fas fa-user"></i> My Account</a>
                        </li>
                        <li><a class="dropdown-item" href="<?php echo APP_URL; ?>/track-order.php">
                            <i class="fas fa-truck"></i> Track Order</a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?php echo APP_URL; ?>/logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout</a>
                        </li>
                    </ul>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo APP_URL; ?>/login.php">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-primary ms-2" href="<?php echo APP_URL; ?>/register.php">
                        Sign Up
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>