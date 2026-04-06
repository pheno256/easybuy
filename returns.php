<?php
require_once '../app/config/config.php';
$page_title = 'Returns Policy';

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="mb-4">Returns & Refunds Policy</h1>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> We want you to be completely satisfied with your purchase. If something isn't right, we're here to help!
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Return Eligibility</h5>
                </div>
                <div class="card-body">
                    <h6>You can return an item within <strong>7 days</strong> of delivery if:</h6>
                    <ul>
                        <li><i class="fas fa-check-circle text-success me-2"></i> The product is damaged or defective</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i> Wrong item was delivered</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i> Missing parts or accessories</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i> You changed your mind (item must be unused, unopened, in original packaging)</li>
                    </ul>
                    
                    <h6 class="mt-3">Items that cannot be returned:</h6>
                    <ul>
                        <li><i class="fas fa-times-circle text-danger me-2"></i> Perishable goods (food, flowers)</li>
                        <li><i class="fas fa-times-circle text-danger me-2"></i> Personal care items (cosmetics, underwear)</li>
                        <li><i class="fas fa-times-circle text-danger me-2"></i> Digital products (software, e-books)</li>
                        <li><i class="fas fa-times-circle text-danger me-2"></i> Customized or personalized items</li>
                        <li><i class="fas fa-times-circle text-danger me-2"></i> Items with broken seals (for hygiene products)</li>
                    </ul>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">How to Initiate a Return</h5>
                </div>
                <div class="card-body">
                    <div class="step-item mb-4">
                        <div class="d-flex">
                            <div class="step-number me-3">1</div>
                            <div>
                                <h6>Contact Customer Support</h6>
                                <p>Call us at +256 700 000 000 or email returns@easybuy.ug within 7 days of delivery. Provide your order number and reason for return.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="step-item mb-4">
                        <div class="d-flex">
                            <div class="step-number me-3">2</div>
                            <div>
                                <h6>Provide Documentation</h6>
                                <p>Upload photos or videos showing the issue (for damaged/defective items). Our team will review and approve your return within 24 hours.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="step-item mb-4">
                        <div class="d-flex">
                            <div class="step-number me-3">3</div>
                            <div>
                                <h6>Package the Item</h6>
                                <p>Pack the item securely in its original packaging, including all accessories, manuals, and tags. Write your order number on the package.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="step-item mb-4">
                        <div class="d-flex">
                            <div class="step-number me-3">4</div>
                            <div>
                                <h6>Return Shipping</h6>
                                <p>For defective or wrong items, we will arrange free pickup. For change-of-mind returns, you will be responsible for return shipping costs.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="step-item">
                        <div class="d-flex">
                            <div class="step-number me-3">5</div>
                            <div>
                                <h6>Inspection & Refund</h6>
                                <p>Once we receive and inspect the item, we will process your refund within 3-5 business days.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Refund Process</h5>
                </div>
                <div class="card-body">
                    <h6>Refund timelines:</h6>
                    <ul>
                        <li><strong>Mobile Money (MTN/Airtel):</strong> 1-3 business days after approval</li>
                        <li><strong>Store Credit:</strong> Instant after approval</li>
                        <li><strong>Bank Transfer:</strong> 5-7 business days</li>
                    </ul>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-clock"></i> <strong>Note:</strong> Refunds are issued to your original payment method. For mobile money, ensure your phone is active to receive the refund.
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Exchange Policy</h5>
                </div>
                <div class="card-body">
                    <p>If you prefer an exchange instead of a refund:</p>
                    <ul>
                        <li>Follow the same return process</li>
                        <li>Specify the replacement item you want</li>
                        <li>If the replacement item costs more, pay the difference</li>
                        <li>If it costs less, we'll refund the difference</li>
                        <li>Exchange shipping is free for defective items</li>
                    </ul>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Return Address</h5>
                </div>
                <div class="card-body">
                    <p>For returns (not arranged via pickup):</p>
                    <address>
                        <strong>EasyBuy Returns Department</strong><br>
                        Plot 123, Kampala Road<br>
                        P.O. Box 12345, Kampala<br>
                        Uganda<br>
                        <strong>Phone:</strong> +256 700 000 000
                    </address>
                    <p class="text-muted small">Please write your order number clearly on the package.</p>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Need Help?</h5>
                </div>
                <div class="card-body">
                    <p>If you have any questions about returns or refunds, our customer support team is here to help:</p>
                    <div class="row">
                        <div class="col-md-6">
                            <p><i class="fas fa-phone text-primary me-2"></i> <strong>Call:</strong> +256 700 000 000</p>
                            <p><i class="fab fa-whatsapp text-primary me-2"></i> <strong>WhatsApp:</strong> +256 700 000 000</p>
                        </div>
                        <div class="col-md-6">
                            <p><i class="fas fa-envelope text-primary me-2"></i> <strong>Email:</strong> returns@easybuy.ug</p>
                            <p><i class="fas fa-clock text-primary me-2"></i> <strong>Hours:</strong> Mon-Sat, 8 AM - 6 PM</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.step-number {
    width: 35px;
    height: 35px;
    background: var(--primary-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    flex-shrink: 0;
}
</style>

<?php require_once 'includes/footer.php'; ?>