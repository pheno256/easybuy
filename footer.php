</main>

<footer class="bg-dark text-white pt-5 mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <h5 class="mb-3">EasyBuy Uganda</h5>
                <p>Your trusted online shopping destination in Uganda. Quality products, best prices, and fast delivery.</p>
                <div class="mt-3">
                    <i class="fas fa-phone me-2"></i> +256 700 000 000<br>
                    <i class="fas fa-envelope me-2"></i> info@easybuy.ug<br>
                    <i class="fas fa-map-marker-alt me-2"></i> Kampala, Uganda
                </div>
            </div>
            
            <div class="col-lg-2">
                <h5 class="mb-3">Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="about.php" class="text-white-50 text-decoration-none">About Us</a></li>
                    <li><a href="contact.php" class="text-white-50 text-decoration-none">Contact</a></li>
                    <li><a href="faq.php" class="text-white-50 text-decoration-none">FAQ</a></li>
                    <li><a href="shipping.php" class="text-white-50 text-decoration-none">Shipping Info</a></li>
                </ul>
            </div>
            
            <div class="col-lg-2">
                <h5 class="mb-3">My Account</h5>
                <ul class="list-unstyled">
                    <li><a href="account.php" class="text-white-50 text-decoration-none">My Account</a></li>
                    <li><a href="track-order.php" class="text-white-50 text-decoration-none">Track Order</a></li>
                    <li><a href="wishlist.php" class="text-white-50 text-decoration-none">Wishlist</a></li>
                    <li><a href="cart.php" class="text-white-50 text-decoration-none">Cart</a></li>
                </ul>
            </div>
            
            <div class="col-lg-4">
                <h5 class="mb-3">Payment Methods</h5>
                <div class="d-flex gap-3">
                    <img src="assets/images/mtn-momo.png" alt="MTN Mobile Money" height="40">
                    <img src="assets/images/airtel-money.png" alt="Airtel Money" height="40">
                </div>
                <h5 class="mt-3 mb-2">Follow Us</h5>
                <div class="d-flex gap-3">
                    <a href="#" class="text-white"><i class="fab fa-facebook fa-2x"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-twitter fa-2x"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-instagram fa-2x"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-whatsapp fa-2x"></i></a>
                </div>
            </div>
        </div>
        
        <hr class="mt-4">
        <div class="text-center py-3">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> EasyBuy Uganda. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?php echo APP_URL; ?>/assets/js/main.js"></script>
<script src="<?php echo APP_URL; ?>/assets/js/cart.js"></script>
<script src="<?php echo APP_URL; ?>/assets/js/auth.js"></script>

<script>
    AOS.init({
        duration: 1000,
        once: true
    });
</script>
</body>
</html>