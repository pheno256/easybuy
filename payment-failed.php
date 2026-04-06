<?php
require_once '../app/config/config.php';
$page_title = 'Payment Failed';

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center">
            <div class="mb-4">
                <i class="fas fa-times-circle text-danger fa-5x"></i>
            </div>
            <h1 class="mb-3">Payment Failed</h1>
            <p class="lead">We were unable to process your payment.</p>
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h5>What went wrong?</h5>
                    <p class="text-muted">Possible reasons:</p>
                    <ul class="text-start">
                        <li>Insufficient funds in your mobile money account</li>
                        <li>Incorrect mobile money PIN entered</li>
                        <li>Network connectivity issues</li>
                        <li>Payment timeout</li>
                    </ul>
                    <hr>
                    <h5>What can you do?</h5>
                    <ul class="text-start">
                        <li>Check your mobile money balance</li>
                        <li>Try again with the same payment method</li>
                        <li>Try a different payment method</li>
                        <li>Contact your mobile network provider</li>
                    </ul>
                    <div class="mt-4">
                        <a href="checkout.php" class="btn btn-primary">
                            <i class="fas fa-redo"></i> Try Again
                        </a>
                        <a href="cart.php" class="btn btn-outline-secondary">
                            <i class="fas fa-shopping-cart"></i> Return to Cart
                        </a>
                        <a href="contact.php" class="btn btn-outline-info">
                            <i class="fas fa-headset"></i> Contact Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>