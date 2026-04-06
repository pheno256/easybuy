<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$page_title = 'Newsletter Subscribers';
$db = Database::getInstance();
$message = '';

// Handle unsubscribe
if(isset($_GET['unsubscribe'])) {
    $id = $_GET['unsubscribe'];
    $db->query("UPDATE newsletter SET status = 'unsubscribed' WHERE id = ?", [$id]);
    $message = "Subscriber unsubscribed!";
}

// Handle delete
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $db->query("DELETE FROM newsletter WHERE id = ?", [$id]);
    $message = "Subscriber deleted!";
}

// Send newsletter
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_newsletter'])) {
    $subject = $_POST['subject'];
    $content = $_POST['content'];
    
    // Get all active subscribers
    $subscribers = $db->query("SELECT email FROM newsletter WHERE status = 'active'")->fetchAll();
    
    $sent = 0;
    foreach($subscribers as $sub) {
        $to = $sub['email'];
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8\r\n";
        $headers .= "From: EasyBuy Uganda <newsletter@easybuy.ug>\r\n";
        
        if(mail($to, $subject, $content, $headers)) {
            $sent++;
        }
    }
    
    $message = "Newsletter sent to $sent subscribers!";
}

$subscribers = $db->query("SELECT * FROM newsletter ORDER BY subscribed_at DESC")->fetchAll();
$active_count = $db->query("SELECT COUNT(*) as count FROM newsletter WHERE status = 'active'")->fetch()['count'];

require_once 'includes/admin-header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Newsletter Subscribers</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sendNewsletterModal">
        <i class="fas fa-paper-plane"></i> Send Newsletter
    </button>
</div>

<?php if($message): ?>
<div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5>Total Subscribers</h5>
                <h2><?php echo count($subscribers); ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5>Active Subscribers</h5>
                <h2><?php echo $active_count; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h5>Unsubscribed</h5>
                <h2><?php echo count($subscribers) - $active_count; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5>Export</h5>
                <button class="btn btn-light btn-sm" onclick="exportSubscribers()">
                    <i class="fas fa-download"></i> Export CSV
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Subscribed Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($subscribers as $sub): ?>
                    <tr>
                        <td><?php echo $sub['id']; ?></td>
                        <td><?php echo htmlspecialchars($sub['email']); ?></td>
                        <td>
                            <span class="badge bg-<?php echo $sub['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                <?php echo ucfirst($sub['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($sub['subscribed_at'])); ?></td>
                        <td>
                            <?php if($sub['status'] == 'active'): ?>
                            <a href="?unsubscribe=<?php echo $sub['id']; ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-envelope-open-text"></i> Unsubscribe
                            </a>
                            <?php endif; ?>
                            <a href="?delete=<?php echo $sub['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Send Newsletter Modal -->
<div class="modal fade" id="sendNewsletterModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Newsletter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> This will be sent to <strong><?php echo $active_count; ?></strong> active subscribers.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" class="form-control" name="subject" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea class="form-control" name="content" rows="10" required></textarea>
                        <small class="text-muted">HTML is supported</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="send_newsletter" class="btn btn-primary" onclick="return confirm('Send newsletter to all subscribers?')">
                        <i class="fas fa-paper-plane"></i> Send Newsletter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function exportSubscribers() {
    let csv = "Email,Status,Subscribed Date\n";
    <?php foreach($subscribers as $sub): ?>
    csv += `<?php echo $sub['email']; ?>,<?php echo $sub['status']; ?>,<?php echo $sub['subscribed_at']; ?>\n`;
    <?php endforeach; ?>
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'subscribers.csv';
    a.click();
    URL.revokeObjectURL(url);
}
</script>

<?php require_once 'includes/admin-footer.php'; ?>