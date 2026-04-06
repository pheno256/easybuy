<?php
require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$page_title = 'Customer Support';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$db = Database::getInstance();

// Get support tickets
$tickets = $db->query("
    SELECT * FROM support_tickets 
    WHERE user_id = ? 
    ORDER BY created_at DESC
", [$_SESSION['user_id']])->fetchAll();

// Create new ticket
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_ticket'])) {
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $priority = $_POST['priority'];
    
    $ticket_id = $db->insert('support_tickets', [
        'user_id' => $_SESSION['user_id'],
        'ticket_number' => 'TKT-' . strtoupper(uniqid()),
        'subject' => $subject,
        'priority' => $priority,
        'status' => 'open',
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
    $db->insert('support_messages', [
        'ticket_id' => $ticket_id,
        'user_id' => $_SESSION['user_id'],
        'message' => $message,
        'is_admin' => 0,
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
    $success = "Ticket created successfully! Ticket #: TKT-" . $ticket_id;
}

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="text-center mb-5">
                <i class="fas fa-headset fa-4x text-primary mb-3"></i>
                <h1>Customer Support</h1>
                <p class="lead">We're here to help you 24/7</p>
            </div>
            
            <?php if(isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card text-center h-100 shadow-sm">
                        <div class="card-body">
                            <i class="fas fa-phone fa-3x text-primary mb-3"></i>
                            <h5>Call Us</h5>
                            <p>+256 700 000 000<br>+256 800 000 000</p>
                            <small class="text-muted">Mon-Sat: 8AM-8PM</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card text-center h-100 shadow-sm">
                        <div class="card-body">
                            <i class="fab fa-whatsapp fa-3x text-primary mb-3"></i>
                            <h5>WhatsApp</h5>
                            <p>+256 700 000 000</p>
                            <small class="text-muted">Instant messaging support</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card text-center h-100 shadow-sm">
                        <div class="card-body">
                            <i class="fas fa-envelope fa-3x text-primary mb-3"></i>
                            <h5>Email</h5>
                            <p>support@easybuy.ug</p>
                            <small class="text-muted">Response within 24 hours</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">My Support Tickets</h5>
                </div>
                <div class="card-body">
                    <?php if(empty($tickets)): ?>
                    <p class="text-muted text-center">No tickets yet. Create a new support ticket below.</p>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Ticket #</th>
                                    <th>Subject</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($tickets as $ticket): ?>
                                <tr>
                                    <td><?php echo $ticket['ticket_number']; ?></td>
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
                                            echo $ticket['status'] == 'closed' ? 'secondary' : 'success'; 
                                        ?>">
                                            <?php echo ucfirst($ticket['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($ticket['created_at'])); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="viewTicket(<?php echo $ticket['id']; ?>)">
                                            View
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Create New Support Ticket</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input type="text" class="form-control" name="subject" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Priority</label>
                            <select class="form-select" name="priority" required>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea class="form-control" name="message" rows="5" required></textarea>
                        </div>
                        <button type="submit" name="create_ticket" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Submit Ticket
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function viewTicket(ticketId) {
    window.location.href = 'support-ticket.php?id=' + ticketId;
}
</script>

<?php require_once 'includes/footer.php'; ?>