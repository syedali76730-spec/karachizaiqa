<?php
// RESET-ADMIN-PASSWORD.php
// Upload this file and visit it in your browser to reset admin password

// IMPORTANT: DELETE THIS FILE AFTER USE!

require_once 'config.php';

$success = false;
$error = '';
$newPassword = 'Admin@2025';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    try {
        $conn = getDBConnection();
        
        // Generate password hash on THIS server (ensures compatibility)
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Delete old admin
        $stmt = $conn->prepare("DELETE FROM admin_users WHERE email = ?");
        $stmt->execute(['syed.ali.76730@gmail.com']);
        
        // Insert new admin with fresh hash
        $stmt = $conn->prepare("
            INSERT INTO admin_users (email, password_hash, name, is_active) 
            VALUES (?, ?, ?, ?)
        ");
        
        $stmt->execute([
            'syed.ali.76730@gmail.com',
            $passwordHash,
            'Admin',
            1
        ]);
        
        $success = true;
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Admin Password - Karachi Zaiqa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #8B1A1A;
            border-bottom: 3px solid #D4A574;
            padding-bottom: 10px;
        }
        .warning {
            background: #fff3e0;
            border-left: 4px solid #ff9800;
            padding: 15px;
            margin: 20px 0;
        }
        .delete-warning {
            background: #ffebee;
            border: 2px solid #f44336;
            padding: 20px;
            margin: 20px 0;
            font-weight: bold;
            border-radius: 5px;
        }
        .success {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .error {
            background: #ffebee;
            border-left: 4px solid #f44336;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .credentials {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            font-family: monospace;
        }
        button {
            background: #8B1A1A;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background: #6B1414;
        }
        .info {
            background: #e3f2fd;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Reset Admin Password</h1>
        
        <div class="delete-warning">
            ⚠️ CRITICAL: DELETE THIS FILE IMMEDIATELY AFTER USE!
        </div>
        
        <?php if ($success): ?>
            <div class="success">
                <h2>✅ Password Reset Successful!</h2>
                <p>Your admin password has been successfully reset.</p>
            </div>
            
            <div class="credentials">
                <strong>Login URL:</strong> <a href="admin-login.php">admin-login.php</a><br><br>
                <strong>Email:</strong> syed.ali.76730@gmail.com<br>
                <strong>Password:</strong> Admin@2025
            </div>
            
            <div class="warning">
                <strong>IMPORTANT NEXT STEPS:</strong><br>
                1. Test the login now<br>
                2. DELETE THIS FILE (RESET-ADMIN-PASSWORD.php)<br>
                3. Never leave this file on your server!
            </div>
            
        <?php elseif ($error): ?>
            <div class="error">
                <strong>❌ Error:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
            <form method="POST">
                <button type="submit" name="confirm" value="1">Try Again</button>
            </form>
            
        <?php else: ?>
            <div class="info">
                <strong>This tool will:</strong><br>
                1. Delete the existing admin user<br>
                2. Create a new admin user<br>
                3. Set the password to: <strong>Admin@2025</strong><br>
                4. Email will be: <strong>syed.ali.76730@gmail.com</strong>
            </div>
            
            <div class="warning">
                <strong>Before clicking:</strong><br>
                Make sure your config.php has the correct database credentials!
            </div>
            
            <form method="POST">
                <button type="submit" name="confirm" value="1">Reset Password Now</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
