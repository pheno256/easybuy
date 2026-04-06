<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$page_title = 'Promo Codes';
$db = Database::getInstance();
$message = '';

// Handle deletion
if(isset($_GET['delete'])) {
    $db->query("DELETE FROM coupons WHERE id = ?", [$_GET['delete']]);
    $message = "Promo code deleted!";
}

// Handle add/update
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = strtoupper($_POST['code']);
    $discount_type = $_POST['discount_type'];
    $discount_value = $_POST['discount_value'];
    $min_order = $_POST['min_order'] ?? 0;
    $usage_limit = $_POST['usage_limit'] ?: null;
    $expiry_date = $_POST['expiry_date'] ?: null;
    
    if(isset($_POST['coupon_id']) && $_POST['coupon_id']) {
        $db->query("
            UPDATE coupons 
            SET code=?, discount_type=?, discount_value=?, min_order_amount=?, usage_limit=?, expiry_date=? 
            WHERE id=?
        ", [$code, $discount_type, $discount_value, $min_order, $usage_limit, $expiry_date, $_POST['coupon_id']]);
        $message = "Promo code updated!";
    } else {
        $db->insert('coupons', [
            'code' => $code,
            'discount_type' => $discount_type,
            'discount_value' => $discount_value,
            'min_order_amount' => $min_order,
            'usage_limit' => $usage_limit,
            'expiry_date' => $expiry_date,
            'status' => 'active'
        ]);
        $message = "Promo code added!";
    }
}

$coupons = $db->query("SELECT * FROM coupons ORDER BY created_at DESC")->fetchAll();

require_once 'includes/admin-header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Promo Codes</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCouponModal">
        <i class="fas fa-plus"></i> Add Promo Code
    </button>
</div>

<?php if($message): ?>
<div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Discount</th>
                        <th>Min Order</th>
                        <th>Used</th>
                        <th>Limit</th>
                        <th>Expiry</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($coupons as $coupon): ?>
                    <tr>
                        <td><code><?php echo $coupon['code']; ?></code></td>
                        <td>
                            <?php if($coupon['discount_type'] == 'percentage'): ?>
                            <?php echo $coupon['discount_value']; ?>% OFF
                            <?php else: ?>
                            UGX <?php echo number_format($coupon['discount_value']); ?> OFF
                            <?php endif; ?>
                        </td>
                        <td>UGX <?php echo number_format($coupon['min_order_amount']); ?></td>
                        <td><?php echo $coupon['used_count']; ?></td>
                        <td><?php echo $coupon['usage_limit'] ?: '∞'; ?></td>
                        <td><?php echo $coupon['expiry_date'] ? date('M d, Y', strtotime($coupon['expiry_date'])) : 'Never'; ?></td>
                        <td>
                            <span class="badge bg-<?php echo $coupon['status'] == 'active' ? 'success' : 'danger'; ?>">
                                <?php echo ucfirst($coupon['status']); ?>
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="editCoupon(<?php echo htmlspecialchars(json_encode($coupon)); ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="?delete=<?php echo $coupon['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
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

<!-- Add/Edit Coupon Modal -->
<div class="modal fade" id="couponModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Promo Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="coupon_id" id="coupon_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Promo Code</label>
                        <input type="text" class="form-control" name="code" id="code" required>
                        <small class="text-muted">Use uppercase letters and numbers only</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Discount Type</label>
                        <select class="form-select" name="discount_type" id="discount_type" required>
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount (UGX)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Discount Value</label>
                        <input type="number" class="form-control" name="discount_value" id="discount_value" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Minimum Order Amount (UGX)</label>
                        <input type="number" class="form-control" name="min_order" id="min_order" value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Usage Limit</label>
                        <input type="number" class="form-control" name="usage_limit" id="usage_limit" placeholder="Leave empty for unlimited">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Expiry Date</label>
                        <input type="date" class="form-control" name="expiry_date" id="expiry_date">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Promo Code</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editCoupon(coupon) {
    document.getElementById('modalTitle').innerText = 'Edit Promo Code';
    document.getElementById('coupon_id').value = coupon.id;
    document.getElementById('code').value = coupon.code;
    document.getElementById('discount_type').value = coupon.discount_type;
    document.getElementById('discount_value').value = coupon.discount_value;
    document.getElementById('min_order').value = coupon.min_order_amount;
    document.getElementById('usage_limit').value = coupon.usage_limit || '';
    document.getElementById('expiry_date').value = coupon.expiry_date ? coupon.expiry_date.split(' ')[0] : '';
    new bootstrap.Modal(document.getElementById('couponModal')).show();
}

// Reset modal when opened for add
document.querySelector('[data-bs-target="#couponModal"]').addEventListener('click', function() {
    document.getElementById('modalTitle').innerText = 'Add Promo Code';
    document.getElementById('coupon_id').value = '';
    document.getElementById('code').value = '';
    document.getElementById('discount_value').value = '';
    document.getElementById('min_order').value = '0';
    document.getElementById('usage_limit').value = '';
    document.getElementById('expiry_date').value = '';
});
</script>

<?php require_once 'includes/admin-footer.php'; ?>