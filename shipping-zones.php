<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$page_title = 'Shipping Zones';
$db = Database::getInstance();
$message = '';

// Handle deletion
if(isset($_GET['delete'])) {
    $db->query("DELETE FROM shipping_zones WHERE id = ?", [$_GET['delete']]);
    $message = "Shipping zone deleted!";
}

// Handle add/update
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $zone_name = $_POST['zone_name'];
    $districts = implode(',', $_POST['districts'] ?? []);
    $delivery_fee = $_POST['delivery_fee'];
    $delivery_days = $_POST['delivery_days'];
    
    if(isset($_POST['zone_id']) && $_POST['zone_id']) {
        $db->query("
            UPDATE shipping_zones 
            SET zone_name=?, districts=?, delivery_fee=?, delivery_days=? 
            WHERE id=?
        ", [$zone_name, $districts, $delivery_fee, $delivery_days, $_POST['zone_id']]);
        $message = "Shipping zone updated!";
    } else {
        $db->insert('shipping_zones', [
            'zone_name' => $zone_name,
            'districts' => $districts,
            'delivery_fee' => $delivery_fee,
            'delivery_days' => $delivery_days
        ]);
        $message = "Shipping zone added!";
    }
}

$zones = $db->query("SELECT * FROM shipping_zones ORDER BY delivery_fee")->fetchAll();
$districts = $db->query("SELECT * FROM uganda_districts ORDER BY district_name")->fetchAll();

require_once 'includes/admin-header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Shipping Zones</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#zoneModal">
        <i class="fas fa-plus"></i> Add Shipping Zone
    </button>
</div>

<?php if($message): ?>
<div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>

<div class="row">
    <?php foreach($zones as $zone): 
        $zone_districts = explode(',', $zone['districts']);
    ?>
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0"><?php echo htmlspecialchars($zone['zone_name']); ?></h5>
            </div>
            <div class="card-body">
                <p><strong>Delivery Fee:</strong> UGX <?php echo number_format($zone['delivery_fee']); ?></p>
                <p><strong>Delivery Time:</strong> <?php echo $zone['delivery_days']; ?> business days</p>
                <p><strong>Districts:</strong> <?php echo count($zone_districts); ?> districts</p>
                <details>
                    <summary>View Districts</summary>
                    <div class="mt-2">
                        <?php foreach($zone_districts as $d): ?>
                        <span class="badge bg-secondary m-1"><?php echo $d; ?></span>
                        <?php endforeach; ?>
                    </div>
                </details>
            </div>
            <div class="card-footer bg-white">
                <button class="btn btn-sm btn-info" onclick="editZone(<?php echo htmlspecialchars(json_encode($zone)); ?>)">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <a href="?delete=<?php echo $zone['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                    <i class="fas fa-trash"></i> Delete
                </a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Add/Edit Zone Modal -->
<div class="modal fade" id="zoneModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Shipping Zone</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="zone_id" id="zone_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Zone Name</label>
                        <input type="text" class="form-control" name="zone_name" id="zone_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Districts in this Zone</label>
                        <select class="form-select" name="districts[]" id="districts" multiple size="10" required>
                            <?php foreach($districts as $district): ?>
                            <option value="<?php echo $district['district_name']; ?>">
                                <?php echo $district['district_name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Hold Ctrl/Cmd to select multiple districts</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Delivery Fee (UGX)</label>
                        <input type="number" class="form-control" name="delivery_fee" id="delivery_fee" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Delivery Time (business days)</label>
                        <input type="text" class="form-control" name="delivery_days" id="delivery_days" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Zone</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editZone(zone) {
    document.getElementById('modalTitle').innerText = 'Edit Shipping Zone';
    document.getElementById('zone_id').value = zone.id;
    document.getElementById('zone_name').value = zone.zone_name;
    document.getElementById('delivery_fee').value = zone.delivery_fee;
    document.getElementById('delivery_days').value = zone.delivery_days;
    
    // Select districts
    const selectedDistricts = zone.districts.split(',');
    const districtSelect = document.getElementById('districts');
    for(let i = 0; i < districtSelect.options.length; i++) {
        districtSelect.options[i].selected = selectedDistricts.includes(districtSelect.options[i].value);
    }
    
    new bootstrap.Modal(document.getElementById('zoneModal')).show();
}

// Reset modal when opened for add
document.querySelector('[data-bs-target="#zoneModal"]').addEventListener('click', function() {
    document.getElementById('modalTitle').innerText = 'Add Shipping Zone';
    document.getElementById('zone_id').value = '';
    document.getElementById('zone_name').value = '';
    document.getElementById('delivery_fee').value = '';
    document.getElementById('delivery_days').value = '';
    const districtSelect = document.getElementById('districts');
    for(let i = 0; i < districtSelect.options.length; i++) {
        districtSelect.options[i].selected = false;
    }
});
</script>

<?php require_once 'includes/admin-footer.php'; ?>