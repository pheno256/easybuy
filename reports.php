<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$page_title = 'System Reports';
$db = Database::getInstance();

// Get date range
$end_date = date('Y-m-d');
$start_date = date('Y-m-d', strtotime('-30 days'));

if(isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
}

// Sales data
$sales_data = $db->query("
    SELECT 
        DATE(created_at) as date,
        COUNT(*) as order_count,
        SUM(total_amount) as total_sales
    FROM orders 
    WHERE DATE(created_at) BETWEEN ? AND ? 
    AND payment_status = 'completed'
    GROUP BY DATE(created_at)
    ORDER BY date ASC
", [$start_date, $end_date])->fetchAll();

// Top selling products
$top_products = $db->query("
    SELECT 
        p.name,
        p.image,
        SUM(oi.quantity) as total_quantity,
        SUM(oi.price * oi.quantity) as total_revenue
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    JOIN orders o ON oi.order_id = o.id
    WHERE o.payment_status = 'completed'
    GROUP BY oi.product_id
    ORDER BY total_quantity DESC
    LIMIT 10
")->fetchAll();

// Sales by payment method
$payment_methods = $db->query("
    SELECT 
        payment_method,
        COUNT(*) as order_count,
        SUM(total_amount) as total_sales
    FROM orders 
    WHERE payment_status = 'completed'
    GROUP BY payment_method
")->fetchAll();

// Sales by district
$sales_by_district = $db->query("
    SELECT 
        district,
        COUNT(*) as order_count,
        SUM(total_amount) as total_sales
    FROM orders 
    WHERE payment_status = 'completed'
    GROUP BY district
    ORDER BY total_sales DESC
    LIMIT 10
")->fetchAll();

// Monthly summary
$monthly_summary = $db->query("
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as month,
        COUNT(*) as order_count,
        SUM(total_amount) as total_sales
    FROM orders 
    WHERE payment_status = 'completed'
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month DESC
    LIMIT 12
")->fetchAll();

require_once 'includes/admin-header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">System Reports</h1>
    <form method="GET" action="" class="d-flex gap-2">
        <input type="date" class="form-control" name="start_date" value="<?php echo $start_date; ?>">
        <input type="date" class="form-control" name="end_date" value="<?php echo $end_date; ?>">
        <button type="submit" class="btn btn-primary">Filter</button>
        <button type="button" class="btn btn-success" onclick="exportReport()">
            <i class="fas fa-download"></i> Export
        </button>
    </form>
</div>

<!-- Sales Overview -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Sales Overview</h5>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Payment Methods</h5>
            </div>
            <div class="card-body">
                <canvas id="paymentChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Top Selling Products -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">Top Selling Products</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity Sold</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($top_products as $product): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <?php if($product['image']): ?>
                                <img src="../assets/images/products/<?php echo $product['image']; ?>" 
                                     style="width: 40px; height: 40px; object-fit: cover;" class="rounded me-2">
                                <?php endif; ?>
                                <?php echo htmlspecialchars($product['name']); ?>
                            </div>
                        </td>
                        <td><?php echo $product['total_quantity']; ?></td>
                        <td>UGX <?php echo number_format($product['total_revenue']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Sales by District -->
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Top Districts by Sales</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>District</th>
                                <th>Orders</th>
                                <th>Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($sales_by_district as $district): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($district['district']); ?></td>
                                <td><?php echo $district['order_count']; ?></td>
                                <td>UGX <?php echo number_format($district['total_sales']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Monthly Summary</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Orders</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($monthly_summary as $month): ?>
                            <tr>
                                <td><?php echo date('F Y', strtotime($month['month'] . '-01')); ?></td>
                                <td><?php echo $month['order_count']; ?></td>
                                <td>UGX <?php echo number_format($month['total_sales']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Sales Chart
const salesCtx = document.getElementById('salesChart').getContext('2d');
new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_column($sales_data, 'date')); ?>,
        datasets: [{
            label: 'Sales (UGX)',
            data: <?php echo json_encode(array_column($sales_data, 'total_sales')); ?>,
            borderColor: '#2563eb',
            backgroundColor: 'rgba(37, 99, 235, 0.1)',
            tension: 0.4,
            fill: true
        }, {
            label: 'Orders',
            data: <?php echo json_encode(array_column($sales_data, 'order_count')); ?>,
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true
    }
});

// Payment Methods Chart
const paymentCtx = document.getElementById('paymentChart').getContext('2d');
new Chart(paymentCtx, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode(array_column($payment_methods, 'payment_method')); ?>,
        datasets: [{
            data: <?php echo json_encode(array_column($payment_methods, 'total_sales')); ?>,
            backgroundColor: ['#2563eb', '#f59e0b']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true
    }
});

function exportReport() {
    const startDate = document.querySelector('input[name="start_date"]').value;
    const endDate = document.querySelector('input[name="end_date"]').value;
    window.location.href = `export-report.php?start_date=${startDate}&end_date=${endDate}`;
}
</script>

<?php require_once 'includes/admin-footer.php'; ?>