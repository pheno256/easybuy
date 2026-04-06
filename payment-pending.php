<?php
require_once '../app/config/config.php';
$page_title = 'Payment Pending';

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center">
            <div class="mb-4">
                <i class="fas fa-clock text-warning fa-5x"></i>
            </div>
            <h1 class="mb-3">Payment Pending</h1>
            <p class="lead">Your payment is being processed.</p>
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <i class="fas fa-spinner fa-spin fa-3x text-primary mb-3"></i>
                    <h5>Please wait...</h5>
                    <p>We are waiting for confirmation from your mobile money provider.</p>
                    <p class="text-muted small">This usually takes a few minutes.</p>
                    <hr>
                    <p>You will receive an SMS and email confirmation once your payment is successful.</p>
                    <div class="mt-4">
                        <a href="account.php" class="btn btn-primary">
                            <i class="fas fa-user"></i> Go to My Account
                        </a>
                        <button class="btn btn-outline-secondary" onclick="location.reload()">
                            <i class="fas fa-sync-alt"></i> Check Status
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-refresh every 10 seconds for 2 minutes
let attempts = 0;
const maxAttempts = 12;
const interval = setInterval(function() {
    attempts++;
    if(attempts >= maxAttempts) {
        clearInterval(interval);
    }
    location.reload();
}, 10000);
</script>

<?php require_once 'includes/footer.php'; ?>