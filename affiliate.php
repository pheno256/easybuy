<?php
require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$page_title = 'Affiliate Program';

if(isset($_SESSION['user_id'])) {
    $db = Database::getInstance();
    $affiliate = $db->query("SELECT * FROM affiliates WHERE user_id = ?", [$_SESSION['user_id']])->fetch();
}

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="text-center mb-5">
                <i class="fas fa-handshake fa-4x text-primary mb-3"></i>
                <h1>EasyBuy Affiliate Program</h1>
                <p class="lead">Earn money by referring friends to EasyBuy Uganda</p>
            </div>
            
            <div class="row mb-5">
                <div class="col-md-4 mb-3">
                    <div class="card h-100 text-center border-0 shadow-sm">
                        <div class="card-body">
                            <i class="fas fa-percent fa-3x text-primary mb-3"></i>
                            <h3>10% Commission</h3>
                            <p>Earn 10% on every sale from your referrals</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card h-100 text-center border-0 shadow-sm">
                        <div class="card-body">
                            <i class="fas fa-chart-line fa-3x text-primary mb-3"></i>
                            <h3>Real-Time Tracking</h3>
                            <p>Track clicks, sales, and earnings in real-time</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card h-100 text-center border-0 shadow-sm">
                        <div class="card-body">
                            <i class="fas fa-money-bill-wave fa-3x text-primary mb-3"></i>
                            <h3>Monthly Payouts</h3>
                            <p>Get paid via MTN or Airtel Money</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if(isset($_SESSION['user_id']) && $affiliate): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Your Affiliate Dashboard</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            <h3><?php echo number_format($affiliate['total_clicks'] ?? 0); ?></h3>
                            <p>Total Clicks</p>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <h3><?php echo number_format($affiliate['total_sales'] ?? 0); ?></h3>
                            <p>Total Sales</p>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <h3>UGX <?php echo number_format($affiliate['total_earnings'] ?? 0); ?></h3>
                            <p>Total Earnings</p>
                        </div>
                    </div>
                    
                    <div class="alert alert-success">
                        <strong>Your Affiliate Link:</strong><br>
                        <code><?php echo APP_URL; ?>/?ref=<?php echo $affiliate['code']; ?></code>
                        <button class="btn btn-sm btn-outline-primary ms-2" onclick="copyLink()">
                            <i class="fas fa-copy"></i> Copy
                        </button>
                    </div>
                    
                    <div class="mt-3">
                        <button class="btn btn-primary" onclick="shareAffiliate()">
                            <i class="fas fa-share-alt"></i> Share Your Link
                        </button>
                        <button class="btn btn-outline-primary" onclick="requestPayout()">
                            <i class="fas fa-money-bill-wave"></i> Request Payout
                        </button>
                    </div>
                </div>
            </div>
            <?php elseif(isset($_SESSION['user_id'])): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <h5>Join Our Affiliate Program</h5>
                    <p>Start earning today by promoting EasyBuy Uganda</p>
                    <form method="POST" action="affiliate.php?join=1">
                        <button type="submit" class="btn btn-primary btn-lg">Join Now</button>
                    </form>
                </div>
            </div>
            <?php else: ?>
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <h5>Ready to Start Earning?</h5>
                    <p>Create an account to join our affiliate program</p>
                    <a href="register.php" class="btn btn-primary btn-lg">Create Account</a>
                    <a href="login.php" class="btn btn-outline-primary btn-lg ms-2">Login</a>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>How It Works</h5>
                    <div class="row mt-3">
                        <div class="col-md-3 text-center mb-3">
                            <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                <span class="h4 mb-0">1</span>
                            </div>
                            <h6>Sign Up</h6>
                            <p class="small">Create your free affiliate account</p>
                        </div>
                        <div class="col-md-3 text-center mb-3">
                            <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                <span class="h4 mb-0">2</span>
                            </div>
                            <h6>Share Link</h6>
                            <p class="small">Share your unique affiliate link</p>
                        </div>
                        <div class="col-md-3 text-center mb-3">
                            <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                <span class="h4 mb-0">3</span>
                            </div>
                            <h6>Earn Commission</h6>
                            <p class="small">Get 10% on every sale</p>
                        </div>
                        <div class="col-md-3 text-center mb-3">
                            <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                <span class="h4 mb-0">4</span>
                            </div>
                            <h6>Get Paid</h6>
                            <p class="small">Receive monthly payouts</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyLink() {
    const link = document.querySelector('code').innerText;
    navigator.clipboard.writeText(link);
    showNotification('Link copied to clipboard!', 'success');
}

function shareAffiliate() {
    if(navigator.share) {
        navigator.share({
            title: 'EasyBuy Uganda',
            text: 'Shop at EasyBuy Uganda and get great deals!',
            url: document.querySelector('code').innerText
        });
    } else {
        copyLink();
    }
}

function requestPayout() {
    Swal.fire({
        title: 'Request Payout',
        text: 'Request payment for your earnings?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, request'
    }).then((result) => {
        if(result.isConfirmed) {
            showNotification('Payout request submitted!', 'success');
        }
    });
}
</script>

<?php require_once 'includes/footer.php'; ?>