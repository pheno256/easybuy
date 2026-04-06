<?php
session_start();
require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

if(isset($_SESSION['vendor_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $db = Database::getInstance();
    $vendor = $db->query("SELECT * FROM vendors WHERE email = ? AND status = 'active'", [$email])->fetch();
    
    if($vendor && password_verify($password, $vendor['password'])) {
        $_SESSION['vendor_id'] = $vendor['id'];
        $_SESSION['vendor_name'] = $vendor['business_name'];
        header('Location: index.php');
        exit;
    } else {
        $error = "Invalid credentials or account not activated";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Login - EasyBuy Uganda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <i class="fas fa-store fa-3x mb-2"></i>
                        <h3 class="mb-0">Vendor Login</h3>
                        <p class="mb-0">EasyBuy Uganda</p>
                    </div>
                    <div class="card-body p-4">
                        <?php if($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                        <hr>
                        <div class="text-center">
                            <a href="register.php">Become a Vendor</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>