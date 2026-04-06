<?php
/**
 * EasyBuy Uganda - Authentication Library
 * Version: 2.0.0
 */

class Auth {
    private $db;
    private $sessionTimeout;
    
    /**
     * Constructor - Initialize database connection
     */
    public function __construct() {
        $this->db = Database::getInstance();
        $this->sessionTimeout = SESSION_LIFETIME * 60;
    }
    
    /**
     * Login user
     * @param string $email User email
     * @param string $password User password
     * @param bool $remember Remember me (set longer session)
     * @return array Result with success status and message
     */
    public function login($email, $password, $remember = false) {
        // Validate input
        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Please enter email and password'];
        }
        
        // Get user by email
        $user = $this->db->query("
            SELECT * FROM users 
            WHERE email = ? AND status = 'active'
        ", [$email])->fetch();
        
        // Check if user exists
        if (!$user) {
            logMessage("Failed login attempt for email: $email - User not found", 'warning');
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        // Verify password
        if (!password_verify($password, $user['password'])) {
            logMessage("Failed login attempt for email: $email - Invalid password", 'warning');
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        // Check if email is verified
        if (!$user['email_verified']) {
            return ['success' => false, 'message' => 'Please verify your email address before logging in. Check your inbox for verification link.'];
        }
        
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_phone'] = $user['phone'];
        $_SESSION['login_time'] = time();
        
        // Set remember me cookie if requested
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $expires = time() + (86400 * 30); // 30 days
            
            // Store token in database
            $this->db->insert('user_sessions', [
                'user_id' => $user['id'],
                'session_token' => $token,
                'ip_address' => getClientIP(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'expires_at' => date('Y-m-d H:i:s', $expires)
            ]);
            
            // Set cookie
            setcookie('remember_token', $token, $expires, '/', '', false, true);
        }
        
        // Update last login time
        $this->db->query("
            UPDATE users SET last_login = NOW() 
            WHERE id = ?
        ", [$user['id']]);
        
        logMessage("User logged in: {$user['email']} (ID: {$user['id']})", 'info');
        
        return ['success' => true, 'message' => 'Login successful', 'user' => $user];
    }
    
    /**
     * Register new user
     * @param string $full_name User's full name
     * @param string $email User's email
     * @param string $phone User's phone number
     * @param string $password User's password
     * @return array Result with success status and message
     */
    public function register($full_name, $email, $phone, $password) {
        // Validate inputs
        if (empty($full_name) || empty($email) || empty($phone) || empty($password)) {
            return ['success' => false, 'message' => 'Please fill in all fields'];
        }
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Please enter a valid email address'];
        }
        
        // Validate phone number (Ugandan format)
        if (!$this->validateUgandanPhone($phone)) {
            return ['success' => false, 'message' => 'Please enter a valid Ugandan phone number (e.g., 07XXXXXXXX or +256XXXXXXXXX)'];
        }
        
        // Validate password strength
        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters'];
        }
        
        // Format phone number to international format
        $phone = $this->formatPhoneNumber($phone);
        
        // Check if email already exists
        $existing = $this->db->query("SELECT id FROM users WHERE email = ?", [$email])->fetch();
        if ($existing) {
            return ['success' => false, 'message' => 'Email address already registered'];
        }
        
        // Check if phone already exists
        $existing = $this->db->query("SELECT id FROM users WHERE phone = ?", [$phone])->fetch();
        if ($existing) {
            return ['success' => false, 'message' => 'Phone number already registered'];
        }
        
        // Generate verification token
        $verificationToken = bin2hex(random_bytes(32));
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user
        $user_id = $this->db->insert('users', [
            'full_name' => $full_name,
            'email' => $email,
            'phone' => $phone,
            'password' => $hashed_password,
            'role' => 'user',
            'verification_token' => $verificationToken,
            'email_verified' => 0,
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        if ($user_id) {
            // Send verification email
            $this->sendVerificationEmail($email, $full_name, $verificationToken);
            
            logMessage("New user registered: $email (ID: $user_id)", 'info');
            
            return ['success' => true, 'message' => 'Registration successful! Please check your email to verify your account.'];
        }
        
        return ['success' => false, 'message' => 'Registration failed. Please try again.'];
    }
    
    /**
     * Verify user email
     * @param string $token Verification token
     * @return array Result with success status and message
     */
    public function verifyEmail($token) {
        if (empty($token)) {
            return ['success' => false, 'message' => 'Invalid verification token'];
        }
        
        $user = $this->db->query("
            SELECT id, email FROM users 
            WHERE verification_token = ? AND email_verified = 0
        ", [$token])->fetch();
        
        if (!$user) {
            return ['success' => false, 'message' => 'Invalid or expired verification token'];
        }
        
        $this->db->query("
            UPDATE users 
            SET email_verified = 1, verification_token = NULL 
            WHERE id = ?
        ", [$user['id']]);
        
        logMessage("User verified email: {$user['email']} (ID: {$user['id']})", 'info');
        
        return ['success' => true, 'message' => 'Email verified successfully! You can now login.'];
    }
    
    /**
     * Resend verification email
     * @param string $email User email
     * @return array Result with success status and message
     */
    public function resendVerification($email) {
        $user = $this->db->query("
            SELECT id, full_name, email FROM users 
            WHERE email = ? AND email_verified = 0
        ", [$email])->fetch();
        
        if (!$user) {
            return ['success' => false, 'message' => 'Email not found or already verified'];
        }
        
        // Generate new token
        $newToken = bin2hex(random_bytes(32));
        $this->db->query("
            UPDATE users SET verification_token = ? 
            WHERE id = ?
        ", [$newToken, $user['id']]);
        
        // Send verification email
        $this->sendVerificationEmail($user['email'], $user['full_name'], $newToken);
        
        return ['success' => true, 'message' => 'Verification email resent. Please check your inbox.'];
    }
    
    /**
     * Send password reset link
     * @param string $email User email
     * @return array Result with success status and message
     */
    public function forgotPassword($email) {
        if (empty($email)) {
            return ['success' => false, 'message' => 'Please enter your email address'];
        }
        
        $user = $this->db->query("
            SELECT id, full_name, email FROM users 
            WHERE email = ?
        ", [$email])->fetch();
        
        if (!$user) {
            // Don't reveal that email doesn't exist for security
            return ['success' => true, 'message' => 'If your email is registered, you will receive a password reset link.'];
        }
        
        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Store in password_resets table
        $this->db->insert('password_resets', [
            'email' => $email,
            'token' => $token,
            'expires_at' => $expires
        ]);
        
        // Also store in users table for backward compatibility
        $this->db->query("
            UPDATE users 
            SET reset_token = ?, reset_expires = ? 
            WHERE id = ?
        ", [$token, $expires, $user['id']]);
        
        // Send reset email
        $this->sendResetEmail($user['email'], $user['full_name'], $token);
        
        logMessage("Password reset requested for: $email", 'info');
        
        return ['success' => true, 'message' => 'If your email is registered, you will receive a password reset link.'];
    }
    
    /**
     * Reset password using token
     * @param string $token Reset token
     * @param string $new_password New password
     * @return array Result with success status and message
     */
    public function resetPassword($token, $new_password) {
        if (empty($token) || empty($new_password)) {
            return ['success' => false, 'message' => 'Invalid request'];
        }
        
        if (strlen($new_password) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters'];
        }
        
        // Check token in password_resets table
        $reset = $this->db->query("
            SELECT * FROM password_resets 
            WHERE token = ? AND expires_at > NOW()
        ", [$token])->fetch();
        
        if (!$reset) {
            // Check in users table for backward compatibility
            $user = $this->db->query("
                SELECT id FROM users 
                WHERE reset_token = ? AND reset_expires > NOW()
            ", [$token])->fetch();
            
            if (!$user) {
                return ['success' => false, 'message' => 'Invalid or expired reset token'];
            }
            
            $email = $this->db->query("SELECT email FROM users WHERE id = ?", [$user['id']])->fetch()['email'];
        } else {
            $email = $reset['email'];
            $user = $this->db->query("SELECT id FROM users WHERE email = ?", [$email])->fetch();
        }
        
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }
        
        // Hash new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update password
        $this->db->query("
            UPDATE users 
            SET password = ?, reset_token = NULL, reset_expires = NULL 
            WHERE id = ?
        ", [$hashed_password, $user['id']]);
        
        // Delete used reset tokens
        $this->db->query("DELETE FROM password_resets WHERE token = ?", [$token]);
        
        logMessage("Password reset for user: $email", 'info');
        
        return ['success' => true, 'message' => 'Password reset successful! You can now login with your new password.'];
    }
    
    /**
     * Change user password (logged in)
     * @param int $user_id User ID
     * @param string $current_password Current password
     * @param string $new_password New password
     * @return array Result with success status and message
     */
    public function changePassword($user_id, $current_password, $new_password) {
        // Get user
        $user = $this->db->query("
            SELECT password FROM users WHERE id = ?
        ", [$user_id])->fetch();
        
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }
        
        // Verify current password
        if (!password_verify($current_password, $user['password'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }
        
        // Validate new password
        if (strlen($new_password) < 6) {
            return ['success' => false, 'message' => 'New password must be at least 6 characters'];
        }
        
        // Hash new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update password
        $this->db->query("
            UPDATE users SET password = ? WHERE id = ?
        ", [$hashed_password, $user_id]);
        
        logMessage("Password changed for user ID: $user_id", 'info');
        
        return ['success' => true, 'message' => 'Password changed successfully'];
    }
    
    /**
     * Update user profile
     * @param int $user_id User ID
     * @param array $data Profile data
     * @return array Result with success status and message
     */
    public function updateProfile($user_id, $data) {
        $allowed_fields = ['full_name', 'phone', 'district', 'city', 'street_address'];
        $update_data = [];
        
        foreach ($allowed_fields as $field) {
            if (isset($data[$field])) {
                if ($field === 'phone') {
                    $data[$field] = $this->formatPhoneNumber($data[$field]);
                    if (!$this->validateUgandanPhone($data[$field])) {
                        return ['success' => false, 'message' => 'Invalid phone number format'];
                    }
                }
                $update_data[$field] = $data[$field];
            }
        }
        
        if (empty($update_data)) {
            return ['success' => false, 'message' => 'No data to update'];
        }
        
        $fields = [];
        $params = [];
        foreach ($update_data as $key => $value) {
            $fields[] = "$key = ?";
            $params[] = $value;
        }
        $params[] = $user_id;
        
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        $this->db->query($sql, $params);
        
        // Update session name if changed
        if (isset($update_data['full_name'])) {
            $_SESSION['user_name'] = $update_data['full_name'];
        }
        
        logMessage("Profile updated for user ID: $user_id", 'info');
        
        return ['success' => true, 'message' => 'Profile updated successfully'];
    }
    
    /**
     * Logout user
     * @return bool Success status
     */
    public function logout() {
        // Clear remember me token if exists
        if (isset($_COOKIE['remember_token'])) {
            $token = $_COOKIE['remember_token'];
            $this->db->query("DELETE FROM user_sessions WHERE session_token = ?", [$token]);
            setcookie('remember_token', '', time() - 3600, '/');
        }
        
        // Clear session
        $_SESSION = array();
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
        
        logMessage("User logged out", 'info');
        
        return true;
    }
    
    /**
     * Check if user is logged in
     * @return bool
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && isset($_SESSION['login_time']);
    }
    
    /**
     * Check if user is admin
     * @return bool
     */
    public function isAdmin() {
        return $this->isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
    
    /**
     * Check if user is vendor
     * @return bool
     */
    public function isVendor() {
        return $this->isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'vendor';
    }
    
    /**
     * Get current user data
     * @return array|null User data or null if not logged in
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        $user = $this->db->query("
            SELECT id, full_name, email, phone, role, district, city, street_address, email_verified 
            FROM users WHERE id = ?
        ", [$_SESSION['user_id']])->fetch();
        
        return $user;
    }
    
    /**
     * Check if session is expired
     * @return bool
     */
    public function isSessionExpired() {
        if (!isset($_SESSION['login_time'])) {
            return true;
        }
        
        return (time() - $_SESSION['login_time']) > $this->sessionTimeout;
    }
    
    /**
     * Refresh session
     */
    public function refreshSession() {
        if ($this->isLoggedIn()) {
            $_SESSION['login_time'] = time();
        }
    }
    
    /**
     * Auto login via remember me cookie
     * @return bool Success status
     */
    public function autoLogin() {
        if ($this->isLoggedIn()) {
            return true;
        }
        
        if (!isset($_COOKIE['remember_token'])) {
            return false;
        }
        
        $token = $_COOKIE['remember_token'];
        
        $session = $this->db->query("
            SELECT * FROM user_sessions 
            WHERE session_token = ? AND expires_at > NOW()
        ", [$token])->fetch();
        
        if (!$session) {
            return false;
        }
        
        $user = $this->db->query("
            SELECT * FROM users WHERE id = ? AND status = 'active'
        ", [$session['user_id']])->fetch();
        
        if (!$user) {
            return false;
        }
        
        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['login_time'] = time();
        
        return true;
    }
    
    /**
     * Validate Ugandan phone number
     * @param string $phone Phone number
     * @return bool
     */
    private function validateUgandanPhone($phone) {
        $pattern = '/^(?:(?:\+256|0)[1-9][0-9]{8})$/';
        return preg_match($pattern, $phone) === 1;
    }
    
    /**
     * Format phone number to international format
     * @param string $phone Phone number
     * @return string Formatted phone number
     */
    private function formatPhoneNumber($phone) {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Convert to international format
        if (substr($phone, 0, 1) === '0') {
            $phone = '256' . substr($phone, 1);
        } elseif (substr($phone, 0, 3) !== '256') {
            $phone = '256' . $phone;
        }
        
        return '+' . $phone;
    }
    
    /**
     * Send verification email
     * @param string $email Recipient email
     * @param string $name Recipient name
     * @param string $token Verification token
     * @return bool
     */
    private function sendVerificationEmail($email, $name, $token) {
        $verify_link = APP_URL . "/verify-email.php?token=" . $token;
        
        $subject = "Verify Your Email - " . APP_NAME;
        
        $message = "
        <html>
        <head>
            <title>Email Verification</title>
            <style>
                body { font-family: Arial, sans-serif; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #2563eb; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9fafb; }
                .button { display: inline-block; padding: 12px 24px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; padding: 20px; font-size: 12px; color: #6b7280; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>" . APP_NAME . "</h2>
                </div>
                <div class='content'>
                    <h3>Hello " . htmlspecialchars($name) . ",</h3>
                    <p>Thank you for registering with " . APP_NAME . "!</p>
                    <p>Please click the button below to verify your email address:</p>
                    <div style='text-align: center;'>
                        <a href='" . $verify_link . "' class='button'>Verify Email Address</a>
                    </div>
                    <p>Or copy and paste this link into your browser:</p>
                    <p><small>" . $verify_link . "</small></p>
                    <p>This link will expire in 24 hours.</p>
                    <p>If you did not create an account with us, please ignore this email.</p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " " . APP_NAME . ". All rights reserved.</p>
                    <p>" . APP_URL . "</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: " . APP_NAME . " <noreply@" . parse_url(APP_URL, PHP_URL_HOST) . ">" . "\r\n";
        
        return mail($email, $subject, $message, $headers);
    }
    
    /**
     * Send password reset email
     * @param string $email Recipient email
     * @param string $name Recipient name
     * @param string $token Reset token
     * @return bool
     */
    private function sendResetEmail($email, $name, $token) {
        $reset_link = APP_URL . "/reset-password.php?token=" . $token;
        
        $subject = "Password Reset Request - " . APP_NAME;
        
        $message = "
        <html>
        <head>
            <title>Password Reset</title>
            <style>
                body { font-family: Arial, sans-serif; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #2563eb; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9fafb; }
                .button { display: inline-block; padding: 12px 24px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .warning { background: #fef3c7; padding: 15px; border-left: 4px solid #f59e0b; margin: 20px 0; }
                .footer { text-align: center; padding: 20px; font-size: 12px; color: #6b7280; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>" . APP_NAME . "</h2>
                </div>
                <div class='content'>
                    <h3>Hello " . htmlspecialchars($name) . ",</h3>
                    <p>We received a request to reset your password for your " . APP_NAME . " account.</p>
                    <div style='text-align: center;'>
                        <a href='" . $reset_link . "' class='button'>Reset Password</a>
                    </div>
                    <p>Or copy and paste this link into your browser:</p>
                    <p><small>" . $reset_link . "</small></p>
                    <div class='warning'>
                        <strong>⚠️ Security Notice:</strong> This link will expire in 1 hour.
                    </div>
                    <p>If you did not request a password reset, please ignore this email. Your password will not be changed.</p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " " . APP_NAME . ". All rights reserved.</p>
                    <p>" . APP_URL . "</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: " . APP_NAME . " <noreply@" . parse_url(APP_URL, PHP_URL_HOST) . ">" . "\r\n";
        
        return mail($email, $subject, $message, $headers);
    }
    
    /**
     * Delete user account
     * @param int $user_id User ID
     * @param string $password Current password for verification
     * @return array Result with success status and message
     */
    public function deleteAccount($user_id, $password) {
        // Verify password
        $user = $this->db->query("SELECT password FROM users WHERE id = ?", [$user_id])->fetch();
        
        if (!$user || !password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Incorrect password'];
        }
        
        // Delete user data
        $this->db->query("DELETE FROM cart WHERE user_id = ?", [$user_id]);
        $this->db->query("DELETE FROM wishlist WHERE user_id = ?", [$user_id]);
        $this->db->query("DELETE FROM reviews WHERE user_id = ?", [$user_id]);
        $this->db->query("DELETE FROM user_sessions WHERE user_id = ?", [$user_id]);
        
        // Anonymize orders instead of deleting
        $this->db->query("
            UPDATE orders 
            SET user_id = NULL, 
                full_name = 'Deleted User', 
                email = 'deleted@easybuy.ug',
                phone = '0000000000'
            WHERE user_id = ?
        ", [$user_id]);
        
        // Delete user
        $this->db->query("DELETE FROM users WHERE id = ?", [$user_id]);
        
        // Logout
        $this->logout();
        
        logMessage("User account deleted: ID $user_id", 'info');
        
        return ['success' => true, 'message' => 'Your account has been permanently deleted'];
    }
}