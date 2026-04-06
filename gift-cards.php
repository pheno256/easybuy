<?php
require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$page_title = 'Gift Cards';
$db = Database::getInstance();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = $_POST['amount'];
    $recipient_name = $_POST['recipient_name'];
    $recipient_email = $_POST['recipient_email'];
    $message = $_POST['message'];
    
    // Generate unique gift card code
    $code = 'GIFT-' . strtoupper(uniqid());
    
    $db->insert('gift_cards', [
        'code' => $code,
        'amount' => $amount,
        'recipient_name' => $recipient_name,
        'recipient_email' => $recipient_email,
        'message' => $message,
        'buyer_id' => $_SESSION['user_id'] ?? null,
        'status' => 'active',
        'expiry_date' => date('Y-m-d H:i:s', strtotime('+1 year'))
    ]);
    
    $success = "Gift card created successfully! Code: " . $code;
}

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="text-center mb-5">
                <i class="fas fa-gift fa-4x text-primary mb-3"></i>
                <h1>EasyBuy Gift Cards</h1>
                <p class="lead">The perfect gift for any occasion</p>
            </div>
            
            <?php if(isset($success)): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
                <hr>
                <p class="mb-0">Share this code with the recipient. They can use it at checkout.</p>
            </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center">
                            <h3>UGX 50,000</h3>
                            <button class="btn btn-outline-primary mt-3 select-amount" data-amount="50000">Select</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center">
                            <h3>UGX 100,000</h3>
                            <button class="btn btn-outline-primary mt-3 select-amount" data-amount="100000">Select</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center">
                            <h3>UGX 200,000</h3>
                            <button class="btn btn-outline-primary mt-3 select-amount" data-amount="200000">Select</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center">
                            <h3>UGX 500,000</h3>
                            <button class="btn btn-outline-primary mt-3 select-amount" data-amount="500000">Select</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm mt-4">
                <div class="card-body p-4">
                    <h5>Custom Gift Card</h5>
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Amount (UGX)</label>
                                <input type="number" class="form-control" name="amount" id="amount" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Recipient Name</label>
                                <input type="text" class="form-control" name="recipient_name" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Recipient Email</label>
                                <input type="email" class="form-control" name="recipient_email" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Personal Message</label>
                                <textarea class="form-control" name="message" rows="3"></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Purchase Gift Card</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.select-amount').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('amount').value = this.dataset.amount;
        this.scrollIntoView({ behavior: 'smooth' });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>