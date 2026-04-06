<?php
require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$page_title = 'Become a Vendor';
$db = Database::getInstance();
$success = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $business_name = $_POST['business_name'];
    $owner_name = $_POST['owner_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $business_type = $_POST['business_type'];
    $tin_number = $_POST['tin_number'];
    $description = $_POST['description'];
    
    $db->insert('vendor_applications', [
        'business_name' => $business_name,
        'owner_name' => $owner_name,
        'email' => $email,
        'phone' => $phone,
        'address' => $address,
        'business_type' => $business_type,
        'tin_number' => $tin_number,
        'description' => $description,
        'status' => 'pending',
        'submitted_at' => date('Y-m-d H:i:s')
    ]);
    
    $success = "Application submitted successfully! We'll contact you within 3-5 business days.";
}

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="text-center mb-5">
                <i class="fas fa-store fa-4x text-primary mb-3"></i>
                <h1>Sell on EasyBuy Uganda</h1>
                <p class="lead">Reach thousands of customers across Uganda</p>
            </div>
            
            <?php if($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <div class="text-center mt-4">
                <a href="index.php" class="btn btn-primary">Return to Homepage</a>
            </div>
            <?php else: ?>
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-4">Vendor Application Form</h5>
                    
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
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Business Address</label>
                                <input type="text" class="form-control" name="address" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Business Type</label>
                                <select class="form-select" name="business_type" required>
                                    <option value="">Select Business Type</option>
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
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Business Description</label>
                                <textarea class="form-control" name="description" rows="4" required></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Submit Application</button>
                    </form>
                </div>
            </div>
            
            <div class="row mt-5">
                <div class="col-md-4 mb-3">
                    <div class="card h-100 border-0 shadow-sm text-center">
                        <div class="card-body">
                            <i class="fas fa-users fa-3x text-primary mb-3"></i>
                            <h6>Reach Thousands</h6>
                            <p class="small">Connect with customers across all districts of Uganda</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card h-100 border-0 shadow-sm text-center">
                        <div class="card-body">
                            <i class="fas fa-mobile-alt fa-3x text-primary mb-3"></i>
                            <h6>Mobile Money Payments</h6>
                            <p class="small">Secure payments via MTN & Airtel Money</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card h-100 border-0 shadow-sm text-center">
                        <div class="card-body">
                            <i class="fas fa-truck fa-3x text-primary mb-3"></i>
                            <h6>Delivery Support</h6>
                            <p class="small">We help with logistics and delivery</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>