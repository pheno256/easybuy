<?php
session_start();
if(!isset($_SESSION['vendor_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$page_title = 'My Products';
$db = Database::getInstance();
$vendor_id = $_SESSION['vendor_id'];
$message = '';

// Handle product deletion
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $db->query("DELETE FROM products WHERE id = ? AND vendor_id = ?", [$id, $vendor_id]);
    $message = "Product deleted successfully!";
}

// Handle product status toggle
if(isset($_GET['toggle'])) {
    $id = $_GET['toggle'];
    $product = $db->query("SELECT status FROM products WHERE id = ? AND vendor_id = ?", [$id, $vendor_id])->fetch();
    if($product) {
        $new_status = $product['status'] == 'active' ? 'inactive' : 'active';
        $db->query("UPDATE products SET status = ? WHERE id = ?", [$new_status, $id]);
        $message = "Product status updated!";
    }
}

// Get vendor products
$products = $db->query("
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.vendor_id = ? 
    ORDER BY p.created_at DESC
", [$vendor_id])->fetchAll();

// Get categories
$categories = $db->query("SELECT * FROM categories ORDER BY name")->fetchAll();

require_once 'includes/vendor-header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">My Products</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
        <i class="fas fa-plus"></i> Add New Product
    </button>
</div>

<?php if($message): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php echo $message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($products)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="fas fa-box-open fa-3x mb-3 d-block"></i>
                            No products yet. Click "Add New Product" to get started.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach($products as $product): ?>
                    <tr>
                        <td>
                            <?php if($product['image']): ?>
                            <img src="../assets/images/products/<?php echo $product['image']; ?>" 
                                 style="width: 50px; height: 50px; object-fit: cover;" class="rounded">
                            <?php else: ?>
                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-image text-muted"></i>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></td>
                        <td>UGX <?php echo number_format($product['price']); ?></td>
                        <td>
                            <span class="badge bg-<?php echo $product['stock'] > 10 ? 'success' : ($product['stock'] > 0 ? 'warning' : 'danger'); ?>">
                                <?php echo $product['stock']; ?>
                            </span>
                        </td>
                        <td>
                            <a href="?toggle=<?php echo $product['id']; ?>" class="badge bg-<?php echo $product['status'] == 'active' ? 'success' : 'secondary'; ?> text-decoration-none">
                                <?php echo ucfirst($product['status']); ?>
                            </a>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="editProduct(<?php echo $product['id']; ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="?delete=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="products.php?save=1" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3" required></textarea>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Price (UGX)</label>
                            <input type="number" class="form-control" name="price" step="1000" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Discount Price (UGX)</label>
                            <input type="number" class="form-control" name="discount_price" step="1000">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Stock Quantity</label>
                            <input type="number" class="form-control" name="stock" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Product Image</label>
                            <input type="file" class="form-control" name="image" accept="image/*" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Process add product
if(isset($_GET['save'])) {
    $name = $_POST['name'];
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    $description = $_POST['description'];
    $price = $_POST['price'];
    $discount_price = $_POST['discount_price'] ?: null;
    $stock = $_POST['stock'];
    $category_id = $_POST['category_id'];
    
    // Handle image upload
    $image_name = '';
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if(in_array($ext, $allowed)) {
            $image_name = time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], '../assets/images/products/' . $image_name);
        }
    }
    
    $db->insert('products', [
        'name' => $name,
        'slug' => $slug,
        'description' => $description,
        'price' => $price,
        'discount_price' => $discount_price,
        'stock' => $stock,
        'category_id' => $category_id,
        'vendor_id' => $vendor_id,
        'image' => $image_name,
        'status' => 'pending'
    ]);
    
    echo '<script>window.location.href="products.php?msg=Product added successfully";</script>';
    exit;
}
?>

<script>
function editProduct(id) {
    window.location.href = 'products.php?edit=' + id;
}
</script>

<?php require_once 'includes/vendor-footer.php'; ?>