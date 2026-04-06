<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$page_title = 'Support Tickets';
$db = Database::getInstance();
$message = '';

// Update ticket status
if(isset($_POST['update_status'])) {
    $ticket_id = $_POST['ticket_id'];
    $status = $_POST['status'];
    $db->query("UPDATE support_tickets SET status = ? WHERE id = ?", [$status, $ticket_id]);
    $message = "Ticket status updated!";
}

// Get all tickets
$status_filter = $_GET['status'] ?? '';
$sql = "SELECT t.*, u.full_name, u.email FROM support_tickets t JOIN users u ON t.user_id = u.id";
if($status_filter) {
    $sql .= " WHERE t.status = ?";
    $params = [$status_filter];
    $tickets = $db->query($sql, $params)->fetchAll();
} else {
    $tickets = $db->query($sql . " ORDER BY t.created_at DESC")->fetchAll();
}

require_once 'includes/admin-header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Support Tickets</h1>
    <div>
        <a href="?status=open" class="btn btn-outline-warning btn-sm">Open</a>
        <a href="?status=in_progress" class="btn btn-outline-info btn-sm">In Progress</a>
        <a href="?status=closed" class="btn btn-outline-secondary btn-sm">Closed</a>
        <a href="support.php" class="btn btn-outline-primary btn-sm">All</a>
    </div>
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
                        <th>Ticket #</th>
                        <th>Customer</th>
                        <th>Subject</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($tickets as $ticket): ?>
                    <tr class="<?php echo $ticket['priority'] == 'high' ? 'table-danger' : ''; ?>">
                        <td><?php echo $ticket['ticket_number']; ?></td>
                        <td>
                            <?php echo htmlspecialchars($ticket['full_name']); ?><br>
                            <small><?php echo $ticket['email']; ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($ticket['subject']); ?></td>
                        <td>
                            <span class="badge bg-<?php 
                                echo $ticket['priority'] == 'high' ? 'danger' : 
                                    ($ticket['priority'] == 'medium' ? 'warning' : 'info'); 
                            ?>">
                                <?php echo ucfirst($ticket['priority']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?php 
                                echo $ticket['status'] == 'closed' ? 'secondary' : 
                                    ($ticket['status'] == 'in_progress' ? 'info' : 'warning'); 
                            ?>">
                                <?php echo ucfirst($ticket['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($ticket['created_at'])); ?></td>
                        <td>
                            <a href="support-ticket.php?id=<?php echo $ticket['id']; ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> View
                            </a>
                         </td>
                     </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/admin-footer.php'; ?>