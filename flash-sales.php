<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$page_title = 'Manage Flash Sales';
$db = Database::getInstance();
$message = '';

// Handle deletion
if(isset($_GET['delete'])) {
    $db->query("DELETE FROM flash_sales WHERE id = ?", [$_GET['delete']]);
    $message = "Flash sale deleted!";
}

// Handle add/update
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $discount_percentage = $_POST['discount_percentage'];
    
    if(isset($_POST['flash_sale_id']) && $_POST['flash_sale_id']) {
        $db->query("
            UPDATE flash_sales 
            SET title=?, description=?, start_date=?, end_date=?, discount_percentage=? 
            WHERE id=?
        ", [$title, $description, $start_date, $end_date, $discount_percentage, $_POST['flash_sale_id']]);
        $message = "Flash sale updated!";
    } else {
        $db->insert('flash_sales', [
            'title' => $title,
            'description' => $description,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'discount_percentage' => $discount_percentage,
            'status' => 'active'
        ]);
        $message = "Flash sale added!";
    }
}

// Handle product assignment
if(isset($_GET['add_product'])) {
    $sale_id = $_GET['sale_id'];
    $product_id = $_GET['product_id'];
    $db->insert('flash_sale_products', [
        'flash_sale_id' => $sale_id,
        'product_id' => $product_id
    ]);
    $message = "Product added to flash sale!";
}

if(isset($_GET['remove_product'])) {
    $db->query("DELETE FROM flash_sale_products WHERE id = ?", [$_GET['remove_product']]);
    $message = "Product removed from flash sale!";
}

$flash_sales = $db->query("SELECT * FROM flash_sales ORDER BY created_at DESC")->fetchAll();
$products = $db->query("SELECT id, name FROM products WHERE status = 'active'")->fetchAll();

require_once 'includes/admin-header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Flash Sales</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#flashSaleModal">
        <i class="fas fa-bolt"></i> Create Flash Sale
    </button>
</div>

<?php if($message): ?>
<div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>

<div class="row">
    <?php foreach($flash_sales as $sale):
        $sale_products = $db->query("
            SELECT p.*, fsp.id as link_id 
            FROM flash_sale_products fsp
            JOIN products p ON fsp.product_id = p.id
            WHERE fsp.flash_sale_id = ?
        ", [$sale['id']])->fetchAll();
    ?>
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><?php echo htmlspecialchars($sale['title']); ?></h5>
            </div>
            <div class="card-body">
                <p><?php echo htmlspecialchars($sale['description']); ?></p>
                <p><strong>Discount:</strong> <?php echo $sale['discount_percentage']; ?>% OFF</p>
                <p><strong>Duration:</strong> <?php echo date('M d, Y H:i', strtotime($sale['start_date'])); ?> - <?php echo date('M d, Y H:i', strtotime($sale['end_date'])); ?></p>
                <p><strong>Status:</strong> 
                    <span class="badge bg-<?php 
                        echo $sale['status'] == 'active' && strtotime($sale['end_date']) > time() ? 'success' : 'secondary'; 
                    ?>">
                        <?php echo $sale['status'] == 'active' && strtotime($sale['end_date']) > time() ? 'Active' : 'Expired'; ?>
                    </span>
                </p>
                
                <h6>Products in this sale (<?php echo count($sale_products); ?>)</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <tbody>
                            <?php foreach($sale_products as $sp): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($sp['name']); ?></td>
                                <td>
                                    <a href="?remove_product=<?php echo $sp['link_id']; ?>&sale_id=<?php echo $sale['id']; ?>" 
                                       class="btn btn-sm btn-danger" onclick="return confirm('Remove product?')">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    <select class="form-select" id="product_<?php echo $sale['id']; ?>">
                        <option value="">Add product to sale...</option>
                        <?php foreach($products as $p): ?>
                        <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-sm btn-primary mt-2" onclick="addProductToSale(<?php echo $sale['id']; ?>)">
                        <i class="fas fa-plus"></i> Add Product
                    </button>
                </div>
                
                <div class="mt-3">
                    <button class="btn btn-sm btn-info" onclick="editFlashSale(<?php echo htmlspecialchars(json_encode($sale)); ?>)">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <a href="?delete=<?php echo $sale['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this flash sale?')">
                        <i class="fas fa-trash"></i> Delete
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Flash Sale Modal -->
<div class="modal fade" id="flashSaleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Create Flash Sale</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="flash_sale_id" id="flash_sale_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Sale Title</label>
                        <input type="text" class="form-control" name="title" id="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="description" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Discount Percentage</label>
                        <input type="number" class="form-control" name="discount_percentage" id="discount_percentage" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Start Date & Time</label>
                        <input type="datetime-local" class="form-control" name="start_date" id="start_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">End Date & Time</label>
                        <input type="datetime-local" class="form-control" name="end_date" id="end_date" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Flash Sale</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function addProductToSale(saleId) {
    const productId = document.getElementById('product_' + saleId).value;
    if(productId) {
        window.location.href = `?add_product=1&sale_id=${saleId}&product_id=${productId}`;
    }
}

function editFlashSale(sale) {
    document.getElementById('modalTitle').innerText = 'Edit Flash Sale';
    document.getElementById('flash_sale_id').value = sale.id;
    document.getElementById('title').value = sale.title;
    document.getElementById('description').value = sale.description;
    document.getElementById('discount_percentage').value = sale.discount_percentage;
    document.getElementById('start_date').value = sale.start_date.replace(' ', 'T').slice(0, 16);
    document.getElementById('end_date').value = sale.end_date.replace(' ', 'T').slice(0, 16);
    new bootstrap.Modal(document.getElementById('flashSaleModal')).show();
}

document.querySelector('[data-bs-target="#flashSaleModal"]').addEventListener('click', function() {
    document.getElementById('modalTitle').innerText = 'Create Flash Sale';
    document.getElementById('flash_sale_id').value = '';
    document.getElementById('title').value = '';
    document.getElementById('description').value = '';
    document.getElementById('discount_percentage').value = '';
    document.getElementById('start_date').value = '';
    document.getElementById('end_date').value = '';
});
</script>

<?php require_once 'includes/admin-footer.php'; ?>