<?php
require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$page_title = 'Shop';
$db = Database::getInstance();

// Get categories for filter
$categories = $db->query("SELECT * FROM categories ORDER BY name")->fetchAll();

// Build query
$where = "WHERE status = 'active'";
$params = [];

if(isset($_GET['category']) && !empty($_GET['category'])) {
    $where .= " AND slug = ?";
    $params[] = $_GET['category'];
}

if(isset($_GET['search']) && !empty($_GET['search'])) {
    $where .= " AND (name LIKE ? OR description LIKE ?)";
    $search = "%{$_GET['search']}%";
    $params[] = $search;
    $params[] = $search;
}

if(isset($_GET['min_price']) && is_numeric($_GET['min_price'])) {
    $where .= " AND price >= ?";
    $params[] = $_GET['min_price'];
}

if(isset($_GET['max_price']) && is_numeric($_GET['max_price'])) {
    $where .= " AND price <= ?";
    $params[] = $_GET['max_price'];
}

// Get total products
$total = $db->query("SELECT COUNT(*) as count FROM products $where", $params)->fetch()['count'];

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;
$total_pages = ceil($total / $per_page);

// Order by
$order_by = "created_at DESC";
if(isset($_GET['sort'])) {
    switch($_GET['sort']) {
        case 'price_asc':
            $order_by = "price ASC";
            break;
        case 'price_desc':
            $order_by = "price DESC";
            break;
        case 'name_asc':
            $order_by = "name ASC";
            break;
        case 'popular':
            $order_by = "views DESC";
            break;
    }
}

// Get products
$products = $db->query("
    SELECT * FROM products 
    $where 
    ORDER BY $order_by 
    LIMIT $offset, $per_page
", $params)->fetchAll();

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Filters</h5>
                </div>
                <div class="card-body">
                    <form method="GET" id="filter-form">
                        <?php if(isset($_GET['search'])): ?>
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($_GET['search']); ?>">
                        <?php endif; ?>
                        
                        <!-- Categories -->
                        <div class="mb-4">
                            <h6>Categories</h6>
                            <div class="list-group list-group-flush">
                                <a href="shop.php" class="list-group-item list-group-item-action <?php echo !isset($_GET['category']) ? 'active' : ''; ?>">
                                    All Products
                                </a>
                                <?php foreach($categories as $cat): ?>
                                <a href="?category=<?php echo $cat['slug']; ?>" 
                                   class="list-group-item list-group-item-action <?php echo (isset($_GET['category']) && $_GET['category'] == $cat['slug']) ? 'active' : ''; ?>">
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Price Range -->
                        <div class="mb-4">
                            <h6>Price Range (UGX)</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" class="form-control" name="min_price" 
                                           placeholder="Min" value="<?php echo $_GET['min_price'] ?? ''; ?>">
                                </div>
                                <div class="col-6">
                                    <input type="number" class="form-control" name="max_price" 
                                           placeholder="Max" value="<?php echo $_GET['max_price'] ?? ''; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                        <a href="shop.php" class="btn btn-outline-secondary w-100 mt-2">Reset Filters</a>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Products (<?php echo $total; ?>)</h4>
                <div class="d-flex gap-2">
                    <select class="form-select" id="sort-select" style="width: auto;">
                        <option value="">Sort by</option>
                        <option value="newest" <?php echo (!isset($_GET['sort']) || $_GET['sort'] == 'newest') ? 'selected' : ''; ?>>Newest</option>
                        <option value="price_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'selected' : ''; ?>>Price: High to Low</option>
                        <option value="name_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'name_asc') ? 'selected' : ''; ?>>Name A-Z</option>
                    </select>
                    <div class="btn-group">
                        <button class="btn btn-outline-primary" id="grid-view">
                            <i class="fas fa-th"></i>
                        </button>
                        <button class="btn btn-outline-primary" id="list-view">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <?php if(empty($products)): ?>
            <div class="text-center py-5">
                <i class="fas fa-search fa-4x text-muted mb-3"></i>
                <h5>No products found</h5>
                <p>Try adjusting your filters or search terms</p>
                <a href="shop.php" class="btn btn-primary">Clear Filters</a>
            </div>
            <?php else: ?>
            <div class="row g-4" id="products-container">
                <?php foreach($products as $product): ?>
                <div class="col-md-6 col-lg-4 product-item">
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
                            <?php if($product['stock'] == 0): ?>
                            <div class="position-absolute top-0 start-0 m-2 bg-dark text-white px-2 py-1 rounded">
                                Out of Stock
                            </div>
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
                                <?php if($product['stock'] > 0): ?>
                                <button class="btn btn-sm btn-primary add-to-cart" data-product-id="<?php echo $product['id']; ?>">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0 text-center pb-3">
                            <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-primary btn-sm w-100">View Details</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if($total_pages > 1): ?>
            <nav class="mt-5">
                <ul class="pagination justify-content-center">
                    <?php if($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page-1; ?>&<?php echo http_build_query(array_diff_key($_GET, ['page' => ''])); ?>">
                            Previous
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&<?php echo http_build_query(array_diff_key($_GET, ['page' => ''])); ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                    <?php endfor; ?>
                    
                    <?php if($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page+1; ?>&<?php echo http_build_query(array_diff_key($_GET, ['page' => ''])); ?>">
                            Next
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.getElementById('sort-select').addEventListener('change', function() {
    const url = new URL(window.location.href);
    if(this.value) {
        url.searchParams.set('sort', this.value);
    } else {
        url.searchParams.delete('sort');
    }
    window.location.href = url.toString();
});

// Grid/List view toggle
let isGridView = localStorage.getItem('viewMode') !== 'list';
updateView();

document.getElementById('grid-view').addEventListener('click', () => {
    isGridView = true;
    localStorage.setItem('viewMode', 'grid');
    updateView();
});

document.getElementById('list-view').addEventListener('click', () => {
    isGridView = false;
    localStorage.setItem('viewMode', 'list');
    updateView();
});

function updateView() {
    const container = document.getElementById('products-container');
    if(isGridView) {
        container.className = 'row g-4';
        document.querySelectorAll('.product-item').forEach(item => {
            item.className = 'col-md-6 col-lg-4 product-item';
        });
    } else {
        container.className = 'row g-3';
        document.querySelectorAll('.product-item').forEach(item => {
            item.className = 'col-12 product-item';
        });
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>