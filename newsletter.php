<!-- Newsletter Signup Section -->
<section class="newsletter-section py-5 bg-primary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center text-white">
                <h3 class="mb-3">Subscribe to Our Newsletter</h3>
                <p class="mb-4">Get the latest updates on new products and exclusive offers!</p>
                <form id="newsletter-form" class="row g-2 justify-content-center">
                    <div class="col-md-6">
                        <input type="email" class="form-control form-control-lg" name="email" 
                               placeholder="Enter your email address" required>
                    </div>
                    <div class="col-md-auto">
                        <button type="submit" class="btn btn-light btn-lg">
                            <i class="fas fa-paper-plane"></i> Subscribe
                        </button>
                    </div>
                </form>
                <div id="newsletter-message" class="mt-3"></div>
            </div>
        </div>
    </div>
</section>

<script>
$('#newsletter-form').submit(function(e) {
    e.preventDefault();
    var email = $(this).find('input[name="email"]').val();
    
    $.ajax({
        url: '/api/newsletter.php',
        method: 'POST',
        data: {
            action: 'subscribe',
            email: email
        },
        success: function(response) {
            if(response.success) {
                $('#newsletter-message').html(
                    '<div class="alert alert-success">' + response.message + '</div>'
                );
                $('#newsletter-form')[0].reset();
                setTimeout(function() {
                    $('#newsletter-message').html('');
                }, 3000);
            } else {
                $('#newsletter-message').html(
                    '<div class="alert alert-danger">' + response.message + '</div>'
                );
            }
        }
    });
});
</script>