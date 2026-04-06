<?php
require_once '../app/config/config.php';
$page_title = 'Shipping Policy';

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="mb-4">Shipping Policy</h1>
            
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5>Last Updated: January 1, 2024</h5>
                    <p>At EasyBuy Uganda, we are committed to delivering your orders quickly and reliably across Uganda.</p>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">1. Processing Time</h5>
                </div>
                <div class="card-body">
                    <p>Orders are processed within <strong>24 hours</strong> after payment confirmation. You will receive a confirmation email/SMS once your order is processed.</p>
                    <ul>
                        <li>Orders placed before 2 PM (Monday-Friday) are processed the same day</li>
                        <li>Orders placed after 2 PM are processed the next business day</li>
                        <li>Weekend orders are processed on Monday</li>
                        <li>Public holiday orders are processed on the next business day</li>
                    </ul>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">2. Delivery Time & Zones</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Zone</th>
                                    <th>Districts</th>
                                    <th>Delivery Time</th>
                                    <th>Fee</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Zone 1</td>
                                    <td>Kampala</td>
                                    <td>1-2 business days</td>
                                    <td>UGX 10,000</td>
                                </tr>
                                <tr>
                                    <td>Zone 2</td>
                                    <td>Wakiso, Mukono, Entebbe</td>
                                    <td>2-3 business days</td>
                                    <td>UGX 12,000</td>
                                </tr>
                                <tr>
                                    <td>Zone 3</td>
                                    <td>Jinja, Masaka, Mbale</td>
                                    <td>3-4 business days</td>
                                    <td>UGX 15,000</td>
                                </tr>
                                <tr>
                                    <td>Zone 4</td>
                                    <td>Gulu, Mbarara, Fort Portal</td>
                                    <td>4-5 business days</td>
                                    <td>UGX 20,000</td>
                                </tr>
                                <tr>
                                    <td>Zone 5</td>
                                    <td>Other districts</td>
                                    <td>5-7 business days</td>
                                    <td>UGX 25,000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="text-success mt-3"><strong>Free delivery</strong> on all orders above UGX 200,000!</p>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">3. Delivery Partners</h5>
                </div>
                <div class="card-body">
                    <p>We partner with reliable delivery services in Uganda including:</p>
                    <ul>
                        <li>SafeBoda Deliveries</li>
                        <li>Kampala Couriers</li>
                        <li>Uganda Post (for remote areas)</li>
                        <li>Private logistics partners</li>
                    </ul>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">4. Tracking Your Order</h5>
                </div>
                <div class="card-body">
                    <p>Once your order is shipped, you will receive:</p>
                    <ul>
                        <li>SMS notification with tracking number</li>
                        <li>Email with delivery updates</li>
                        <li>Access to <a href="track-order.php">real-time tracking</a> on our website</li>
                    </ul>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">5. Delivery Attempts</h5>
                </div>
                <div class="card-body">
                    <p>Our delivery partners will make <strong>two delivery attempts</strong>. If both attempts fail:</p>
                    <ul>
                        <li>The order will be returned to our warehouse</li>
                        <li>You will be notified via SMS and email</li>
                        <li>Additional delivery fees may apply for re-delivery</li>
                        <li>You can arrange to pick up from our Kampala store</li>
                    </ul>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">6. International Shipping</h5>
                </div>
                <div class="card-body">
                    <p>Currently, we only ship within Uganda. International shipping will be available soon. For inquiries about shipping outside Uganda, please contact our customer support.</p>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">7. Delayed or Lost Packages</h5>
                </div>
                <div class="card-body">
                    <p>If your package is delayed beyond the estimated delivery time:</p>
                    <ul>
                        <li>Contact our support team at +256 700 000 000</li>
                        <li>Email us at shipping@easybuy.ug with your order number</li>
                        <li>We will investigate and provide updates within 24 hours</li>
                    </ul>
                    <p>For lost packages, we will issue a full refund or send a replacement at no extra cost.</p>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">8. Contact Information</h5>
                </div>
                <div class="card-body">
                    <p>For shipping-related inquiries:</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-phone text-primary me-2"></i> +256 700 000 000 (Shipping Department)</li>
                        <li><i class="fas fa-envelope text-primary me-2"></i> shipping@easybuy.ug</li>
                        <li><i class="fas fa-clock text-primary me-2"></i> Monday-Friday: 8 AM - 6 PM</li>
                        <li><i class="fas fa-clock text-primary me-2"></i> Saturday: 9 AM - 2 PM</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>