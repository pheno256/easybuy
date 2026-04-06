<?php
require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$page_title = 'Search Results';
$db = Database::getInstance();

$query = $_GET['q'] ?? '';
$category = $_GET['category'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';

$where = "WHERE status = 'active'";
$params = [];

if(!empty($query)) {
    $where .= " AND (name LIKE ? OR description LIKE ?)";
    $search_term = "%{$query}%";
    $params[] = $search_term;
    $params[] = $search_term;
}

if(!empty($category)) {
    $where .= " AND category_id = (SELECT id FROM categories WHERE slug = ?)";
    $params[] = $category;
}

if(!empty($min_price) && is_numeric($min_price)) {
    $where .= " AND price >= ?";
    $params[] = $min_price;
}

if(!empty($max_price) && is_numeric($max_price)) {
    $where .= " AND price <= ?";
    $params[] = $max_price;
}

$products = $db->query("SELECT * FROM products $where ORDER BY created_at DESC", $params)->fetchAll();
$total_results = count($products);

// Get categories for filter
$categories = $db->query("SELECT * FROM categories ORDER BY name")->fetchAll();

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Filter Results</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="search.php">
                        <input type="hidden" name="q" value="<?php echo htmlspecialchars($query); ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category">
                                <option value="">All Categories</option>
                                <?php foreach($categories as $cat): ?>
                                <option value="<?php echo $cat['slug']; ?>" 
                                    <?php echo $category == $cat['slug'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Price Range (UGX)</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" class="form-control" name="min_price" 
                                           placeholder="Min" value="<?php echo $min_price; ?>">
                                </div>
                                <div class="col-6">
                                    <input type="number" class="form-control" name="max_price" 
                                           placeholder="Max" value="<?php echo $max_price; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                        <a href="search.php?q=<?php echo urlencode($query); ?>" class="btn btn-outline-secondary w-100 mt-2">Reset</a>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Search Results -->
        <div class="col-lg-9">
            <div class="mb-4">
                <h4>Search Results for "<?php echo htmlspecialchars($query); ?>"</h4>
                <p class="text-muted">Found <?php echo $total_results; ?> product(s)</p>
            </div>
            
            <?php if(empty($products)): ?>
            <div class="text-center py-5">
                <i class="fas fa-search fa-5x text-muted mb-4"></i>
                <h3>No products found</h3>
                <p class="text-muted">Try adjusting your search terms or filters</p>
                <a href="shop.php" class="btn btn-primary">Browse All Products</a>
            </div>
            <?php else: ?>
            <div class="row g-4">
                <?php foreach($products as $product): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card product-card h-100 border-0 shadow-sm hover-lift">
                        <div class="position-relative">
                            <img src="assets/images/products/<?php echo $product['image']; ?>" 
                                 class="card-img-top" alt="<?php echo $product['name']; ?>"
                                 style="height: 200px; object-fit: cover;">
                            <?php if($product['discount_price']): ?>
                            <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                                -<?php echo round((1 - $product['discount_price']/$product['price']) * 100); ?>%
                            </span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text text-muted small"><?php echo substr($product['description'], 0, 60); ?>...</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <?php if($product['discount_price']): ?>
                                    <span class="text-muted text-decoration-line-through small">UGX <?php echo number_format($product['price']); ?></span>
                                    <div class="text-danger fw-bold">UGX <?php echo number_format($product['discount_price']); ?></div>
                                    <?php else: ?>
                                    <div class="text-primary fw-bold">UGX <?php echo number_format($product['price']); ?></div>
                                    <?php endif; ?>
                                </div>
                                <button class="btn btn-sm btn-primary add-to-cart" data-product-id="<?php echo $product['id']; ?>">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0 text-center pb-3">
                            <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-primary btn-sm w-100">View Details</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>