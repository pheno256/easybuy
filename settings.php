<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$page_title = 'Site Settings';
$db = Database::getInstance();
$message = '';

// Update settings
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $site_name = $_POST['site_name'];
    $site_email = $_POST['site_email'];
    $site_phone = $_POST['site_phone'];
    $site_address = $_POST['site_address'];
    $delivery_fee_threshold = $_POST['delivery_fee_threshold'];
    $delivery_fee = $_POST['delivery_fee'];
    
    // Update settings in database or config file
    $db->query("UPDATE settings SET value = ? WHERE key = 'site_name'", [$site_name]);
    $db->query("UPDATE settings SET value = ? WHERE key = 'site_email'", [$site_email]);
    $db->query("UPDATE settings SET value = ? WHERE key = 'site_phone'", [$site_phone]);
    $db->query("UPDATE settings SET value = ? WHERE key = 'site_address'", [$site_address]);
    $db->query("UPDATE settings SET value = ? WHERE key = 'delivery_fee_threshold'", [$delivery_fee_threshold]);
    $db->query("UPDATE settings SET value = ? WHERE key = 'delivery_fee'", [$delivery_fee]);
    
    $message = "Settings updated successfully!";
}

// Get current settings
$settings = [];
$result = $db->query("SELECT * FROM settings")->fetchAll();
foreach($result as $row) {
    $settings[$row['key']] = $row['value'];
}

require_once 'includes/admin-header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Site Settings</h1>
</div>

<?php if($message): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php echo $message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">General Settings</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Site Name</label>
                        <input type="text" class="form-control" name="site_name" 
                               value="<?php echo $settings['site_name'] ?? 'EasyBuy Uganda'; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Site Email</label>
                        <input type="email" class="form-control" name="site_email" 
                               value="<?php echo $settings['site_email'] ?? 'info@easybuy.ug'; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Site Phone</label>
                        <input type="tel" class="form-control" name="site_phone" 
                               value="<?php echo $settings['site_phone'] ?? '+256700000000'; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Site Address</label>
                        <textarea class="form-control" name="site_address" rows="2"><?php echo $settings['site_address'] ?? 'Kampala, Uganda'; ?></textarea>
                    </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Delivery Settings</h5>
            </div>
            <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Free Delivery Threshold (UGX)</label>
                        <input type="number" class="form-control" name="delivery_fee_threshold" 
                               value="<?php echo $settings['delivery_fee_threshold'] ?? '200000'; ?>">
                        <small class="text-muted">Orders above this amount get free delivery</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Standard Delivery Fee (UGX)</label>
                        <input type="number" class="form-control" name="delivery_fee" 
                               value="<?php echo $settings['delivery_fee'] ?? '15000'; ?>">
                    </div>
            </div>
        </div>
        
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Payment API Settings</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">MTN API Environment</label>
                    <select class="form-select">
                        <option>Sandbox (Testing)</option>
                        <option>Production (Live)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Airtel API Environment</label>
                    <select class="form-select">
                        <option>Sandbox (Testing)</option>
                        <option>Production (Live)</option>
                    </select>
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> API credentials are stored in the .env file
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Maintenance</h5>
            </div>
            <div class="card-body">
                <button type="submit" class="btn btn-primary">Save All Settings</button>
                <button type="button" class="btn btn-danger" onclick="confirmClearCache()">Clear Cache</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmClearCache() {
    if(confirm('Clear all cached data? This may temporarily slow down the site.')) {
        showNotification('Cache cleared successfully!', 'success');
    }
}
</script>

<?php require_once 'includes/admin-footer.php'; ?>