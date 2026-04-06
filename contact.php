<?php
require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$page_title = 'Contact Us';
$success = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    
    if(empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'Please fill in all fields';
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        // Save to database
        $db = Database::getInstance();
        $db->query("
            INSERT INTO contacts (name, email, subject, message) 
            VALUES (?, ?, ?, ?)
        ", [$name, $email, $subject, $message]);
        
        // Send email notification
        $to = "info@easybuy.ug";
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        mail($to, $subject, $message, $headers);
        
        $success = "Thank you for contacting us. We'll get back to you soon!";
        
        // Clear form
        $_POST = [];
    }
}

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body p-4">
                    <h2 class="mb-4">Get in Touch</h2>
                    
                    <?php if($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Your Name</label>
                            <input type="text" class="form-control" name="name" required 
                                   value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="email" required
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input type="text" class="form-control" name="subject" required
                                   value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea class="form-control" name="message" rows="5" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card shadow-sm mb-4">
                <div class="card-body p-4">
                    <h3 class="mb-4">Contact Information</h3>
                    <div class="mb-3">
                        <i class="fas fa-map-marker-alt fa-2x text-primary mb-2"></i>
                        <h5>Visit Us</h5>
                        <p class="text-muted">
                            EasyBuy Uganda<br>
                            Kampala Road,<br>
                            Kampala, Uganda
                        </p>
                    </div>
                    <div class="mb-3">
                        <i class="fas fa-phone fa-2x text-primary mb-2"></i>
                        <h5>Call Us</h5>
                        <p class="text-muted">
                            +256 700 000 000<br>
                            +256 800 000 000 (Toll-Free)
                        </p>
                    </div>
                    <div class="mb-3">
                        <i class="fas fa-envelope fa-2x text-primary mb-2"></i>
                        <h5>Email Us</h5>
                        <p class="text-muted">
                            info@easybuy.ug<br>
                            support@easybuy.ug
                        </p>
                    </div>
                    <div>
                        <i class="fas fa-clock fa-2x text-primary mb-2"></i>
                        <h5>Business Hours</h5>
                        <p class="text-muted">
                            Monday - Friday: 8:00 AM - 8:00 PM<br>
                            Saturday: 9:00 AM - 6:00 PM<br>
                            Sunday: 10:00 AM - 4:00 PM
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3">Follow Us</h5>
                    <div class="d-flex gap-3">
                        <a href="#" class="btn btn-outline-primary rounded-circle" style="width: 50px; height: 50px;">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="btn btn-outline-primary rounded-circle" style="width: 50px; height: 50px;">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="btn btn-outline-primary rounded-circle" style="width: 50px; height: 50px;">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="btn btn-outline-primary rounded-circle" style="width: 50px; height: 50px;">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>