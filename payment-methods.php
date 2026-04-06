<?php
require_once '../app/config/config.php';
$page_title = 'Payment Methods';

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <h1 class="mb-4">Payment Methods</h1>
            
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5>Accepted Payment Methods</h5>
                    <p>EasyBuy Uganda accepts the following payment methods for secure and convenient shopping:</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-2">
                                <div class="card-body text-center">
                                    <img src="assets/images/mtn-momo.png" alt="MTN Mobile Money" style="height: 60px;" class="mb-3">
                                    <h5>MTN Mobile Money</h5>
                                    <p>Pay directly from your MTN MoMo account</p>
                                    <ul class="text-start">
                                        <li>Instant payment confirmation</li>
                                        <li>No additional fees</li>
                                        <li>Secure and encrypted</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-2">
                                <div class="card-body text-center">
                                    <img src="assets/images/airtel-money.png" alt="Airtel Money" style="height: 60px;" class="mb-3">
                                    <h5>Airtel Money</h5>
                                    <p>Pay using your Airtel Money wallet</p>
                                    <ul class="text-start">
                                        <li>Instant payment confirmation</li>
                                        <li>No additional fees</li>
                                        <li>Secure and encrypted</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5>How to Pay with Mobile Money</h5>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h6>MTN Mobile Money</h6>
                            <ol>
                                <li>Select MTN Mobile Money at checkout</li>
                                <li>Enter your MTN phone number</li>
                                <li>You'll receive a payment request on your phone</li>
                                <li>Enter your MoMo PIN to authorize payment</li>
                                <li>Payment confirmed instantly</li>
                            </ol>
                        </div>
                        <div class="col-md-6">
                            <h6>Airtel Money</h6>
                            <ol>
                                <li>Select Airtel Money at checkout</li>
                                <li>Enter your Airtel phone number</li>
                                <li>You'll receive a payment request on your phone</li>
                                <li>Enter your Airtel Money PIN to authorize</li>
                                <li>Payment confirmed instantly</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5>Payment Security</h5>
                    <p>Your payment security is our priority:</p>
                    <ul>
                        <li><i class="fas fa-lock text-success me-2"></i> 256-bit SSL encryption</li>
                        <li><i class="fas fa-shield-alt text-success me-2"></i> PCI DSS compliant</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i> No card details stored on our servers</li>
                        <li><i class="fas fa-mobile-alt text-success me-2"></i> PIN verification for all transactions</li>
                    </ul>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5>Payment FAQs</h5>
                    <div class="accordion" id="paymentFaq">
                        <div class="accordion-item">
                            <h6 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#pay1">
                                    Is it safe to pay with mobile money?
                                </button>
                            </h6>
                            <div id="pay1" class="accordion-collapse collapse show">
                                <div class="accordion-body">
                                    Yes! All mobile money transactions are encrypted and require your PIN for authorization. We never store your payment information.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h6 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#pay2">
                                    What if my payment fails?
                                </button>
                            </h6>
                            <div id="pay2" class="accordion-collapse collapse">
                                <div class="accordion-body">
                                    If payment fails, you can try again. Check your mobile money balance and ensure you have sufficient funds. Contact your mobile network provider if the issue persists.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h6 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#pay3">
                                    Will I get a receipt?
                                </button>
                            </h6>
                            <div id="pay3" class="accordion-collapse collapse">
                                <div class="accordion-body">
                                    Yes, you'll receive an SMS confirmation from your mobile network and an email receipt from EasyBuy Uganda.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-headset fa-3x text-primary mb-3"></i>
                    <h5>Payment Support</h5>
                    <p>Having trouble with payment? Our support team is here to help!</p>
                    <a href="contact.php" class="btn btn-primary">Contact Support</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>