<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$db = Database::getInstance();

$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Get orders data
$orders = $db->query("
    SELECT 
        order_number,
        full_name,
        email,
        phone,
        district,
        city,
        total_amount,
        payment_method,
        payment_status,
        order_status,
        created_at
    FROM orders 
    WHERE DATE(created_at) BETWEEN ? AND ?
    ORDER BY created_at DESC
", [$start_date, $end_date])->fetchAll();

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="report_' . $start_date . '_to_' . $end_date . '.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Order #', 'Customer', 'Email', 'Phone', 'District', 'City', 'Amount', 'Payment', 'Payment Status', 'Order Status', 'Date']);

foreach($orders as $order) {
    fputcsv($output, [
        $order['order_number'],
        $order['full_name'],
        $order['email'],
        $order['phone'],
        $order['district'],
        $order['city'],
        $order['total_amount'],
        $order['payment_method'],
        $order['payment_status'],
        $order['order_status'],
        $order['created_at']
    ]);
}

fclose($output);
exit;
?>