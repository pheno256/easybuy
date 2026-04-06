<?php
session_start();
if(!isset($_SESSION['vendor_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$page_title = 'Store Settings';
$db = Database::getInstance();
$vendor_id = $_SESSION['vendor_id'];
$message = '';

$vendor = $db->query("SELECT * FROM vendors WHERE id = ?", [$vendor_id])->fetch();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $business_name = $_POST['business_name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $description = $_POST['description'];
    
    $db->query("
        UPDATE vendors 
        SET business_name = ?, phone = ?, address = ?, description = ? 
        WHERE id = ?
    ", [$business_name, $phone, $address, $description, $vendor_id]);
    
    $message = "Settings updated successfully!";
    $vendor = $db->query("SELECT * FROM vendors WHERE id = ?", [$vendor_id])->fetch();
}

// Change password
if(isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];
    
    if(password_verify($current, $vendor['password'])) {
        if($new === $confirm && strlen($new) >= 6) {
            $hashed = password_hash($new, PASSWORD_DEFAULT);
            $db->query("UPDATE vendors SET password = ? WHERE id = ?", [$hashed, $vendor_id]);
            $password_message = "Password changed successfully!";
        } else {
            $password_error = "Passwords do not match or are too short";
        }
    } else {
        $password_error = "Current password is incorrect";
    }
}

require_once 'includes/vendor-header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Store Settings</h1>
</div>

<?php if($message): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php echo $message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Store Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Business Name</label>
                        <input type="text" class="form-control" name="business_name" 
                               value="<?php echo htmlspecialchars($vendor['business_name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($vendor['email']); ?>" disabled>
                        <small class="text-muted">Email cannot be changed</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" name="phone" 
                               value="<?php echo htmlspecialchars($vendor['phone']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Business Address</label>
                        <textarea class="form-control" name="address" rows="2"><?php echo htmlspecialchars($vendor['address']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Store Description</label>
                        <textarea class="form-control" name="description" rows="3"><?php echo htmlspecialchars($vendor['description'] ?? ''); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Security Settings</h5>
            </div>
            <div class="card-body">
                <?php if(isset($password_message)): ?>
                <div class="alert alert-success"><?php echo $password_message; ?></div>
                <?php endif; ?>
                <?php if(isset($password_error)): ?>
                <div class="alert alert-danger"><?php echo $password_error; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" class="form-control" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" class="form-control" name="new_password" required>
                        <small class="text-muted">Minimum 6 characters</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" name="confirm_password" required>
                    </div>
                    <button type="submit" name="change_password" class="btn btn-warning">Change Password</button>
                </form>
            </div>
        </div>
        
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Payout Information</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Your payouts are sent to your registered mobile money number.
                </div>
                <p><strong>Mobile Money Number:</strong> <?php echo htmlspecialchars($vendor['phone']); ?></p>
                <p><strong>Commission Rate:</strong> <?php echo $vendor['commission_rate'] ?? '10'; ?>%</p>
                <p><strong>Available Balance:</strong> <strong class="text-success">UGX <?php echo number_format($vendor['balance'] ?? 0); ?></strong></p>
                <button class="btn btn-success w-100" onclick="requestPayout()">
                    <i class="fas fa-money-bill-wave"></i> Request Payout
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function requestPayout() {
    Swal.fire({
        title: 'Request Payout',
        text: 'Are you sure you want to request a payout?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, request'
    }).then((result) => {
        if(result.isConfirmed) {
            showNotification('Payout request submitted! We\'ll process within 3-5 business days.', 'success');
        }
    });
}
</script>

<?php require_once 'includes/vendor-footer.php'; ?>