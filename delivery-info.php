<?php
require_once '../app/config/config.php';
$page_title = 'Delivery Information';

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <h1 class="mb-4">Delivery Information</h1>
            
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5><i class="fas fa-truck text-primary me-2"></i> Delivery Coverage</h5>
                    <p>We deliver to all districts across Uganda! Our delivery network covers:</p>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <h6>Central Region</h6>
                            <ul class="list-unstyled">
                                <li>Kampala</li>
                                <li>Wakiso</li>
                                <li>Mukono</li>
                                <li>Entebbe</li>
                                <li>Masaka</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h6>Eastern Region</h6>
                            <ul class="list-unstyled">
                                <li>Jinja</li>
                                <li>Mbale</li>
                                <li>Tororo</li>
                                <li>Soroti</li>
                                <li>Iganga</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h6>Western Region</h6>
                            <ul class="list-unstyled">
                                <li>Mbarara</li>
                                <li>Fort Portal</li>
                                <li>Kasese</li>
                                <li>Bushenyi</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5><i class="fas fa-clock text-primary me-2"></i> Delivery Times</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Location</th>
                                    <th>Delivery Time</th>
                                    <th>Fee</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Kampala Metro</td>
                                    <td>1-2 business days</td>
                                    <td>UGX 10,000</td>
                                </tr>
                                <tr>
                                    <td>Greater Kampala (Wakiso, Mukono, Entebbe)</td>
                                    <td>2-3 business days</td>
                                    <td>UGX 12,000</td>
                                </tr>
                                <tr>
                                    <td>Major Cities (Jinja, Mbarara, Gulu)</td>
                                    <td>3-4 business days</td>
                                    <td>UGX 15,000</td>
                                </tr>
                                <tr>
                                    <td>Other Districts</td>
                                    <td>4-6 business days</td>
                                    <td>UGX 20,000-25,000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5><i class="fas fa-gift text-primary me-2"></i> Free Delivery Offer</h5>
                    <p class="lead text-success">Free delivery on all orders above UGX 200,000!</p>
                    <p>Orders that qualify for free delivery will have the delivery fee automatically waived at checkout.</p>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5><i class="fas fa-box text-primary me-2"></i> Order Processing</h5>
                    <ul>
                        <li>Orders are processed within 24 hours of payment confirmation</li>
                        <li>You will receive an SMS when your order is ready for dispatch</li>
                        <li>Tracking information is sent via email and SMS</li>
                        <li>Our delivery partners will contact you before delivery</li>
                    </ul>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5><i class="fas fa-question-circle text-primary me-2"></i> Delivery FAQs</h5>
                    <div class="accordion" id="deliveryFaq">
                        <div class="accordion-item">
                            <h6 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Can I change my delivery address after ordering?
                                </button>
                            </h6>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#deliveryFaq">
                                <div class="accordion-body">
                                    Yes, contact our support team within 30 minutes of placing your order. After that, we cannot guarantee address changes.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h6 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    What if I'm not home when delivery arrives?
                                </button>
                            </h6>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#deliveryFaq">
                                <div class="accordion-body">
                                    Our delivery partner will call you. If you're not available, they will make two more attempts or leave with a security guard/receptionist with your permission.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h6 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    How can I track my delivery?
                                </button>
                            </h6>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#deliveryFaq">
                                <div class="accordion-body">
                                    Use our <a href="track-order.php">Track Order</a> page with your order number, or check the tracking link sent to your email/SMS.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5><i class="fas fa-headset text-primary me-2"></i> Need Help?</h5>
                    <p>Contact our delivery support team:</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-phone me-2"></i> +256 700 000 000 (Delivery Support)</li>
                        <li><i class="fas fa-envelope me-2"></i> delivery@easybuy.ug</li>
                        <li><i class="fab fa-whatsapp me-2"></i> +256 700 000 000 (WhatsApp)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>