<?php
require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$page_title = 'My Account';
$db = Database::getInstance();

// Get user details
$user = $db->query("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']])->fetch();

// Get orders
$orders = $db->query("
    SELECT * FROM orders 
    WHERE user_id = ? 
    ORDER BY created_at DESC 
    LIMIT 10
", [$_SESSION['user_id']])->fetchAll();

// Update profile
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $district = $_POST['district'];
    $city = $_POST['city'];
    $street_address = $_POST['street_address'];
    
    $db->query("
        UPDATE users 
        SET full_name = ?, phone = ?, district = ?, city = ?, street_address = ? 
        WHERE id = ?
    ", [$full_name, $phone, $district, $city, $street_address, $_SESSION['user_id']]);
    
    $_SESSION['user_name'] = $full_name;
    $success = "Profile updated successfully!";
    $user = $db->query("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']])->fetch();
}

// Change password
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verify current password
    $check = $db->query("SELECT password FROM users WHERE id = ?", [$_SESSION['user_id']])->fetch();
    
    if(password_verify($current_password, $check['password'])) {
        if($new_password === $confirm_password && strlen($new_password) >= 6) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $db->query("UPDATE users SET password = ? WHERE id = ?", [$hashed_password, $_SESSION['user_id']]);
            $password_success = "Password changed successfully!";
        } else {
            $password_error = "Passwords do not match or are too short";
        }
    } else {
        $password_error = "Current password is incorrect";
    }
}

// Get Uganda districts
$districts = $db->query("SELECT * FROM uganda_districts ORDER BY district_name")->fetchAll();

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-md-3">
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <i class="fas fa-user-circle fa-4x text-primary mb-3"></i>
                    <h5><?php echo htmlspecialchars($user['full_name']); ?></h5>
                    <p class="text-muted small"><?php echo htmlspecialchars($user['email']); ?></p>
                    <hr>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary btn-sm" onclick="showTab('profile')">
                            <i class="fas fa-user"></i> Profile
                        </button>
                        <button class="btn btn-outline-primary btn-sm" onclick="showTab('orders')">
                            <i class="fas fa-shopping-bag"></i> My Orders
                        </button>
                        <button class="btn btn-outline-primary btn-sm" onclick="showTab('security')">
                            <i class="fas fa-shield-alt"></i> Security
                        </button>
                        <a href="wishlist.php" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-heart"></i> Wishlist
                        </a>
                        <a href="track-order.php" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-truck"></i> Track Order
                        </a>
                        <hr>
                        <a href="logout.php" class="btn btn-danger btn-sm">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <!-- Profile Tab -->
            <div id="profile-tab" class="tab-content">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Profile Information</h5>
                    </div>
                    <div class="card-body">
                        <?php if(isset($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" class="form-control" name="full_name" 
                                           value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" name="phone" 
                                           value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">District</label>
                                    <select class="form-select" name="district">
                                        <option value="">Select District</option>
                                        <?php foreach($districts as $district): ?>
                                        <option value="<?php echo $district['district_name']; ?>"
                                            <?php echo $user['district'] == $district['district_name'] ? 'selected' : ''; ?>>
                                            <?php echo $district['district_name']; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">City/Town</label>
                                    <input type="text" class="form-control" name="city" 
                                           value="<?php echo htmlspecialchars($user['city']); ?>">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Street Address</label>
                                    <textarea class="form-control" name="street_address" rows="2"><?php echo htmlspecialchars($user['street_address']); ?></textarea>
                                </div>
                            </div>
                            <button type="submit" name="update_profile" class="btn btn-primary">
                                Update Profile
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Orders Tab -->
            <div id="orders-tab" class="tab-content" style="display: none;">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">My Orders</h5>
                    </div>
                    <div class="card-body">
                        <?php if(empty($orders)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
                            <h5>No orders yet</h5>
                            <p>Start shopping to see your orders here</p>
                            <a href="shop.php" class="btn btn-primary">Start Shopping</a>
                        </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($orders as $order): ?>
                                    <tr>
                                        <td><?php echo $order['order_number']; ?></td>
                                        <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                        <td>UGX <?php echo number_format($order['total_amount']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $order['order_status'] == 'delivered' ? 'success' : 
                                                    ($order['order_status'] == 'cancelled' ? 'danger' : 'warning'); 
                                            ?>">
                                                <?php echo ucfirst($order['order_status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $order['payment_status'] == 'completed' ? 'success' : 'warning'; 
                                            ?>">
                                                <?php echo ucfirst($order['payment_status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-info">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Security Tab -->
            <div id="security-tab" class="tab-content" style="display: none;">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Change Password</h5>
                    </div>
                    <div class="card-body">
                        <?php if(isset($password_success)): ?>
                        <div class="alert alert-success"><?php echo $password_success; ?></div>
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
                            <button type="submit" name="change_password" class="btn btn-primary">
                                Change Password
                            </button>
                        </form>
                        
                        <hr>
                        
                        <h6>Two-Factor Authentication</h6>
                        <p class="text-muted small">Enhance your account security with 2FA</p>
                        <button class="btn btn-outline-primary btn-sm" onclick="showNotification('Coming soon!', 'info')">
                            Enable 2FA
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showTab(tab) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(el => {
        el.style.display = 'none';
    });
    
    // Show selected tab
    document.getElementById(`${tab}-tab`).style.display = 'block';
}

// Show profile tab by default
showTab('profile');
</script>

<?php require_once 'includes/footer.php'; ?>