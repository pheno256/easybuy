<?php
function redirect($path) {
    header("Location: " . BASE_URL . "/public/" . ltrim($path, '/'));
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function getCartCount() {
    if (!isset($_SESSION['cart'])) {
        return 0;
    }
    $count = 0;
    foreach ($_SESSION['cart'] as $item) {
        $count += $item['quantity'];
    }
    return $count;
}

function formatPrice($price) {
    return 'UGX ' . number_format($price, 0);
}

function generateOrderNumber() {
    return 'ORD-' . date('Ymd') . '-' . strtoupper(uniqid());
}

function getUgandaDistricts() {
    return [
        'Kampala', 'Wakiso', 'Mukono', 'Jinja', 'Mbale', 'Mbarara', 'Gulu', 
        'Lira', 'Entebbe', 'Masaka', 'Fort Portal', 'Arua', 'Soroti', 'Kabale',
        'Kasese', 'Busia', 'Tororo', 'Hoima', 'Iganga', 'Kitgum', 'Apac',
        'Mityana', 'Luwero', 'Kayunga', 'Nakasongola', 'Rakai', 'Sembabule',
        'Lyantonde', 'Kalungu', 'Bukomansimbi', 'Butambala', 'Gomba', 'Mpigi'
    ];
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function generateCSRFToken() {
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

function verifyCSRFToken($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

function sendEmail($to, $subject, $body) {
    // Simple mail function - can be enhanced with PHPMailer
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: " . APP_NAME . " <" . SITE_EMAIL . ">" . "\r\n";
    
    return mail($to, $subject, $body, $headers);
}

function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

function logError($message, $file = 'error.log') {
    $logPath = BASE_PATH . '/logs/' . $file;
    $logMessage = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
    error_log($logMessage, 3, $logPath);
}