<?php
require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$page_title = 'Vendor Registration';
$db = Database::getInstance();
$success = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $business_name = $_POST['business_name'];
    $owner_name = $_POST['owner_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $address = $_POST['address'];
    $business_type = $_POST['business_type'];
    $tin_number = $_POST['tin_number'];
    
    // Validation
    if($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif(strlen($password) < 6) {
        $error = "Password must be at least 6 characters";
    } else {
        // Check if email exists
        $existing = $db->query("SELECT id FROM vendors WHERE email = ?", [$email])->fetch();
        if($existing) {
            $error = "Email already registered";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $db->insert('vendors', [
                'business_name' => $business_name,
                'owner_name' => $owner_name,
                'email' => $email,
                'phone' => $phone,
                'password' => $hashed_password,
                'address' => $address,
                'business_type' => $business_type,
                'tin_number' => $tin_number,
                'status' => 'pending'
            ]);
            $success = "Registration successful! Your application is pending approval. We'll contact you within 3-5 business days.";
        }
    }
}

require_once 'includes/vendor-header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center py-3">
                    <i class="fas fa-store fa-2x mb-2"></i>
                    <h4 class="mb-0">Vendor Registration</h4>
                    <p class="mb-0">Start selling on EasyBuy Uganda</p>
                </div>
                <div class="card-body p-4">
                    <?php if($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                    <div class="text-center mt-3">
                        <a href="login.php" class="btn btn-primary">Go to Login</a>
                    </div>
                    <?php else: ?>
                    <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Business Name</label>
                                <input type="text" class="form-control" name="business_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Owner Name</label>
                                <input type="text" class="form-control" name="owner_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" name="phone" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" name="confirm_password" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Business Address</label>
                                <textarea class="form-control" name="address" rows="2" required></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Business Type</label>
                                <select class="form-select" name="business_type" required>
                                    <option value="">Select Type</option>
                                    <option value="retail">Retail</option>
                                    <option value="wholesale">Wholesale</option>
                                    <option value="manufacturer">Manufacturer</option>
                                    <option value="distributor">Distributor</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">TIN Number (Optional)</label>
                                <input type="text" class="form-control" name="tin_number">
                            </div>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="../terms.php" target="_blank">Terms & Conditions</a>
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Register as Vendor</button>
                    </form>
                    <hr>
                    <div class="text-center">
                        Already have a vendor account? <a href="login.php">Login here</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/vendor-footer.php'; ?>