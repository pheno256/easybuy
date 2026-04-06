<?php
require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$ticket_id = $_GET['id'] ?? 0;
$db = Database::getInstance();

$ticket = $db->query("
    SELECT * FROM support_tickets 
    WHERE id = ? AND user_id = ?
", [$ticket_id, $_SESSION['user_id']])->fetch();

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
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reply'])) {
    $message = $_POST['message'];
    
    $db->insert('support_messages', [
        'ticket_id' => $ticket_id,
        'user_id' => $_SESSION['user_id'],
        'message' => $message,
        'is_admin' => 0,
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
    // Update ticket status
    $db->query("UPDATE support_tickets SET status = 'open', updated_at = NOW() WHERE id = ?", [$ticket_id]);
    
    header("Location: support-ticket.php?id=$ticket_id");
    exit;
}

$page_title = 'Ticket #' . $ticket['ticket_number'];

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="mb-4">
                <a href="support.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Back to Support
                </a>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Ticket #<?php echo $ticket['ticket_number']; ?></h5>
                        <span class="badge bg-<?php 
                            echo $ticket['priority'] == 'high' ? 'danger' : 
                                ($ticket['priority'] == 'medium' ? 'warning' : 'info'); 
                        ?>">
                            <?php echo ucfirst($ticket['priority']); ?> Priority
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <h6><?php echo htmlspecialchars($ticket['subject']); ?></h6>
                    <p class="text-muted small">
                        Status: <span class="badge bg-<?php echo $ticket['status'] == 'closed' ? 'secondary' : 'success'; ?>">
                            <?php echo ucfirst($ticket['status']); ?>
                        </span>
                    </p>
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
                                    <strong><?php echo $msg['is_admin'] ? 'EasyBuy Support' : htmlspecialchars($msg['full_name']); ?></strong>
                                    <small class="text-muted"><?php echo date('M d, Y H:i', strtotime($msg['created_at'])); ?></small>
                                </div>
                                <p class="mb-0 mt-2"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <?php if($ticket['status'] != 'closed'): ?>
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Add Reply</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <textarea class="form-control" name="message" rows="4" required placeholder="Type your message here..."></textarea>
                        </div>
                        <button type="submit" name="reply" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Send Reply
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
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
    background: #e0f2fe;
    padding: 15px;
    border-radius: 10px;
}
</style>

<?php require_once 'includes/footer.php'; ?>