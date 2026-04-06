<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$page_title = 'System Health Check';
$db = Database::getInstance();

// Check database connection
$db_status = 'OK';
try {
    $db->query("SELECT 1");
} catch(Exception $e) {
    $db_status = 'Error: ' . $e->getMessage();
}

// Check required directories
$directories = [
    '../logs/' => is_writable('../logs/'),
    '../tmp/' => is_writable('../tmp/'),
    '../public/assets/uploads/' => is_writable('../public/assets/uploads/'),
    '../backups/' => is_writable('../backups/') || mkdir('../backups/', 0777, true),
];

// Check PHP extensions
$extensions = [
    'PDO' => extension_loaded('pdo'),
    'MySQLi' => extension_loaded('mysqli'),
    'JSON' => extension_loaded('json'),
    'OpenSSL' => extension_loaded('openssl'),
    'cURL' => extension_loaded('curl'),
    'GD' => extension_loaded('gd'),
    'MBString' => extension_loaded('mbstring'),
];

// Get system info
$php_version = PHP_VERSION;
$server_software = $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown';
$mysql_version = $db->query("SELECT VERSION() as version")->fetch()['version'];

// Check table counts
$tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
$table_counts = [];
foreach($tables as $table) {
    $count = $db->query("SELECT COUNT(*) as count FROM $table")->fetch()['count'];
    $table_counts[$table] = $count;
}

require_once 'includes/admin-header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">System Health Check</h1>
    <button class="btn btn-primary" onclick="location.reload()">
        <i class="fas fa-sync-alt"></i> Refresh
    </button>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">System Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th>PHP Version:</th>
                        <td>
                            <?php echo $php_version; ?>
                            <?php if(version_compare($php_version, '7.4', '<')): ?>
                            <span class="badge bg-danger">Update Required</span>
                            <?php else: ?>
                            <span class="badge bg-success">OK</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>MySQL Version:</th>
                        <td><?php echo $mysql_version; ?></td>
                    </tr>
                    <tr>
                        <th>Server Software:</th>
                        <td><?php echo $server_software; ?></td>
                    </tr>
                    <tr>
                        <th>Database Status:</th>
                        <td>
                            <span class="badge bg-<?php echo $db_status == 'OK' ? 'success' : 'danger'; ?>">
                                <?php echo $db_status; ?>
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Directory Permissions</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <?php foreach($directories as $dir => $writable): ?>
                    <tr>
                        <th><?php echo $dir; ?></th>
                        <td>
                            <span class="badge bg-<?php echo $writable ? 'success' : 'danger'; ?>">
                                <?php echo $writable ? 'Writable' : 'Not Writable'; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">PHP Extensions</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <?php foreach($extensions as $ext => $loaded): ?>
                    <tr>
                        <th><?php echo $ext; ?></th>
                        <td>
                            <span class="badge bg-<?php echo $loaded ? 'success' : 'danger'; ?>">
                                <?php echo $loaded ? 'Loaded' : 'Missing'; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Database Statistics</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <?php foreach($table_counts as $table => $count): ?>
                    <tr>
                        <th><?php echo $table; ?></th>
                        <td><?php echo number_format($count); ?> records</td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Recommended Actions</h5>
    </div>
    <div class="card-body">
        <ul>
            <?php if(version_compare($php_version, '7.4', '<')): ?>
            <li class="text-danger">⚠️ Upgrade PHP to version 7.4 or higher for better security and performance</li>
            <?php endif; ?>
            
            <?php if(!$directories['../logs/']): ?>
            <li class="text-danger">⚠️ Make logs directory writable: chmod 755 logs/</li>
            <?php endif; ?>
            
            <?php if(!$directories['../backups/']): ?>
            <li class="text-warning">⚠️ Create backups directory for database backups</li>
            <?php endif; ?>
            
            <?php foreach($extensions as $ext => $loaded): ?>
            <?php if(!$loaded): ?>
            <li class="text-danger">⚠️ Install PHP extension: <?php echo $ext; ?></li>
            <?php endif; ?>
            <?php endforeach; ?>
            
            <li class="text-success">✅ Regular backups recommended (weekly)</li>
            <li class="text-success">✅ Enable HTTPS for production environment</li>
        </ul>
    </div>
</div>

<?php require_once 'includes/admin-footer.php'; ?>