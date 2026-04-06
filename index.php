<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$page_title = 'Admin Dashboard';
$db = Database::getInstance();

// Get stats
$total_orders = $db->query("SELECT COUNT(*) as count FROM orders")->fetch()['count'];
$total_users = $db->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'")->fetch()['count'];
$total_products = $db->query("SELECT COUNT(*) as count FROM products")->fetch()['count'];
$total_revenue = $db->query("SELECT SUM(total_amount) as total FROM orders WHERE payment_status = 'completed'")->fetch()['total'];

// Get recent orders
$recent_orders = $db->query("
    SELECT * FROM orders 
    ORDER BY created_at DESC 
    LIMIT 10
")->fetchAll();

// Get low stock products
$low_stock = $db->query("
    SELECT * FROM products 
    WHERE stock < 10 
    ORDER BY stock ASC 
    LIMIT 5
")->fetchAll();

require_once 'includes/admin-header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h1 class="h3 mb-4">Dashboard</h1>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Total Orders</h6>
                            <h2 class="mb-0"><?php echo number_format($total_orders); ?></h2>
                        </div>
                        <i class="fas fa-shopping-cart fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Total Users</h6>
                            <h2 class="mb-0"><?php echo number_format($total_users); ?></h2>
                        </div>
                        <i class="fas fa-users fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Total Products</h6>
                            <h2 class="mb-0"><?php echo number_format($total_products); ?></h2>
                        </div>
                        <i class="fas fa-box fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Total Revenue</h6>
                            <h2 class="mb-0">UGX <?php echo number_format($total_revenue ?? 0); ?></h2>
                        </div>
                        <i class="fas fa-chart-line fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Recent Orders -->
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Recent Orders</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($recent_orders as $order): ?>
                                <tr>
                                    <td><?php echo $order['order_number']; ?></td>
                                    <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                                    <td>UGX <?php echo number_format($order['total_amount']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $order['order_status'] == 'delivered' ? 'success' : 
                                                ($order['order_status'] == 'cancelled' ? 'danger' : 'warning'); 
                                        ?>">
                                            <?php echo ucfirst($order['order_status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <a href="orders.php?view=<?php echo $order['id']; ?>" class="btn btn-sm btn-info">
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
        </div>
        
        <!-- Low Stock Alerts -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Low Stock Alerts</h5>
                </div>
                <div class="card-body">
                    <?php if(empty($low_stock)): ?>
                    <p class="text-success">All products have sufficient stock!</p>
                    <?php else: ?>
                    <div class="list-group">
                        <?php foreach($low_stock as $product): ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-0"><?php echo htmlspecialchars($product['name']); ?></h6>
                                    <small class="text-danger">Stock: <?php echo $product['stock']; ?> left</small>
                                </div>
                                <a href="products.php?edit=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">
                                    Update
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/admin-footer.php'; ?>