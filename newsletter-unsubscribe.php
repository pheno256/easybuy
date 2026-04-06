<?php
require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$email = $_GET['email'] ?? '';
$token = $_GET['token'] ?? '';
$message = '';
$error = '';

$db = Database::getInstance();

if(!empty($email) && !empty($token)) {
    $subscriber = $db->query("
        SELECT * FROM newsletter 
        WHERE email = ? AND unsubscribe_token = ? AND status = 'active'
    ", [$email, $token])->fetch();
    
    if($subscriber) {
        $db->query("UPDATE newsletter SET status = 'unsubscribed', unsubscribed_at = NOW() WHERE id = ?", [$subscriber['id']]);
        $message = "You have been successfully unsubscribed from our newsletter.";
    } else {
        $error = "Invalid unsubscribe link or already unsubscribed.";
    }
} else {
    $error = "Invalid unsubscribe link.";
}

$page_title = 'Newsletter Unsubscribe';

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm text-center">
                <div class="card-body p-5">
                    <?php if($message): ?>
                    <i class="fas fa-check-circle text-success fa-5x mb-4"></i>
                    <h2 class="mb-3">Unsubscribed Successfully</h2>
                    <p><?php echo $message; ?></p>
                    <div class="mt-4">
                        <a href="index.php" class="btn btn-primary">Return to Homepage</a>
                    </div>
                    <?php else: ?>
                    <i class="fas fa-exclamation-triangle text-warning fa-5x mb-4"></i>
                    <h2 class="mb-3">Unsubscribe Failed</h2>
                    <p><?php echo $error; ?></p>
                    <div class="mt-4">
                        <a href="index.php" class="btn btn-primary">Return to Homepage</a>
                        <a href="contact.php" class="btn btn-outline-secondary">Contact Support</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>