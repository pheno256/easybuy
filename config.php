<?php
/**
 * EasyBuy Uganda - Main Configuration File
 * Version: 2.0.0
 */

// ============================================
// ERROR REPORTING (Development Mode)
// ============================================
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../logs/php_errors.log');

// ============================================
// SESSION CONFIGURATION
// ============================================
if (session_status() === PHP_SESSION_NONE) {
    // Set session parameters
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 in production with HTTPS
    ini_set('session.cookie_samesite', 'Lax');
    
    // Start session
    session_start();
}

// ============================================
// TIMEZONE SETTING
// ============================================
date_default_timezone_set('Africa/Kampala');

// ============================================
// DEFINE ROOT PATHS
// ============================================
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', realpath(__DIR__ . '/../..') . DS);
}
if (!defined('APP_PATH')) {
    define('APP_PATH', ROOT_PATH . 'app' . DS);
}
if (!defined('PUBLIC_PATH')) {
    define('PUBLIC_PATH', ROOT_PATH . 'public' . DS);
}
if (!defined('ADMIN_PATH')) {
    define('ADMIN_PATH', ROOT_PATH . 'admin' . DS);
}
if (!defined('VENDOR_PATH')) {
    define('VENDOR_PATH', ROOT_PATH . 'vendor' . DS);
}
if (!defined('LOG_PATH')) {
    define('LOG_PATH', ROOT_PATH . 'logs' . DS);
}

// ============================================
// LOAD ENVIRONMENT VARIABLES
// ============================================
$envFile = ROOT_PATH . '.env';
$envVariables = [];

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines !== false) {
        foreach ($lines as $line) {
            // Skip comments
            $trimmedLine = trim($line);
            if (strpos($trimmedLine, '#') === 0 || empty($trimmedLine)) {
                continue;
            }
            
            // Parse key=value
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1]);
                $envVariables[$key] = $value;
                putenv("$key=$value");
                $_ENV[$key] = $value;
            }
        }
    }
}

// Helper function to get environment variable
if (!function_exists('getEnvValue')) {
    function getEnvValue($key, $default = null) {
        $value = getenv($key);
        if ($value === false) {
            global $envVariables;
            return isset($envVariables[$key]) ? $envVariables[$key] : $default;
        }
        return $value;
    }
}

// ============================================
// DATABASE CONFIGURATION
// ============================================
if (!defined('DB_HOST')) {
    define('DB_HOST', getEnvValue('DB_HOST', 'localhost'));
}
if (!defined('DB_NAME')) {
    define('DB_NAME', getEnvValue('DB_NAME', 'easybuy_db'));
}
if (!defined('DB_USER')) {
    define('DB_USER', getEnvValue('DB_USER', 'root'));
}
if (!defined('DB_PASS')) {
    define('DB_PASS', getEnvValue('DB_PASSWORD', ''));
}
if (!defined('DB_PORT')) {
    define('DB_PORT', getEnvValue('DB_PORT', '3306'));
}
if (!defined('DB_CHARSET')) {
    define('DB_CHARSET', 'utf8mb4');
}

// ============================================
// APPLICATION CONFIGURATION
// ============================================
if (!defined('APP_NAME')) {
    define('APP_NAME', getEnvValue('APP_NAME', 'EasyBuy Uganda'));
}
if (!defined('APP_URL')) {
    define('APP_URL', getEnvValue('APP_URL', 'http://localhost/easybuy-php'));
}
if (!defined('APP_ENV')) {
    define('APP_ENV', getEnvValue('APP_ENV', 'development'));
}
if (!defined('APP_DEBUG')) {
    $debugValue = getEnvValue('APP_DEBUG', 'true');
    define('APP_DEBUG', filter_var($debugValue, FILTER_VALIDATE_BOOLEAN));
}
if (!defined('APP_VERSION')) {
    define('APP_VERSION', '2.0.0');
}

// ============================================
// SESSION CONFIGURATION
// ============================================
if (!defined('SESSION_LIFETIME')) {
    define('SESSION_LIFETIME', getEnvValue('SESSION_LIFETIME', 120));
}
if (!defined('SESSION_NAME')) {
    define('SESSION_NAME', getEnvValue('SESSION_NAME', 'easybuy_session'));
}

// Set session name
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME * 60);
}

// ============================================
// SECURITY CONFIGURATION
// ============================================
if (!defined('SALT')) {
    define('SALT', getEnvValue('SALT', 'easybuy_salt_2024'));
}
if (!defined('CSRF_TOKEN_NAME')) {
    define('CSRF_TOKEN_NAME', 'csrf_token');
}
if (!defined('CSRF_TOKEN_LIFETIME')) {
    define('CSRF_TOKEN_LIFETIME', 3600); // 1 hour
}

// ============================================
// UPLOAD CONFIGURATION
// ============================================
if (!defined('MAX_FILE_SIZE')) {
    define('MAX_FILE_SIZE', 5242880); // 5MB
}
if (!defined('ALLOWED_EXTENSIONS')) {
    define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
}
if (!defined('UPLOAD_PATH')) {
    define('UPLOAD_PATH', PUBLIC_PATH . 'assets' . DS . 'uploads' . DS);
}
if (!defined('PRODUCT_IMAGE_PATH')) {
    define('PRODUCT_IMAGE_PATH', UPLOAD_PATH . 'products' . DS);
}
if (!defined('CATEGORY_IMAGE_PATH')) {
    define('CATEGORY_IMAGE_PATH', UPLOAD_PATH . 'categories' . DS);
}
if (!defined('BRAND_IMAGE_PATH')) {
    define('BRAND_IMAGE_PATH', UPLOAD_PATH . 'brands' . DS);
}
if (!defined('BLOG_IMAGE_PATH')) {
    define('BLOG_IMAGE_PATH', UPLOAD_PATH . 'blog' . DS);
}

// Create upload directories if they don't exist
$directories = [UPLOAD_PATH, PRODUCT_IMAGE_PATH, CATEGORY_IMAGE_PATH, BRAND_IMAGE_PATH, BLOG_IMAGE_PATH];
foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// ============================================
// PAYMENT CONFIGURATION
// ============================================
// MTN Mobile Money
if (!defined('MTN_API_URL')) {
    define('MTN_API_URL', getEnvValue('MTN_API_URL', 'https://sandbox.mtn.com/momo'));
}
if (!defined('MTN_API_USER')) {
    define('MTN_API_USER', getEnvValue('MTN_API_USER', ''));
}
if (!defined('MTN_API_KEY')) {
    define('MTN_API_KEY', getEnvValue('MTN_API_KEY', ''));
}
if (!defined('MTN_SUBSCRIPTION_KEY')) {
    define('MTN_SUBSCRIPTION_KEY', getEnvValue('MTN_SUBSCRIPTION_KEY', ''));
}
if (!defined('MTN_CALLBACK_URL')) {
    define('MTN_CALLBACK_URL', getEnvValue('MTN_CALLBACK_URL', APP_URL . '/api/payment/mtn-callback'));
}

// Airtel Money
if (!defined('AIRTel_API_URL')) {
    define('AIRTel_API_URL', getEnvValue('AIRTel_API_URL', 'https://openapi.airtel.africa'));
}
if (!defined('AIRTel_CLIENT_ID')) {
    define('AIRTel_CLIENT_ID', getEnvValue('AIRTel_CLIENT_ID', ''));
}
if (!defined('AIRTel_CLIENT_SECRET')) {
    define('AIRTel_CLIENT_SECRET', getEnvValue('AIRTel_CLIENT_SECRET', ''));
}
if (!defined('AIRTel_API_KEY')) {
    define('AIRTel_API_KEY', getEnvValue('AIRTel_API_KEY', ''));
}
if (!defined('AIRTel_CALLBACK_URL')) {
    define('AIRTel_CALLBACK_URL', getEnvValue('AIRTel_CALLBACK_URL', APP_URL . '/api/payment/airtel-callback'));
}

// ============================================
// EMAIL CONFIGURATION
// ============================================
if (!defined('SMTP_HOST')) {
    define('SMTP_HOST', getEnvValue('SMTP_HOST', 'smtp.gmail.com'));
}
if (!defined('SMTP_PORT')) {
    define('SMTP_PORT', getEnvValue('SMTP_PORT', 587));
}
if (!defined('SMTP_USER')) {
    define('SMTP_USER', getEnvValue('SMTP_USER', ''));
}
if (!defined('SMTP_PASSWORD')) {
    define('SMTP_PASSWORD', getEnvValue('SMTP_PASSWORD', ''));
}
if (!defined('SMTP_ENCRYPTION')) {
    define('SMTP_ENCRYPTION', getEnvValue('SMTP_ENCRYPTION', 'tls'));
}
if (!defined('ADMIN_EMAIL')) {
    define('ADMIN_EMAIL', getEnvValue('ADMIN_EMAIL', 'admin@easybuy.ug'));
}

// ============================================
// SHIPPING CONFIGURATION
// ============================================
if (!defined('FREE_DELIVERY_THRESHOLD')) {
    define('FREE_DELIVERY_THRESHOLD', 200000);
}
if (!defined('STANDARD_DELIVERY_FEE')) {
    define('STANDARD_DELIVERY_FEE', 15000);
}
if (!defined('EXPRESS_DELIVERY_FEE')) {
    define('EXPRESS_DELIVERY_FEE', 25000);
}

// ============================================
// CURRENCY CONFIGURATION
// ============================================
if (!defined('CURRENCY_SYMBOL')) {
    define('CURRENCY_SYMBOL', 'UGX');
}
if (!defined('CURRENCY_CODE')) {
    define('CURRENCY_CODE', 'UGX');
}
if (!defined('DECIMAL_PLACES')) {
    define('DECIMAL_PLACES', 0);
}
if (!defined('DECIMAL_SEPARATOR')) {
    define('DECIMAL_SEPARATOR', '.');
}
if (!defined('THOUSANDS_SEPARATOR')) {
    define('THOUSANDS_SEPARATOR', ',');
}

// ============================================
// CACHE CONFIGURATION
// ============================================
if (!defined('CACHE_ENABLED')) {
    define('CACHE_ENABLED', APP_ENV === 'production');
}
if (!defined('CACHE_PATH')) {
    define('CACHE_PATH', ROOT_PATH . 'tmp' . DS . 'cache' . DS);
}
if (!defined('CACHE_LIFETIME')) {
    define('CACHE_LIFETIME', 3600); // 1 hour
}

// Create cache directory
if (!file_exists(CACHE_PATH)) {
    @mkdir(CACHE_PATH, 0777, true);
}

// ============================================
// LOGGING CONFIGURATION
// ============================================
if (!defined('LOG_ENABLED')) {
    define('LOG_ENABLED', true);
}
if (!defined('LOG_LEVEL')) {
    define('LOG_LEVEL', APP_ENV === 'production' ? 'error' : 'debug');
}

// Create log directory
if (!file_exists(LOG_PATH)) {
    @mkdir(LOG_PATH, 0777, true);
}

// ============================================
// PAGINATION CONFIGURATION
// ============================================
if (!defined('PRODUCTS_PER_PAGE')) {
    define('PRODUCTS_PER_PAGE', 12);
}
if (!defined('ADMIN_PRODUCTS_PER_PAGE')) {
    define('ADMIN_PRODUCTS_PER_PAGE', 25);
}
if (!defined('ORDERS_PER_PAGE')) {
    define('ORDERS_PER_PAGE', 20);
}

// ============================================
// IMAGE CONFIGURATION
// ============================================
if (!defined('IMAGE_QUALITY')) {
    define('IMAGE_QUALITY', 80);
}
if (!defined('THUMBNAIL_WIDTH')) {
    define('THUMBNAIL_WIDTH', 300);
}
if (!defined('THUMBNAIL_HEIGHT')) {
    define('THUMBNAIL_HEIGHT', 300);
}
if (!defined('MEDIUM_WIDTH')) {
    define('MEDIUM_WIDTH', 600);
}
if (!defined('MEDIUM_HEIGHT')) {
    define('MEDIUM_HEIGHT', 600);
}
if (!defined('LARGE_WIDTH')) {
    define('LARGE_WIDTH', 1200);
}
if (!defined('LARGE_HEIGHT')) {
    define('LARGE_HEIGHT', 1200);
}

// ============================================
// HELPER FUNCTIONS
// ============================================

/**
 * Get environment variable with fallback
 */
if (!function_exists('env')) {
    function env($key, $default = null) {
        $value = getenv($key);
        if ($value === false) {
            return $default;
        }
        return $value;
    }
}

/**
 * Check if application is in debug mode
 */
if (!function_exists('isDebug')) {
    function isDebug() {
        return defined('APP_DEBUG') && APP_DEBUG === true;
    }
}

/**
 * Check if application is in production mode
 */
if (!function_exists('isProduction')) {
    function isProduction() {
        return defined('APP_ENV') && APP_ENV === 'production';
    }
}

/**
 * Get application URL
 */
if (!function_exists('url')) {
    function url($path = '') {
        $url = rtrim(APP_URL, '/');
        if (!empty($path)) {
            $url .= '/' . ltrim($path, '/');
        }
        return $url;
    }
}

/**
 * Redirect to URL
 */
if (!function_exists('redirect')) {
    function redirect($url, $permanent = false) {
        if ($permanent) {
            header('HTTP/1.1 301 Moved Permanently');
        }
        header('Location: ' . $url);
        exit();
    }
}

/**
 * Display formatted variable (debug only)
 */
if (!function_exists('debug')) {
    function debug($var, $die = false) {
        if (isDebug()) {
            echo '<pre>';
            print_r($var);
            echo '</pre>';
            if ($die) {
                die();
            }
        }
    }
}

/**
 * Log message to file
 */
if (!function_exists('logMessage')) {
    function logMessage($message, $type = 'info') {
        if (!defined('LOG_ENABLED') || !LOG_ENABLED) {
            return;
        }
        
        $logFile = LOG_PATH . date('Y-m-d') . '.log';
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] [$type] $message" . PHP_EOL;
        @file_put_contents($logFile, $logEntry, FILE_APPEND);
    }
}

/**
 * Generate CSRF token
 */
if (!function_exists('generateCsrfToken')) {
    function generateCsrfToken() {
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time();
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }
}

/**
 * Verify CSRF token
 */
if (!function_exists('verifyCsrfToken')) {
    function verifyCsrfToken($token) {
        if (!isset($_SESSION[CSRF_TOKEN_NAME]) || !isset($_SESSION['csrf_token_time'])) {
            return false;
        }
        
        // Check token age
        if (time() - $_SESSION['csrf_token_time'] > CSRF_TOKEN_LIFETIME) {
            unset($_SESSION[CSRF_TOKEN_NAME]);
            unset($_SESSION['csrf_token_time']);
            return false;
        }
        
        return hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    }
}

/**
 * Sanitize input
 */
if (!function_exists('sanitize')) {
    function sanitize($input) {
        if (is_array($input)) {
            return array_map('sanitize', $input);
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Generate slug from string
 */
if (!function_exists('createSlug')) {
    function createSlug($string) {
        $string = strtolower($string);
        $string = preg_replace('/[^a-z0-9-]/', '-', $string);
        $string = preg_replace('/-+/', '-', $string);
        return trim($string, '-');
    }
}

/**
 * Format currency
 */
if (!function_exists('formatCurrency')) {
    function formatCurrency($amount) {
        return CURRENCY_SYMBOL . ' ' . number_format($amount, DECIMAL_PLACES, DECIMAL_SEPARATOR, THOUSANDS_SEPARATOR);
    }
}

/**
 * Format date
 */
if (!function_exists('formatDate')) {
    function formatDate($date, $format = 'M d, Y') {
        return date($format, strtotime($date));
    }
}

/**
 * Generate random string
 */
if (!function_exists('randomString')) {
    function randomString($length = 10) {
        return bin2hex(random_bytes($length / 2));
    }
}

/**
 * Get client IP address
 */
if (!function_exists('getClientIP')) {
    function getClientIP() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
}

// ============================================
// INITIALIZATION
// ============================================

// Set time limit for long operations
set_time_limit(60);

// Set memory limit
ini_set('memory_limit', '256M');

// Set default timezone
date_default_timezone_set('Africa/Kampala');

// Set default charset
ini_set('default_charset', 'UTF-8');

// Generate CSRF token for forms (only if session is active)
if (session_status() === PHP_SESSION_ACTIVE && !isset($_SESSION[CSRF_TOKEN_NAME])) {
    generateCsrfToken();
}

// Log startup in debug mode
if (isDebug()) {
    logMessage('Application initialized in ' . APP_ENV . ' mode', 'info');
}

// ============================================
// ERROR HANDLER
// ============================================
if (!function_exists('customErrorHandler')) {
    function customErrorHandler($errno, $errstr, $errfile, $errline) {
        if (!(error_reporting() & $errno)) {
            return false;
        }
        
        $message = "Error [$errno] $errstr in $errfile on line $errline";
        logMessage($message, 'error');
        
        if (isDebug()) {
            echo "<div style='background:#f8d7da; color:#721c24; padding:10px; margin:10px; border:1px solid #f5c6cb; border-radius:5px;'>";
            echo "<strong>Error:</strong> $errstr<br>";
            echo "<strong>File:</strong> $errfile<br>";
            echo "<strong>Line:</strong> $errline";
            echo "</div>";
        }
        
        return true;
    }
}

if (!function_exists('customExceptionHandler')) {
    function customExceptionHandler($exception) {
        $message = "Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine();
        logMessage($message, 'critical');
        
        if (isDebug()) {
            echo "<div style='background:#f8d7da; color:#721c24; padding:10px; margin:10px; border:1px solid #f5c6cb; border-radius:5px;'>";
            echo "<strong>Exception:</strong> " . $exception->getMessage() . "<br>";
            echo "<strong>File:</strong> " . $exception->getFile() . "<br>";
            echo "<strong>Line:</strong> " . $exception->getLine();
            echo "</div>";
        } else {
            echo "<div style='background:#f8d7da; color:#721c24; padding:10px; margin:10px; border:1px solid #f5c6cb; border-radius:5px;'>";
            echo "<strong>An error occurred.</strong> Please try again later.";
            echo "</div>";
        }
    }
}

// Set error handlers
set_error_handler('customErrorHandler');
set_exception_handler('customExceptionHandler');

// ============================================
// SECURITY HEADERS (Production)
// ============================================
if (isProduction() && !headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

// ============================================
// CONFIGURATION COMPLETE
// ============================================