<?php
require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$order_id = $_GET['id'] ?? 0;
$db = Database::getInstance();

$order = $db->query("
    SELECT * FROM orders 
    WHERE id = ? AND user_id = ?
", [$order_id, $_SESSION['user_id']])->fetch();

if(!$order) {
    header('Location: account.php');
    exit;
}

$order_items = $db->query("
    SELECT * FROM order_items 
    WHERE order_id = ?
", [$order_id])->fetchAll();

// Set headers for PDF download
header('Content-Type: text/html');
header('Content-Disposition: attachment; filename="invoice-' . $order['order_number'] . '.html"');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice #<?php echo $order['order_number']; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 50px;
            color: #333;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #2563eb;
        }
        .invoice-title {
            font-size: 28px;
            color: #2563eb;
            margin-bottom: 5px;
        }
        .invoice-number {
            font-size: 16px;
            color: #666;
        }
        .company-info {
            text-align: center;
            margin-bottom: 30px;
        }
        .customer-info {
            margin-bottom: 30px;
            padding: 15px;
            background: #f3f4f6;
            border-radius: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background: #f9fafb;
            font-weight: 600;
        }
        .totals {
            text-align: right;
            margin-top: 20px;
        }
        .totals table {
            width: 300px;
            float: right;
        }
        .footer {
            text-align: center;
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #666;
        }
        @media print {
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-header">
        <div class="invoice-title">EasyBuy Uganda</div>
        <div class="invoice-number">Invoice #: <?php echo $order['order_number']; ?></div>
        <div>Date: <?php echo date('F d, Y', strtotime($order['created_at'])); ?></div>
    </div>
    
    <div class="company-info">
        <strong>EasyBuy Uganda</strong><br>
        Kampala Road, Kampala, Uganda<br>
        Phone: +256 700 000 000 | Email: info@easybuy.ug<br>
        Website: www.easybuy.ug
    </div>
    
    <div class="customer-info">
        <strong>Bill To:</strong><br>
        <?php echo htmlspecialchars($order['full_name']); ?><br>
        <?php echo htmlspecialchars($order['email']); ?><br>
        <?php echo htmlspecialchars($order['phone']); ?><br>
        <?php echo htmlspecialchars($order['street_address']); ?><br>
        <?php echo htmlspecialchars($order['city']); ?>, <?php echo htmlspecialchars($order['district']); ?>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php $counter = 1; foreach($order_items as $item): ?>
            <tr>
                <td><?php echo $counter++; ?></td>
                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td>UGX <?php echo number_format($item['price']); ?></td>
                <td>UGX <?php echo number_format($item['price'] * $item['quantity']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="totals">
        <table>
            <tr>
                <td>Subtotal:</td>
                <td><strong>UGX <?php echo number_format($order['total_amount'] - ($order['total_amount'] > 200000 ? 0 : 15000)); ?></strong></td>
            </tr>
            <tr>
                <td>Delivery Fee:</td>
                <td><strong>UGX <?php echo number_format($order['total_amount'] > 200000 ? 0 : 15000); ?></strong></td>
            </tr>
            <tr style="border-top: 2px solid #333;">
                <td><strong>Total:</strong></td>
                <td><strong>UGX <?php echo number_format($order['total_amount']); ?></strong></td>
            </tr>
        </table>
    </div>
    
    <div style="clear: both;"></div>
    
    <div class="footer">
        <p>Thank you for shopping with EasyBuy Uganda!</p>
        <p>For any inquiries, please contact our customer support at +256 700 000 000 or support@easybuy.ug</p>
        <p>Payment Method: <?php echo strtoupper($order['payment_method']); ?> Money</p>
        <p>Payment Status: <?php echo ucfirst($order['payment_status']); ?></p>
    </div>
</body>
</html>