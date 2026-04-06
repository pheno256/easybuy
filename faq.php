<?php
require_once '../app/config/config.php';
$page_title = 'Frequently Asked Questions';

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="display-4">Frequently Asked Questions</h1>
        <p class="lead">Find answers to common questions about EasyBuy Uganda</p>
    </div>
    
    <div class="row">
        <div class="col-lg-3 mb-4">
            <div class="list-group sticky-top" style="top: 100px;">
                <a href="#ordering" class="list-group-item list-group-item-action">Ordering</a>
                <a href="#payment" class="list-group-item list-group-item-action">Payment</a>
                <a href="#delivery" class="list-group-item list-group-item-action">Delivery</a>
                <a href="#returns" class="list-group-item list-group-item-action">Returns & Refunds</a>
                <a href="#account" class="list-group-item list-group-item-action">Account</a>
                <a href="#products" class="list-group-item list-group-item-action">Products</a>
            </div>
        </div>
        
        <div class="col-lg-9">
            <div id="ordering" class="mb-5">
                <h2 class="mb-4">Ordering</h2>
                
                <div class="accordion" id="orderingAccordion">
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#q1">
                                How do I place an order?
                            </button>
                        </h3>
                        <div id="q1" class="accordion-collapse collapse show" data-bs-parent="#orderingAccordion">
                            <div class="accordion-body">
                                Placing an order on EasyBuy Uganda is easy:
                                <ol>
                                    <li>Browse our products and add items to your cart</li>
                                    <li>Click on the cart icon to review your items</li>
                                    <li>Proceed to checkout and enter your shipping information</li>
                                    <li>Select your preferred payment method (MTN or Airtel Money)</li>
                                    <li>Confirm your order and complete payment</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q2">
                                Can I modify my order after placing it?
                            </button>
                        </h3>
                        <div id="q2" class="accordion-collapse collapse" data-bs-parent="#orderingAccordion">
                            <div class="accordion-body">
                                You can modify your order within 30 minutes of placing it. Please contact our customer support immediately at +256 700 000 000 or email support@easybuy.ug with your order number and modification requests.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q3">
                                How do I track my order?
                            </button>
                        </h3>
                        <div id="q3" class="accordion-collapse collapse" data-bs-parent="#orderingAccordion">
                            <div class="accordion-body">
                                You can track your order by:
                                <ul>
                                    <li>Going to the <a href="track-order.php">Track Order</a> page and entering your order number and email</li>
                                    <li>Logging into your account and viewing your order history</li>
                                    <li>Checking the tracking link sent to your email/SMS</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id="payment" class="mb-5">
                <h2 class="mb-4">Payment</h2>
                
                <div class="accordion" id="paymentAccordion">
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#p1">
                                What payment methods do you accept?
                            </button>
                        </h3>
                        <div id="p1" class="accordion-collapse collapse show" data-bs-parent="#paymentAccordion">
                            <div class="accordion-body">
                                We accept the following payment methods in Uganda:
                                <ul>
                                    <li><strong>MTN Mobile Money</strong> - Pay directly from your MTN MoMo account</li>
                                    <li><strong>Airtel Money</strong> - Pay using your Airtel Money wallet</li>
                                </ul>
                                All payments are secure and encrypted.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#p2">
                                Is it safe to pay with Mobile Money?
                            </button>
                        </h3>
                        <div id="p2" class="accordion-collapse collapse" data-bs-parent="#paymentAccordion">
                            <div class="accordion-body">
                                Yes, absolutely! We use secure, encrypted payment gateways for both MTN and Airtel Mobile Money. Your payment information is never stored on our servers. You will receive a PIN prompt on your phone to authorize the payment, adding an extra layer of security.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#p3">
                                What if my payment fails?
                            </button>
                        </h3>
                        <div id="p3" class="accordion-collapse collapse" data-bs-parent="#paymentAccordion">
                            <div class="accordion-body">
                                If your payment fails, you can:
                                <ul>
                                    <li>Check your mobile money balance to ensure you have sufficient funds</li>
                                    <li>Try again after a few minutes</li>
                                    <li>Use a different payment method</li>
                                    <li>Contact your mobile network provider</li>
                                </ul>
                                Your order will not be processed until payment is successful.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id="delivery" class="mb-5">
                <h2 class="mb-4">Delivery</h2>
                
                <div class="accordion" id="deliveryAccordion">
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#d1">
                                How long does delivery take?
                            </button>
                        </h3>
                        <div id="d1" class="accordion-collapse collapse show" data-bs-parent="#deliveryAccordion">
                            <div class="accordion-body">
                                Delivery times vary by location:
                                <ul>
                                    <li><strong>Kampala & surrounding areas:</strong> 1-2 business days</li>
                                    <li><strong>Major cities (Jinja, Mbarara, Gulu):</strong> 2-3 business days</li>
                                    <li><strong>Other districts:</strong> 3-5 business days</li>
                                </ul>
                                Orders are processed within 24 hours of payment confirmation.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#d2">
                                How much does delivery cost?
                            </button>
                        </h3>
                        <div id="d2" class="accordion-collapse collapse" data-bs-parent="#deliveryAccordion">
                            <div class="accordion-body">
                                Delivery fees are calculated based on your location:
                                <ul>
                                    <li><strong>Free delivery</strong> on orders over UGX 200,000</li>
                                    <li><strong>Kampala:</strong> UGX 10,000</li>
                                    <li><strong>Other districts:</strong> UGX 15,000 - 25,000</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#d3">
                                Do you deliver to all districts in Uganda?
                            </button>
                        </h3>
                        <div id="d3" class="accordion-collapse collapse" data-bs-parent="#deliveryAccordion">
                            <div class="accordion-body">
                                Yes! We deliver to all districts across Uganda. We have partnered with reliable delivery services to ensure your orders reach you, no matter where you are in the country.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id="returns" class="mb-5">
                <h2 class="mb-4">Returns & Refunds</h2>
                
                <div class="accordion" id="returnsAccordion">
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#r1">
                                What is your return policy?
                            </button>
                        </h3>
                        <div id="r1" class="accordion-collapse collapse show" data-bs-parent="#returnsAccordion">
                            <div class="accordion-body">
                                We offer a <strong>7-day return policy</strong> for most items. You can return products if:
                                <ul>
                                    <li>The product is damaged or defective</li>
                                    <li>The wrong item was delivered</li>
                                    <li>You changed your mind (item must be unused and in original packaging)</li>
                                </ul>
                                Please visit our <a href="returns.php">Returns Policy</a> page for detailed information.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#r2">
                                How do I initiate a return?
                            </button>
                        </h3>
                        <div id="r2" class="accordion-collapse collapse" data-bs-parent="#returnsAccordion">
                            <div class="accordion-body">
                                To initiate a return:
                                <ol>
                                    <li>Contact our customer support within 7 days of delivery</li>
                                    <li>Provide your order number and reason for return</li>
                                    <li>Upload photos of the product (if damaged)</li>
                                    <li>Our team will review and approve your return</li>
                                    <li>We'll arrange for pickup or guide you on where to send the item</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#r3">
                                How long does it take to get a refund?
                            </button>
                        </h3>
                        <div id="r3" class="accordion-collapse collapse" data-bs-parent="#returnsAccordion">
                            <div class="accordion-body">
                                Once we receive and inspect your returned item, refunds are processed within 3-5 business days. The refund will be sent to your mobile money account (MTN or Airtel) depending on your original payment method.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id="account" class="mb-5">
                <h2 class="mb-4">Account</h2>
                
                <div class="accordion" id="accountAccordion">
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#a1">
                                How do I create an account?
                            </button>
                        </h3>
                        <div id="a1" class="accordion-collapse collapse show" data-bs-parent="#accountAccordion">
                            <div class="accordion-body">
                                Creating an account is free and easy! Click on the "Sign Up" button at the top of the page, fill in your details (name, email, phone number, and password), and you're ready to start shopping. You can also sign up using your Google or Facebook account.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a2">
                                I forgot my password. What should I do?
                            </button>
                        </h3>
                        <div id="a2" class="accordion-collapse collapse" data-bs-parent="#accountAccordion">
                            <div class="accordion-body">
                                Click on the "Forgot Password" link on the login page. Enter your email address, and we'll send you a link to reset your password. If you don't receive the email within a few minutes, check your spam folder or contact our support team.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a3">
                                How do I update my account information?
                            </button>
                        </h3>
                        <div id="a3" class="accordion-collapse collapse" data-bs-parent="#accountAccordion">
                            <div class="accordion-body">
                                Log into your account and go to the "My Account" page. You can update your profile information, change your password, update your address, and manage your preferences from there.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id="products" class="mb-5">
                <h2 class="mb-4">Products</h2>
                
                <div class="accordion" id="productsAccordion">
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#pr1">
                                Are your products genuine?
                            </button>
                        </h3>
                        <div id="pr1" class="accordion-collapse collapse show" data-bs-parent="#productsAccordion">
                            <div class="accordion-body">
                                Yes! We source all our products directly from authorized distributors and manufacturers. Every product is inspected for quality before being listed on our website. We guarantee 100% genuine products.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#pr2">
                                Do you offer product warranties?
                            </button>
                        </h3>
                        <div id="pr2" class="accordion-collapse collapse" data-bs-parent="#productsAccordion">
                            <div class="accordion-body">
                                Warranty periods vary by product category. Most electronics come with a manufacturer's warranty ranging from 6 months to 2 years. Please check the product description for specific warranty information or contact our support team.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#pr3">
                                Can I get a product that's out of stock?
                            </button>
                        </h3>
                        <div id="pr3" class="accordion-collapse collapse" data-bs-parent="#productsAccordion">
                            <div class="accordion-body">
                                Yes! You can click on the "Notify Me" button on out-of-stock products. We'll send you an email or SMS as soon as the product is back in stock. You can also contact our customer support to check on estimated restock dates.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Smooth scroll for anchor links
document.querySelectorAll('.list-group-item').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if(target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>