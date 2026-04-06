<?php
require_once '../app/config/config.php';
$page_title = 'About Us';

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-6 mb-4">
            <h1 class="display-4 mb-4">About EasyBuy Uganda</h1>
            <p class="lead">Your trusted online shopping destination in Uganda since 2024.</p>
            <p>EasyBuy Uganda was founded with a simple mission: to make quality products accessible to everyone in Uganda through a seamless online shopping experience.</p>
            <p>We understand the unique needs of Ugandan shoppers, which is why we offer flexible payment options including MTN Mobile Money and Airtel Money, fast delivery across all districts, and exceptional customer service.</p>
            <h5 class="mt-4">Our Values</h5>
            <ul class="list-unstyled">
                <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i> Quality Products at Best Prices</li>
                <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i> Fast & Reliable Delivery</li>
                <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i> Secure Mobile Money Payments</li>
                <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i> 24/7 Customer Support</li>
                <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i> Easy Returns & Refunds</li>
            </ul>
        </div>
        <div class="col-lg-6">
            <img src="assets/images/about-us.jpg" alt="About EasyBuy" class="img-fluid rounded-4 shadow-sm">
        </div>
    </div>
    
    <div class="row mt-5 pt-4">
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <i class="fas fa-shopping-bag fa-3x text-primary mb-3"></i>
                    <h3>10,000+</h3>
                    <p class="text-muted">Happy Customers</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <i class="fas fa-box-open fa-3x text-primary mb-3"></i>
                    <h3>5,000+</h3>
                    <p class="text-muted">Products Sold</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <i class="fas fa-truck fa-3x text-primary mb-3"></i>
                    <h3>100+</h3>
                    <p class="text-muted">Cities Covered</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>