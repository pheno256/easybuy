<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$page_title = 'Manage Categories';
$db = Database::getInstance();
$message = '';

// Handle category deletion
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $db->query("DELETE FROM categories WHERE id = ?", [$id]);
    $message = "Category deleted successfully!";
}

// Get all categories
$categories = $db->query("SELECT * FROM categories ORDER BY name")->fetchAll();

require_once 'includes/admin-header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Manage Categories</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
        <i class="fas fa-plus"></i> Add Category
    </button>
</div>

<?php if($message): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php echo $message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row">
    <?php foreach($categories as $category): ?>
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="card-title"><?php echo htmlspecialchars($category['name']); ?></h5>
                        <p class="card-text text-muted small">Slug: <?php echo $category['slug']; ?></p>
                        <p class="card-text"><?php echo htmlspecialchars($category['description']); ?></p>
                    </div>
                    <?php if($category['image']): ?>
                    <img src="../assets/images/categories/<?php echo $category['image']; ?>" 
                         style="width: 60px; height: 60px; object-fit: cover;" class="rounded">
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-footer bg-white">
                <button class="btn btn-sm btn-info" onclick="editCategory(<?php echo $category['id']; ?>, '<?php echo htmlspecialchars($category['name']); ?>', '<?php echo htmlspecialchars($category['slug']); ?>', '<?php echo htmlspecialchars($category['description']); ?>')">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <a href="?delete=<?php echo $category['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure? This will affect products in this category.')">
                    <i class="fas fa-trash"></i> Delete
                </a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="categories.php?save=1" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Slug (URL friendly)</label>
                        <input type="text" class="form-control" name="slug" required>
                        <small class="text-muted">e.g., electronics, fashion</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category Image</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="categories.php?update=1" enctype="multipart/form-data">
                <input type="hidden" name="category_id" id="edit_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" class="form-control" name="name" id="edit_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" class="form-control" name="slug" id="edit_slug" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Image (optional)</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Process add category
if(isset($_GET['save'])) {
    $name = $_POST['name'];
    $slug = $_POST['slug'];
    $description = $_POST['description'];
    
    $image_name = '';
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $image_name = time() . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], '../assets/images/categories/' . $image_name);
    }
    
    $db->insert('categories', [
        'name' => $name,
        'slug' => $slug,
        'description' => $description,
        'image' => $image_name
    ]);
    header("Location: categories.php?msg=Category added");
    exit;
}

// Process update category
if(isset($_GET['update'])) {
    $id = $_POST['category_id'];
    $name = $_POST['name'];
    $slug = $_POST['slug'];
    $description = $_POST['description'];
    
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $image_name = time() . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], '../assets/images/categories/' . $image_name);
        $db->query("UPDATE categories SET name=?, slug=?, description=?, image=? WHERE id=?", 
                   [$name, $slug, $description, $image_name, $id]);
    } else {
        $db->query("UPDATE categories SET name=?, slug=?, description=? WHERE id=?", 
                   [$name, $slug, $description, $id]);
    }
    header("Location: categories.php?msg=Category updated");
    exit;
}
?>

<script>
function editCategory(id, name, slug, description) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_slug').value = slug;
    document.getElementById('edit_description').value = description;
    new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
}
</script>

<?php require_once 'includes/admin-footer.php'; ?>