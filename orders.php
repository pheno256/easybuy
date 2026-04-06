<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$page_title = 'Manage Orders';
$db = Database::getInstance();
$message = '';

// Update order status
if(isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $order_status = $_POST['order_status'];
    $payment_status = $_POST['payment_status'];
    
    $db->query("UPDATE orders SET order_status = ?, payment_status = ? WHERE id = ?", 
               [$order_status, $payment_status, $order_id]);
    $message = "Order status updated!";
}

// Get all orders
$status_filter = $_GET['status'] ?? '';
$sql = "SELECT * FROM orders";
$params = [];
if($status_filter) {
    $sql .= " WHERE order_status = ?";
    $params[] = $status_filter;
}
$sql .= " ORDER BY created_at DESC";
$orders = $db->query($sql, $params)->fetchAll();

// Get order details if viewing single order
$view_order = null;
$order_items = [];
if(isset($_GET['view'])) {
    $view_order = $db->query("SELECT * FROM orders WHERE id = ?", [$_GET['view']])->fetch();
    if($view_order) {
        $order_items = $db->query("SELECT * FROM order_items WHERE order_id = ?", [$view_order['id']])->fetchAll();
    }
}

require_once 'includes/admin-header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Manage Orders</h1>
    <div>
        <a href="?status=pending" class="btn btn-outline-warning btn-sm">Pending</a>
        <a href="?status=processing" class="btn btn-outline-info btn-sm">Processing</a>
        <a href="?status=shipped" class="btn btn-outline-primary btn-sm">Shipped</a>
        <a href="?status=delivered" class="btn btn-outline-success btn-sm">Delivered</a>
        <a href="?status=cancelled" class="btn btn-outline-danger btn-sm">Cancelled</a>
        <a href="orders.php" class="btn btn-outline-secondary btn-sm">All</a>
    </div>
</div>

<?php if($message): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php echo $message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if($view_order): ?>
<!-- View Single Order -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">Order #<?php echo $view_order['order_number']; ?></h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6>Customer Information</h6>
                <p>
                    <strong>Name:</strong> <?php echo htmlspecialchars($view_order['full_name']); ?><br>
                    <strong>Email:</strong> <?php echo htmlspecialchars($view_order['email']); ?><br>
                    <strong>Phone:</strong> <?php echo htmlspecialchars($view_order['phone']); ?>
                </p>
                <h6>Shipping Address</h6>
                <p>
                    <?php echo htmlspecialchars($view_order['street_address']); ?><br>
                    <?php echo htmlspecialchars($view_order['city']); ?>, <?php echo htmlspecialchars($view_order['district']); ?>
                </p>
            </div>
            <div class="col-md-6">
                <h6>Order Information</h6>
                <p>
                    <strong>Order Date:</strong> <?php echo date('F d, Y H:i', strtotime($view_order['created_at'])); ?><br>
                    <strong>Total Amount:</strong> UGX <?php echo number_format($view_order['total_amount']); ?><br>
                    <strong>Payment Method:</strong> <?php echo strtoupper($view_order['payment_method']); ?> Money<br>
                    <strong>Payment Status:</strong> 
                    <span class="badge bg-<?php echo $view_order['payment_status'] == 'completed' ? 'success' : 'warning'; ?>">
                        <?php echo ucfirst($view_order['payment_status']); ?>
                    </span>
                </p>
            </div>
        </div>
        
        <h6 class="mt-4">Order Items</h6>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($order_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>UGX <?php echo number_format($item['price']); ?></td>
                        <td>UGX <?php echo number_format($item['price'] * $item['quantity']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <form method="POST" action="" class="mt-4">
            <input type="hidden" name="order_id" value="<?php echo $view_order['id']; ?>">
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Order Status</label>
                    <select class="form-select" name="order_status">
                        <option value="pending" <?php echo $view_order['order_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="processing" <?php echo $view_order['order_status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                        <option value="shipped" <?php echo $view_order['order_status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                        <option value="delivered" <?php echo $view_order['order_status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                        <option value="cancelled" <?php echo $view_order['order_status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Payment Status</label>
                    <select class="form-select" name="payment_status">
                        <option value="pending" <?php echo $view_order['payment_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="completed" <?php echo $view_order['payment_status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="failed" <?php echo $view_order['payment_status'] == 'failed' ? 'selected' : ''; ?>>Failed</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" name="update_status" class="btn btn-primary w-100">Update Status</button>
                </div>
            </div>
        </form>
        
        <div class="mt-4">
            <a href="orders.php" class="btn btn-secondary">Back to Orders</a>
            <button class="btn btn-info" onclick="window.print()"><i class="fas fa-print"></i> Print Invoice</button>
        </div>
    </div>
</div>
<?php else: ?>
<!-- Orders List -->
<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($orders as $order): ?>
                    <tr>
                        <td><?php echo $order['order_number']; ?></td>
                        <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                        <td>UGX <?php echo number_format($order['total_amount']); ?></td>
                        <td>
                            <span class="badge bg-<?php echo $order['payment_status'] == 'completed' ? 'success' : 'warning'; ?>">
                                <?php echo ucfirst($order['payment_status']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?php 
                                echo $order['order_status'] == 'delivered' ? 'success' : 
                                    ($order['order_status'] == 'cancelled' ? 'danger' : 
                                    ($order['order_status'] == 'shipped' ? 'info' : 'warning')); 
                            ?>">
                                <?php echo ucfirst($order['order_status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                        <td>
                            <a href="?view=<?php echo $order['id']; ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once 'includes/admin-footer.php'; ?>