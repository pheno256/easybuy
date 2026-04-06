<?php
session_start();
require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

if(!isset($_SESSION['vendor_id'])) {
    header('Location: login.php');
    exit;
}

$page_title = 'Vendor Dashboard';
$db = Database::getInstance();

$vendor_id = $_SESSION['vendor_id'];
$vendor = $db->query("SELECT * FROM vendors WHERE id = ?", [$vendor_id])->fetch();

// Get vendor stats
$total_products = $db->query("SELECT COUNT(*) as count FROM products WHERE vendor_id = ?", [$vendor_id])->fetch()['count'];
$total_orders = $db->query("
    SELECT COUNT(*) as count FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    WHERE p.vendor_id = ?
", [$vendor_id])->fetch()['count'];
$total_revenue = $db->query("
    SELECT SUM(oi.price * oi.quantity) as total FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    WHERE p.vendor_id = ? AND oi.order_id IN (SELECT id FROM orders WHERE payment_status = 'completed')
", [$vendor_id])->fetch()['total'];

require_once 'includes/vendor-header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h1 class="h3 mb-4">Welcome, <?php echo htmlspecialchars($vendor['business_name']); ?></h1>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
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
            <div class="card bg-success text-white">
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
            <div class="card bg-info text-white">
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
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Pending Orders</h6>
                            <h2 class="mb-0">0</h2>
                        </div>
                        <i class="fas fa-clock fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
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
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No orders yet</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="products.php?action=add" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Product
                        </a>
                        <a href="products.php" class="btn btn-outline-primary">
                            <i class="fas fa-box"></i> Manage Products
                        </a>
                        <a href="orders.php" class="btn btn-outline-primary">
                            <i class="fas fa-shopping-cart"></i> View Orders
                        </a>
                        <a href="settings.php" class="btn btn-outline-secondary">
                            <i class="fas fa-cog"></i> Store Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/vendor-footer.php'; ?>