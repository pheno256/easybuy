<?php
function preventXSS($data) {
    if (is_array($data)) {
        return array_map('preventXSS', $data);
    }
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

function validatePhoneNumber($phone) {
    // Ugandan phone numbers: 07XXXXXXXX or 07XX XXXXXX
    return preg_match('/^07[0-9]{8}$/', preg_replace('/\s+/', '', $phone));
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function generateRandomToken($length = 32) {
    return bin2hex(random_bytes($length));
}

function rateLimit($key, $limit = 10, $window = 60) {
    $storage = $_SESSION['rate_limits'] ?? [];
    $now = time();
    
    if (!isset($storage[$key])) {
        $storage[$key] = ['count' => 1, 'first_request' => $now];
    } else {
        if ($now - $storage[$key]['first_request'] < $window) {
            $storage[$key]['count']++;
        } else {
            $storage[$key] = ['count' => 1, 'first_request' => $now];
        }
    }
    
    $_SESSION['rate_limits'] = $storage;
    
    return $storage[$key]['count'] <= $limit;
}