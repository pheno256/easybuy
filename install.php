<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyBuy Uganda - Installation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 50px 0;
        }
        .install-card {
            border-radius: 15px;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card install-card shadow-lg">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <i class="fas fa-shopping-bag fa-3x mb-2"></i>
                        <h2 class="mb-0">EasyBuy Uganda Installation</h2>
                        <p class="mb-0">Complete E-Commerce System Setup</p>
                    </div>
                    <div class="card-body p-4">
                        <?php
                        $step = $_GET['step'] ?? 1;
                        
                        if($step == 1):
                        ?>
                        <h4>Step 1: System Requirements Check</h4>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Checking your server configuration...
                        </div>
                        
                        <?php
                        $requirements = [
                            'PHP Version >= 7.4' => version_compare(PHP_VERSION, '7.4', '>='),
                            'PDO Extension' => extension_loaded('pdo'),
                            'MySQLi Extension' => extension_loaded('mysqli'),
                            'JSON Extension' => extension_loaded('json'),
                            'OpenSSL Extension' => extension_loaded('openssl'),
                            'MBString Extension' => extension_loaded('mbstring'),
                            'cURL Extension' => extension_loaded('curl'),
                            'GD Extension' => extension_loaded('gd'),
                        ];
                        
                        $all_passed = true;
                        ?>
                        
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr><th>Requirement</th><th>Status</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach($requirements as $req => $passed): 
                                        if(!$passed) $all_passed = false;
                                    ?>
                                    <tr class="<?php echo $passed ? 'table-success' : 'table-danger'; ?>">
                                        <td><?php echo $req; ?></td>
                                        <td>
                                            <?php if($passed): ?>
                                            <i class="fas fa-check-circle text-success"></i> Passed
                                            <?php else: ?>
                                            <i class="fas fa-times-circle text-danger"></i> Failed
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <?php if($all_passed): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> All requirements met!
                        </div>
                        <a href="?step=2" class="btn btn-primary">Continue to Database Setup</a>
                        <?php else: ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> Please fix the failed requirements before continuing.
                        </div>
                        <?php endif; ?>
                        
                        <?php elseif($step == 2): ?>
                        <h4>Step 2: Database Configuration</h4>
                        
                        <?php
                        if($_SERVER['REQUEST_METHOD'] == 'POST') {
                            $host = $_POST['host'];
                            $name = $_POST['name'];
                            $user = $_POST['user'];
                            $pass = $_POST['pass'];
                            
                            try {
                                $pdo = new PDO("mysql:host=$host", $user, $pass);
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                
                                // Create database
                                $pdo->exec("CREATE DATABASE IF NOT EXISTS `$name`");
                                $pdo->exec("USE `$name`");
                                
                                // Import schema
                                $sql = file_get_contents('../database/schema.sql');
                                $pdo->exec($sql);
                                
                                // Import additional tables
                                $sql = file_get_contents('../database/migrations/add_missing_tables.sql');
                                $pdo->exec($sql);
                                
                                $sql = file_get_contents('../database/migrations/blog_tables.sql');
                                $pdo->exec($sql);
                                
                                // Create .env file
                                $env_content = "DB_HOST=$host\n";
                                $env_content .= "DB_NAME=$name\n";
                                $env_content .= "DB_USER=$user\n";
                                $env_content .= "DB_PASSWORD=$pass\n";
                                $env_content .= "APP_URL=http://localhost/easybuy-php\n";
                                $env_content .= "APP_ENV=development\n";
                                $env_content .= "APP_DEBUG=true\n";
                                
                                file_put_contents('../.env', $env_content);
                                
                                echo '<div class="alert alert-success">Database installed successfully!</div>';
                                echo '<a href="?step=3" class="btn btn-primary">Continue to Final Step</a>';
                            } catch(PDOException $e) {
                                echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                            }
                        }
                        ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Database Host</label>
                                <input type="text" class="form-control" name="host" value="localhost" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Database Name</label>
                                <input type="text" class="form-control" name="name" value="easybuy_db" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Database Username</label>
                                <input type="text" class="form-control" name="user" value="root" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Database Password</label>
                                <input type="password" class="form-control" name="pass">
                            </div>
                            <button type="submit" class="btn btn-primary">Install Database</button>
                        </form>
                        
                        <?php elseif($step == 3): ?>
                        <h4>Step 3: Admin Account Setup</h4>
                        
                        <?php
                        if($_SERVER['REQUEST_METHOD'] == 'POST') {
                            $admin_name = $_POST['admin_name'];
                            $admin_email = $_POST['admin_email'];
                            $admin_phone = $_POST['admin_phone'];
                            $admin_password = password_hash($_POST['admin_password'], PASSWORD_DEFAULT);
                            
                            require_once '../app/lib/Database.php';
                            $db = Database::getInstance();
                            
                            $db->query("
                                INSERT INTO users (full_name, email, phone, password, role) 
                                VALUES (?, ?, ?, ?, 'admin')
                            ", [$admin_name, $admin_email, $admin_phone, $admin_password]);
                            
                            echo '<div class="alert alert-success">Admin account created successfully!</div>';
                            echo '<div class="text-center">';
                            echo '<a href="../index.php" class="btn btn-success">Go to Website</a> ';
                            echo '<a href="../admin/login.php" class="btn btn-primary">Go to Admin Panel</a>';
                            echo '</div>';
                        }
                        ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Admin Name</label>
                                <input type="text" class="form-control" name="admin_name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Admin Email</label>
                                <input type="email" class="form-control" name="admin_email" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Admin Phone</label>
                                <input type="tel" class="form-control" name="admin_phone" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Admin Password</label>
                                <input type="password" class="form-control" name="admin_password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Complete Installation</button>
                        </form>
                        
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>