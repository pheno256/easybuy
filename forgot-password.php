<?php
require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$page_title = 'Forgot Password';
$db = Database::getInstance();
$success = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    
    $user = $db->query("SELECT id, email, full_name FROM users WHERE email = ?", [$email])->fetch();
    
    if($user) {
        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $db->query("
            UPDATE users 
            SET reset_token = ?, reset_expires = ? 
            WHERE id = ?
        ", [$token, $expires, $user['id']]);
        
        // Send reset email
        $reset_link = APP_URL . "/reset-password.php?token=" . $token;
        $subject = "Password Reset Request - EasyBuy Uganda";
        $message = "
        <html>
        <head>
            <title>Password Reset</title>
        </head>
        <body>
            <h2>Hello {$user['full_name']},</h2>
            <p>We received a request to reset your password for your EasyBuy Uganda account.</p>
            <p>Click the link below to reset your password:</p>
            <p><a href='{$reset_link}' style='background: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Reset Password</a></p>
            <p>This link will expire in 1 hour.</p>
            <p>If you didn't request this, please ignore this email.</p>
            <br>
            <p>Best regards,<br>EasyBuy Uganda Team</p>
        </body>
        </html>
        ";
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: EasyBuy Uganda <noreply@easybuy.ug>" . "\r\n";
        
        mail($email, $subject, $message, $headers);
        
        $success = "Password reset link has been sent to your email address.";
    } else {
        $error = "Email address not found in our records.";
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
                        <i class="fas fa-key fa-4x text-primary"></i>
                        <h2 class="mt-3">Forgot Password?</h2>
                        <p class="text-muted">Enter your email to reset your password</p>
                    </div>
                    
                    <?php if($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    </div>
                    <div class="text-center mt-3">
                        <a href="login.php" class="btn btn-primary">Back to Login</a>
                    </div>
                    <?php else: ?>
                    <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" name="email" required 
                                       placeholder="Enter your registered email">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="fas fa-paper-plane"></i> Send Reset Link
                        </button>
                    </form>
                    
                    <div class="text-center mt-4">
                        <a href="login.php" class="text-decoration-none">
                            <i class="fas fa-arrow-left"></i> Back to Login
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>