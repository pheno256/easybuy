<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$page_title = 'Manage Products';
$db = Database::getInstance();
$message = '';
$error = '';

// Handle product deletion
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $db->query("DELETE FROM products WHERE id = ?", [$id]);
    $message = "Product deleted successfully!";
}

// Handle product status toggle
if(isset($_GET['toggle'])) {
    $id = $_GET['toggle'];
    $product = $db->query("SELECT status FROM products WHERE id = ?", [$id])->fetch();
    $new_status = $product['status'] == 'active' ? 'inactive' : 'active';
    $db->query("UPDATE products SET status = ? WHERE id = ?", [$new_status, $id]);
    $message = "Product status updated!";
}

// Get all products
$products = $db->query("
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    ORDER BY p.created_at DESC
")->fetchAll();

// Get categories for filter
$categories = $db->query("SELECT * FROM categories ORDER BY name")->fetchAll();

require_once 'includes/admin-header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Manage Products</h1>
    <a href="products.php?action=add" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Product
    </a>
</div>

<?php if($message): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php echo $message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if(isset($_GET['action']) && $_GET['action'] == 'add'): ?>
<!-- Add/Edit Product Form -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><?php echo isset($_GET['edit']) ? 'Edit Product' : 'Add New Product'; ?></h5>
    </div>
    <div class="card-body">
        <?php
        $product = null;
        if(isset($_GET['edit'])) {
            $product = $db->query("SELECT * FROM products WHERE id = ?", [$_GET['edit']])->fetch();
        }
        ?>
        <form method="POST" action="products.php?save=1" enctype="multipart/form-data">
            <?php if($product): ?>
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Product Name</label>
                    <input type="text" class="form-control" name="name" required 
                           value="<?php echo $product ? htmlspecialchars($product['name']) : ''; ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Category</label>
                    <select class="form-select" name="category_id" required>
                        <option value="">Select Category</option>
                        <?php foreach($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"
                            <?php echo ($product && $product['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="4" required><?php echo $product ? htmlspecialchars($product['description']) : ''; ?></textarea>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Price (UGX)</label>
                    <input type="number" class="form-control" name="price" step="1000" required 
                           value="<?php echo $product ? $product['price'] : ''; ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Discount Price (UGX)</label>
                    <input type="number" class="form-control" name="discount_price" step="1000" 
                           value="<?php echo $product ? $product['discount_price'] : ''; ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Stock Quantity</label>
                    <input type="number" class="form-control" name="stock" required 
                           value="<?php echo $product ? $product['stock'] : '0'; ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Main Image</label>
                    <input type="file" class="form-control" name="image" accept="image/*" 
                           <?php echo !$product ? 'required' : ''; ?>>
                    <?php if($product && $product['image']): ?>
                    <div class="mt-2">
                        <img src="../assets/images/products/<?php echo $product['image']; ?>" 
                             style="width: 100px; height: 100px; object-fit: cover;">
                    </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Additional Images (Multiple)</label>
                    <input type="file" class="form-control" name="images[]" multiple accept="image/*">
                </div>
                <div class="col-md-12 mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="featured" value="1" 
                               <?php echo ($product && $product['featured']) ? 'checked' : ''; ?>>
                        <label class="form-check-label">Featured Product</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="trending" value="1"
                               <?php echo ($product && $product['trending']) ? 'checked' : ''; ?>>
                        <label class="form-check-label">Trending Product</label>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Product
            </button>
            <a href="products.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php elseif(isset($_POST['save'])): ?>
<?php
// Process product save
$name = $_POST['name'];
$slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
$description = $_POST['description'];
$price = $_POST['price'];
$discount_price = $_POST['discount_price'] ?: null;
$stock = $_POST['stock'];
$category_id = $_POST['category_id'];
$featured = isset($_POST['featured']) ? 1 : 0;
$trending = isset($_POST['trending']) ? 1 : 0;

// Handle main image upload
$image_name = '';
if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $_FILES['image']['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if(in_array($ext, $allowed)) {
        $image_name = time() . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], '../assets/images/products/' . $image_name);
    }
}

if(isset($_POST['product_id'])) {
    // Update existing product
    $id = $_POST['product_id'];
    if($image_name) {
        $db->query("UPDATE products SET name=?, slug=?, description=?, price=?, discount_price=?, stock=?, category_id=?, featured=?, trending=?, image=? WHERE id=?", 
                   [$name, $slug, $description, $price, $discount_price, $stock, $category_id, $featured, $trending, $image_name, $id]);
    } else {
        $db->query("UPDATE products SET name=?, slug=?, description=?, price=?, discount_price=?, stock=?, category_id=?, featured=?, trending=? WHERE id=?", 
                   [$name, $slug, $description, $price, $discount_price, $stock, $category_id, $featured, $trending, $id]);
    }
    $message = "Product updated successfully!";
} else {
    // Insert new product
    $db->insert('products', [
        'name' => $name,
        'slug' => $slug,
        'description' => $description,
        'price' => $price,
        'discount_price' => $discount_price,
        'stock' => $stock,
        'category_id' => $category_id,
        'featured' => $featured,
        'trending' => $trending,
        'image' => $image_name,
        'status' => 'active'
    ]);
    $message = "Product added successfully!";
}
header("Location: products.php?msg=" . urlencode($message));
exit;
?>

<?php else: ?>
<!-- Products List -->
<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover data-table">
                <thead>
                    <tr>
                        <th>ID</th>
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
                    <?php foreach($products as $product): ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td>
                            <?php if($product['image']): ?>
                            <img src="../assets/images/products/<?php echo $product['image']; ?>" 
                                 style="width: 50px; height: 50px; object-fit: cover;" class="rounded">
                            <?php else: ?>
                            <div class="bg-light rounded" style="width: 50px; height: 50px;"></div>
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
                            <a href="?action=add&edit=<?php echo $product['id']; ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="?delete=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once 'includes/admin-footer.php'; ?>