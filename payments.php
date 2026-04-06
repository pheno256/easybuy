<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$page_title = 'Manage Payments';
$db = Database::getInstance();

// Get all payments
$payments = $db->query("
    SELECT p.*, o.order_number, o.full_name, o.email 
    FROM payments p 
    JOIN orders o ON p.order_id = o.id 
    ORDER BY p.created_at DESC
")->fetchAll();

require_once 'includes/admin-header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Payment Transactions</h1>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($payments as $payment): ?>
                    <tr>
                        <td><code><?php echo $payment['transaction_id'] ?? 'N/A'; ?></code></td>
                        <td><?php echo $payment['order_number']; ?></td>
                        <td>
                            <?php echo htmlspecialchars($payment['full_name']); ?><br>
                            <small><?php echo $payment['email']; ?></small>
                        </td>
                        <td>UGX <?php echo number_format($payment['amount']); ?></td>
                        <td>
                            <span class="badge bg-<?php echo $payment['payment_method'] == 'mtn' ? 'yellow' : 'red'; ?>">
                                <?php echo strtoupper($payment['payment_method']); ?>
                            </span>
                        </td>
                        <td><?php echo $payment['phone_number'] ?? 'N/A'; ?></td>
                        <td>
                            <span class="badge bg-<?php 
                                echo $payment['status'] == 'success' ? 'success' : 
                                    ($payment['status'] == 'failed' ? 'danger' : 'warning'); 
                            ?>">
                                <?php echo ucfirst($payment['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y H:i', strtotime($payment['created_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/admin-footer.php'; ?>