<?php
require_once '../app/config/config.php';
$page_title = 'Cookie Policy';

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <h1 class="mb-4">Cookie Policy</h1>
            <p class="text-muted">Last Updated: January 1, 2024</p>
            
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5>What Are Cookies?</h5>
                    <p>Cookies are small text files that are placed on your computer or mobile device when you visit our website. They help us provide you with a better experience.</p>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5>How We Use Cookies</h5>
                    <p>We use cookies for:</p>
                    <ul>
                        <li><strong>Essential Cookies:</strong> Required for website functionality (shopping cart, login)</li>
                        <li><strong>Analytics Cookies:</strong> Help us understand how visitors use our site</li>
                        <li><strong>Preference Cookies:</strong> Remember your settings and preferences</li>
                        <li><strong>Marketing Cookies:</strong> Used to deliver relevant advertisements</li>
                    </ul>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5>Types of Cookies We Use</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Cookie Name</th>
                                    <th>Purpose</th>
                                    <th>Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>session_id</td>
                                    <td>Maintain user session</td>
                                    <td>Session</td>
                                </tr>
                                <tr>
                                    <td>cart_items</td>
                                    <td>Store cart contents</td>
                                    <td>30 days</td>
                                </tr>
                                <tr>
                                    <td>user_preferences</td>
                                    <td>Save user settings</td>
                                    <td>1 year</td>
                                </tr>
                                <tr>
                                    <td>_ga</td>
                                    <td>Google Analytics</td>
                                    <td>2 years</td>
                                </tr>
                            </tbody>
                         </table>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5>Managing Cookies</h5>
                    <p>You can control cookies through your browser settings. Most browsers allow you to:</p>
                    <ul>
                        <li>See what cookies are stored</li>
                        <li>Delete all cookies</li>
                        <li>Block cookies from specific sites</li>
                        <li>Block all cookies</li>
                    </ul>
                    <p>Note: Disabling cookies may affect website functionality.</p>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5>Third-Party Cookies</h5>
                    <p>We use third-party services that may set cookies:</p>
                    <ul>
                        <li>Google Analytics for website analytics</li>
                        <li>Facebook Pixel for advertising</li>
                        <li>Payment gateways (MTN, Airtel)</li>
                    </ul>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Contact Us</h5>
                    <p>If you have questions about our Cookie Policy, please contact us at privacy@easybuy.ug</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>