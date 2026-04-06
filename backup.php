<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$db = Database::getInstance();
$message = '';

// Create backup
if(isset($_GET['create'])) {
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    $backup = "-- EasyBuy Uganda Database Backup\n";
    $backup .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
    
    foreach($tables as $table) {
        // Get create table syntax
        $create = $db->query("SHOW CREATE TABLE $table")->fetch();
        $backup .= $create['Create Table'] . ";\n\n";
        
        // Get data
        $rows = $db->query("SELECT * FROM $table")->fetchAll();
        foreach($rows as $row) {
            $columns = array_keys($row);
            $values = array_map(function($val) use ($db) {
                return $db->getConnection()->quote($val);
            }, array_values($row));
            $backup .= "INSERT INTO $table (" . implode(',', $columns) . ") VALUES (" . implode(',', $values) . ");\n";
        }
        $backup .= "\n";
    }
    
    $filename = '../backups/backup_' . date('Y-m-d_H-i-s') . '.sql';
    file_put_contents($filename, $backup);
    $message = "Backup created successfully!";
}

// Get existing backups
$backups = glob('../backups/backup_*.sql');
rsort($backups);

require_once 'includes/admin-header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Database Backup</h1>
    <a href="?create=1" class="btn btn-primary" onclick="return confirm('Create database backup?')">
        <i class="fas fa-database"></i> Create Backup
    </a>
</div>

<?php if($message): ?>
<div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Available Backups</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Backup File</th>
                        <th>Size</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($backups as $backup): ?>
                    <tr>
                        <td><?php echo basename($backup); ?></td>
                        <td><?php echo round(filesize($backup) / 1024, 2); ?> KB</td>
                        <td><?php echo date('M d, Y H:i:s', filemtime($backup)); ?></td>
                        <td>
                            <a href="<?php echo $backup; ?>" class="btn btn-sm btn-success" download>
                                <i class="fas fa-download"></i> Download
                            </a>
                            <a href="?restore=<?php echo urlencode($backup); ?>" class="btn btn-sm btn-warning" onclick="return confirm('Restore database from this backup? This will overwrite current data.')">
                                <i class="fas fa-undo"></i> Restore
                            </a>
                            <a href="?delete=<?php echo urlencode($backup); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this backup?')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($backups)): ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted">No backups found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">Backup Information</h5>
    </div>
    <div class="card-body">
        <ul>
            <li>Backups include all database tables and data</li>
            <li>Backups are stored in the <code>/backups</code> folder</li>
            <li>Regular backups are recommended (weekly)</li>
            <li>Download backups for safekeeping</li>
            <li>Restoring will overwrite current database</li>
        </ul>
    </div>
</div>

<?php require_once 'includes/admin-footer.php'; ?>