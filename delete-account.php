<?php
require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$page_title = 'Delete Account';
$db = Database::getInstance();
$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = $_POST['password'];
    $confirm = $_POST['confirm_delete'];
    
    $user = $db->query("SELECT password FROM users WHERE id = ?", [$_SESSION['user_id']])->fetch();
    
    if($confirm !== 'DELETE') {
        $error = 'Please type DELETE to confirm';
    } elseif(!password_verify($password, $user['password'])) {
        $error = 'Incorrect password';
    } else {
        // Delete user data
        $db->query("DELETE FROM cart WHERE user_id = ?", [$_SESSION['user_id']]);
        $db->query("DELETE FROM wishlist WHERE user_id = ?", [$_SESSION['user_id']]);
        $db->query("DELETE FROM reviews WHERE user_id = ?", [$_SESSION['user_id']]);
        $db->query("DELETE FROM users WHERE id = ?", [$_SESSION['user_id']]);
        
        session_destroy();
        $success = "Your account has been permanently deleted.";
    }
}

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-exclamation-triangle text-danger fa-4x mb-3"></i>
                        <h2 class="mb-3">Delete Account</h2>
                        <p class="text-danger">Warning: This action cannot be undone!</p>
                    </div>
                    
                    <?php if($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                    <div class="text-center mt-3">
                        <a href="index.php" class="btn btn-primary">Return to Homepage</a>
                    </div>
                    <?php else: ?>
                    <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <div class="alert alert-warning">
                        <strong>What will be deleted:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Your personal information</li>
                            <li>Order history (anonymized for legal purposes)</li>
                            <li>Shopping cart items</li>
                            <li>Wishlist items</li>
                            <li>Product reviews</li>
                        </ul>
                    </div>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Enter your password to confirm</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type <code class="text-danger">DELETE</code> to confirm</label>
                            <input type="text" class="form-control" name="confirm_delete" required>
                        </div>
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Are you absolutely sure? This cannot be undone.')">
                            <i class="fas fa-trash"></i> Permanently Delete Account
                        </button>
                    </form>
                    
                    <div class="text-center mt-4">
                        <a href="account.php" class="text-decoration-none">
                            <i class="fas fa-arrow-left"></i> Cancel and Go Back
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>