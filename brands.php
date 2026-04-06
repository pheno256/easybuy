<?php
require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$page_title = 'Brands';
$db = Database::getInstance();

$brands = $db->query("SELECT * FROM brands WHERE status = 'active' ORDER BY name")->fetchAll();

require_once 'includes/header.php';
?>

<div class="container py-5">
    <h1 class="text-center mb-5">Shop by Brand</h1>
    
    <div class="row g-4">
        <?php foreach($brands as $brand): ?>
        <div class="col-lg-3 col-md-4 col-6">
            <a href="shop.php?brand=<?php echo $brand['slug']; ?>" class="text-decoration-none">
                <div class="card brand-card h-100 border-0 shadow-sm text-center hover-lift">
                    <div class="card-body p-4">
                        <?php if($brand['logo']): ?>
                        <img src="assets/images/brands/<?php echo $brand['logo']; ?>" 
                             alt="<?php echo $brand['name']; ?>" class="img-fluid mb-3" style="height: 80px; object-fit: contain;">
                        <?php else: ?>
                        <i class="fas fa-building fa-3x text-primary mb-3"></i>
                        <?php endif; ?>
                        <h5 class="card-title"><?php echo htmlspecialchars($brand['name']); ?></h5>
                        <p class="card-text text-muted small"><?php echo $brand['product_count']; ?> products</p>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>