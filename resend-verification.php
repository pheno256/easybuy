<?php
require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$page_title = 'Resend Verification';
$db = Database::getInstance();
$success = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    
    $user = $db->query("
        SELECT id, full_name, email FROM users 
        WHERE email = ? AND email_verified = 0
    ", [$email])->fetch();
    
    if($user) {
        // Generate new token
        $token = bin2hex(random_bytes(32));
        $db->query("UPDATE users SET verification_token = ? WHERE id = ?", [$token, $user['id']]);
        
        // Send verification email
        $verify_link = APP_URL . "/verify-email.php?token=" . $token;
        $subject = "Verify Your Email - EasyBuy Uganda";
        $message = "
        <html>
        <head>
            <title>Email Verification</title>
        </head>
        <body>
            <h2>Hello {$user['full_name']},</h2>
            <p>Please verify your email address by clicking the link below:</p>
            <p><a href='{$verify_link}' style='background: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Verify Email</a></p>
            <p>This link will expire in 24 hours.</p>
            <br>
            <p>Best regards,<br>EasyBuy Uganda Team</p>
        </body>
        </html>
        ";
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: EasyBuy Uganda <noreply@easybuy.ug>" . "\r\n";
        
        mail($email, $subject, $message, $headers);
        
        $success = "Verification email has been resent. Please check your inbox.";
    } else {
        $error = "Email not found or already verified.";
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
                        <i class="fas fa-envelope fa-4x text-primary"></i>
                        <h2 class="mt-3">Resend Verification Email</h2>
                        <p class="text-muted">Enter your email to receive a new verification link</p>
                    </div>
                    
                    <?php if($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                    <div class="text-center mt-3">
                        <a href="login.php" class="btn btn-primary">Go to Login</a>
                    </div>
                    <?php else: ?>
                    <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-paper-plane"></i> Resend Verification
                        </button>
                    </form>
                    
                    <div class="text-center mt-4">
                        <a href="login.php">Back to Login</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>