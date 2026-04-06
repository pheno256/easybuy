<?php
require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$page_title = 'Verify Email';
$db = Database::getInstance();
$message = '';
$error = '';

$token = $_GET['token'] ?? '';

if(empty($token)) {
    header('Location: login.php');
    exit;
}

// Verify token
$user = $db->query("
    SELECT id, email, full_name FROM users 
    WHERE verification_token = ? AND email_verified = 0
", [$token])->fetch();

if($user) {
    $db->query("
        UPDATE users 
        SET email_verified = 1, verification_token = NULL 
        WHERE id = ?
    ", [$user['id']]);
    $message = "Email verified successfully! You can now login.";
} else {
    $error = "Invalid or expired verification link.";
}

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-5 text-center">
                    <?php if($message): ?>
                    <i class="fas fa-check-circle text-success fa-5x mb-4"></i>
                    <h2 class="mb-3">Email Verified!</h2>
                    <p class="lead"><?php echo $message; ?></p>
                    <div class="mt-4">
                        <a href="login.php" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> Login Now
                        </a>
                    </div>
                    <?php else: ?>
                    <i class="fas fa-times-circle text-danger fa-5x mb-4"></i>
                    <h2 class="mb-3">Verification Failed</h2>
                    <p><?php echo $error; ?></p>
                    <div class="mt-4">
                        <a href="login.php" class="btn btn-primary">Go to Login</a>
                        <a href="register.php" class="btn btn-outline-primary">Register New Account</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>