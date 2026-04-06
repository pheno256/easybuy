<?php
http_response_code(404);
$page_title = 'Page Not Found';
require_once 'includes/header.php';
?>

<div class="container py-5 text-center">
    <i class="fas fa-search fa-5x text-muted mb-4"></i>
    <h1 class="display-1 fw-bold text-muted">404</h1>
    <h2 class="mb-4">Page Not Found</h2>
    <p class="lead mb-4">Sorry, the page you are looking for does not exist or has been moved.</p>
    <a href="index.php" class="btn btn-primary btn-lg">
        <i class="fas fa-home"></i> Back to Home
    </a>
</div>

<?php require_once 'includes/footer.php'; ?>