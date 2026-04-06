<?php
header('Content-Type: application/json');
require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

session_start();
$db = Database::getInstance();
$action = $_POST['action'] ?? '';

switch($action) {
    case 'apply':
        $code = strtoupper($_POST['code']);
        
        $coupon = $db->query("
            SELECT * FROM coupons 
            WHERE code = ? 
            AND status = 'active' 
            AND (expiry_date IS NULL OR expiry_date > NOW())
            AND (usage_limit IS NULL OR used_count < usage_limit)
        ", [$code])->fetch();
        
        if(!$coupon) {
            echo json_encode(['success' => false, 'message' => 'Invalid or expired coupon']);
            exit;
        }
        
        // Store coupon in session
        $_SESSION['applied_coupon'] = $coupon;
        
        echo json_encode([
            'success' => true, 
            'message' => 'Coupon applied!',
            'discount' => $coupon['discount_type'] == 'percentage' ? 
                         $coupon['discount_value'] . '%' : 
                         'UGX ' . number_format($coupon['discount_value'])
        ]);
        break;
        
    case 'remove':
        unset($_SESSION['applied_coupon']);
        echo json_encode(['success' => true, 'message' => 'Coupon removed']);
        break;
        
    default:
        echo json_encode(['error' => 'Invalid action']);
}
?>