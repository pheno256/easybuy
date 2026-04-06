<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$ticket_id = $_GET['id'] ?? 0;
$db = Database::getInstance();

$ticket = $db->query("
    SELECT t.*, u.full_name, u.email 
    FROM support_tickets t 
    JOIN users u ON t.user_id = u.id 
    WHERE t.id = ?
", [$ticket_id])->fetch();

if(!$ticket) {
    header('Location: support.php');
    exit;
}

// Get messages
$messages = $db->query("
    SELECT m.*, u.full_name 
    FROM support_messages m
    LEFT JOIN users u ON m.user_id = u.id
    WHERE m.ticket_id = ?
    ORDER BY m.created_at ASC
", [$ticket_id])->fetchAll();

// Add reply
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['reply'])) {
        $message = $_POST['message'];
        
        $db->insert('support_messages', [
            'ticket_id' => $ticket_id,
            'user_id' => $_SESSION['admin_id'],
            'message' => $message,
            'is_admin' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        // Update ticket status
        $status = $_POST['status'] ?? 'in_progress';
        $db->query("UPDATE support_tickets SET status = ?, updated_at = NOW() WHERE id = ?", [$status, $ticket_id]);
        
        // Send email notification to customer
        $subject = "Re: " . $ticket['subject'];
        $email_body = "Hello {$ticket['full_name']},\n\n";
        $email_body .= "You have a new reply to your support ticket #{$ticket['ticket_number']}:\n\n";
        $email_body .= $message . "\n\n";
        $email_body .= "View your ticket: " . APP_URL . "/support-ticket.php?id={$ticket_id}\n\n";
        $email_body .= "Best regards,\nEasyBuy Support Team";
        
        mail($ticket['email'], $subject, $email_body);
        
        $success = "Reply sent successfully!";
    }
    
    if(isset($_POST['close_ticket'])) {
        $db->query("UPDATE support_tickets SET status = 'closed', updated_at = NOW() WHERE id = ?", [$ticket_id]);
        $success = "Ticket closed!";
    }
}

$page_title = 'Ticket #' . $ticket['ticket_number'];

require_once 'includes/admin-header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Ticket #<?php echo $ticket['ticket_number']; ?></h1>
    <a href="support.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Tickets
    </a>
</div>

<?php if(isset($success)): ?>
<div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Ticket Details</h5>
            </div>
            <div class="card-body">
                <p><strong>Customer:</strong> <?php echo htmlspecialchars($ticket['full_name']); ?> (<?php echo $ticket['email']; ?>)</p>
                <p><strong>Subject:</strong> <?php echo htmlspecialchars($ticket['subject']); ?></p>
                <p><strong>Priority:</strong> 
                    <span class="badge bg-<?php 
                        echo $ticket['priority'] == 'high' ? 'danger' : 
                            ($ticket['priority'] == 'medium' ? 'warning' : 'info'); 
                    ?>">
                        <?php echo ucfirst($ticket['priority']); ?>
                    </span>
                </p>
                <p><strong>Status:</strong> 
                    <span class="badge bg-<?php 
                        echo $ticket['status'] == 'closed' ? 'secondary' : 
                            ($ticket['status'] == 'in_progress' ? 'info' : 'warning'); 
                    ?>">
                        <?php echo ucfirst($ticket['status']); ?>
                    </span>
                </p>
                <p><strong>Created:</strong> <?php echo date('F d, Y H:i', strtotime($ticket['created_at'])); ?></p>
            </div>
        </div>
        
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Conversation</h5>
            </div>
            <div class="card-body">
                <?php foreach($messages as $msg): ?>
                <div class="message mb-3 <?php echo $msg['is_admin'] ? 'admin-message' : 'user-message'; ?>">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <strong>
                                    <?php if($msg['is_admin']): ?>
                                    <i class="fas fa-user-shield text-primary"></i> EasyBuy Support
                                    <?php else: ?>
                                    <i class="fas fa-user text-secondary"></i> <?php echo htmlspecialchars($msg['full_name']); ?>
                                    <?php endif; ?>
                                </strong>
                                <small class="text-muted"><?php echo date('M d, Y H:i', strtotime($msg['created_at'])); ?></small>
                            </div>
                            <p class="mb-0 mt-2"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Reply to Ticket</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea class="form-control" name="message" rows="5" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Update Status</label>
                        <select class="form-select" name="status">
                            <option value="open" <?php echo $ticket['status'] == 'open' ? 'selected' : ''; ?>>Open</option>
                            <option value="in_progress" <?php echo $ticket['status'] == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                            <option value="closed" <?php echo $ticket['status'] == 'closed' ? 'selected' : ''; ?>>Closed</option>
                        </select>
                    </div>
                    <button type="submit" name="reply" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-paper-plane"></i> Send Reply
                    </button>
                    <?php if($ticket['status'] != 'closed'): ?>
                    <button type="submit" name="close_ticket" class="btn btn-danger w-100" onclick="return confirm('Close this ticket?')">
                        <i class="fas fa-times-circle"></i> Close Ticket
                    </button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.user-message {
    background: #f3f4f6;
    padding: 15px;
    border-radius: 10px;
}

.admin-message {
    background: #dbeafe;
    padding: 15px;
    border-radius: 10px;
}
</style>

<?php require_once 'includes/admin-footer.php'; ?>