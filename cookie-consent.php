<?php
// Cookie consent banner
if(!isset($_COOKIE['cookie_consent'])) {
?>
<div id="cookie-consent" class="fixed-bottom bg-dark text-white p-3" style="z-index: 9999;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-9">
                <p class="mb-0">
                    <i class="fas fa-cookie-bite"></i> We use cookies to enhance your experience. 
                    By continuing to visit this site you agree to our use of cookies.
                    <a href="privacy.php" class="text-white text-decoration-underline">Learn more</a>
                </p>
            </div>
            <div class="col-md-3 text-end">
                <button class="btn btn-primary btn-sm" onclick="acceptCookies()">
                    <i class="fas fa-check"></i> Accept
                </button>
                <button class="btn btn-outline-light btn-sm" onclick="declineCookies()">
                    Decline
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function acceptCookies() {
    document.cookie = "cookie_consent=accepted; path=/; max-age=" + (365 * 24 * 60 * 60);
    document.getElementById('cookie-consent').style.display = 'none';
}

function declineCookies() {
    document.cookie = "cookie_consent=declined; path=/; max-age=" + (30 * 24 * 60 * 60);
    document.getElementById('cookie-consent').style.display = 'none';
}
</script>
<?php } ?>