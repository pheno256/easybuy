<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$page_title = 'Manage Brands';
$db = Database::getInstance();
$message = '';

// Handle deletion
if(isset($_GET['delete'])) {
    $db->query("DELETE FROM brands WHERE id = ?", [$_GET['delete']]);
    $message = "Brand deleted!";
}

// Handle add/update
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    $description = $_POST['description'];
    
    $logo_name = '';
    if(isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
        $logo_name = time() . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['logo']['tmp_name'], '../assets/images/brands/' . $logo_name);
    }
    
    if(isset($_POST['brand_id']) && $_POST['brand_id']) {
        if($logo_name) {
            $db->query("UPDATE brands SET name=?, slug=?, description=?, logo=? WHERE id=?", 
                       [$name, $slug, $description, $logo_name, $_POST['brand_id']]);
        } else {
            $db->query("UPDATE brands SET name=?, slug=?, description=? WHERE id=?", 
                       [$name, $slug, $description, $_POST['brand_id']]);
        }
        $message = "Brand updated!";
    } else {
        $db->insert('brands', [
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'logo' => $logo_name,
            'status' => 'active'
        ]);
        $message = "Brand added!";
    }
}

$brands = $db->query("
    SELECT b.*, COUNT(p.id) as product_count 
    FROM brands b
    LEFT JOIN products p ON b.id = p.brand_id
    GROUP BY b.id
    ORDER BY b.name
")->fetchAll();

require_once 'includes/admin-header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Manage Brands</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#brandModal">
        <i class="fas fa-plus"></i> Add Brand
    </button>
</div>

<?php if($message): ?>
<div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>

<div class="row">
    <?php foreach($brands as $brand): ?>
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-body text-center">
                <?php if($brand['logo']): ?>
                <img src="../assets/images/brands/<?php echo $brand['logo']; ?>" 
                     style="height: 80px; object-fit: contain;" class="mb-3">
                <?php else: ?>
                <i class="fas fa-building fa-3x text-primary mb-3"></i>
                <?php endif; ?>
                <h5><?php echo htmlspecialchars($brand['name']); ?></h5>
                <p class="text-muted small"><?php echo $brand['product_count']; ?> products</p>
                <p><?php echo htmlspecialchars($brand['description']); ?></p>
            </div>
            <div class="card-footer bg-white">
                <button class="btn btn-sm btn-info" onclick="editBrand(<?php echo htmlspecialchars(json_encode($brand)); ?>)">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <a href="?delete=<?php echo $brand['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                    <i class="fas fa-trash"></i> Delete
                </a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Brand Modal -->
<div class="modal fade" id="brandModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Brand</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="brand_id" id="brand_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Brand Name</label>
                        <input type="text" class="form-control" name="name" id="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Brand Logo</label>
                        <input type="file" class="form-control" name="logo" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Brand</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editBrand(brand) {
    document.getElementById('modalTitle').innerText = 'Edit Brand';
    document.getElementById('brand_id').value = brand.id;
    document.getElementById('name').value = brand.name;
    document.getElementById('description').value = brand.description;
    new bootstrap.Modal(document.getElementById('brandModal')).show();
}

document.querySelector('[data-bs-target="#brandModal"]').addEventListener('click', function() {
    document.getElementById('modalTitle').innerText = 'Add Brand';
    document.getElementById('brand_id').value = '';
    document.getElementById('name').value = '';
    document.getElementById('description').value = '';
});
</script>

<?php require_once 'includes/admin-footer.php'; ?>