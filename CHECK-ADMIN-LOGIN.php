<?php
// CHECK-ADMIN-LOGIN.php
// Diagnostic tool to check why login is failing
// DELETE AFTER USE!

require_once 'config.php';

$checks = [];
$adminUser = null;

// Check 1: Database Connection
try {
    $conn = getDBConnection();
    $checks['database'] = ['status' => 'success', 'message' => 'Database connected successfully'];
} catch (Exception $e) {
    $checks['database'] = ['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()];
}

// Check 2: Admin Users Table Exists
if (isset($conn)) {
    try {
        $stmt = $conn->query("SHOW TABLES LIKE 'admin_users'");
        $tableExists = $stmt->fetch();
        if ($tableExists) {
            $checks['table'] = ['status' => 'success', 'message' => 'admin_users table exists'];
        } else {
            $checks['table'] = ['status' => 'error', 'message' => 'admin_users table does NOT exist'];
        }
    } catch (Exception $e) {
        $checks['table'] = ['status' => 'error', 'message' => 'Error checking table: ' . $e->getMessage()];
    }
}

// Check 3: Admin User Exists
if (isset($conn)) {
    try {
        $stmt = $conn->prepare("SELECT * FROM admin_users WHERE email = ?");
        $stmt->execute(['syed.ali.76730@gmail.com']);
        $adminUser = $stmt->fetch();
        
        if ($adminUser) {
            $checks['user'] = ['status' => 'success', 'message' => 'Admin user found in database'];
        } else {
            $checks['user'] = ['status' => 'error', 'message' => 'Admin user NOT found in database'];
        }
    } catch (Exception $e) {
        $checks['user'] = ['status' => 'error', 'message' => 'Error finding user: ' . $e->getMessage()];
    }
}

// Check 4: Test Password Verification
if ($adminUser) {
    $testPassword = 'Admin@2025';
    $hashMatches = password_verify($testPassword, $adminUser['password_hash']);
    
    if ($hashMatches) {
        $checks['password'] = ['status' => 'success', 'message' => 'Password hash is CORRECT for: Admin@2025'];
    } else {
        $checks['password'] = ['status' => 'error', 'message' => 'Password hash does NOT match: Admin@2025'];
    }
}

// Check 5: Session Configuration
if (session_status() === PHP_SESSION_NONE) {
    $checks['session'] = ['status' => 'warning', 'message' => 'Sessions not started (this is normal here)'];
} else {
    $checks['session'] = ['status' => 'success', 'message' => 'Sessions available'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login Diagnostic - Karachi Zaiqa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
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
        .check {
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            border-left: 4px solid;
        }
        .success {
            background: #e8f5e9;
            border-left-color: #4caf50;
        }
        .error {
            background: #ffebee;
            border-left-color: #f44336;
        }
        .warning {
            background: #fff3e0;
            border-left-color: #ff9800;
        }
        .delete-warning {
            background: #ffebee;
            border: 2px solid #f44336;
            padding: 20px;
            margin: 20px 0;
            font-weight: bold;
            border-radius: 5px;
        }
        .info-box {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            font-family: monospace;
            font-size: 12px;
        }
        .icon {
            font-size: 20px;
            margin-right: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f5f5f5;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Admin Login Diagnostic</h1>
        
        <div class="delete-warning">
            ⚠️ DELETE THIS FILE AFTER CHECKING! It exposes sensitive information.
        </div>
        
        <h2>System Checks:</h2>
        
        <?php foreach ($checks as $name => $check): ?>
            <div class="check <?php echo $check['status']; ?>">
                <span class="icon">
                    <?php 
                    if ($check['status'] === 'success') echo '✅';
                    elseif ($check['status'] === 'error') echo '❌';
                    else echo '⚠️';
                    ?>
                </span>
                <strong><?php echo ucfirst($name); ?>:</strong> 
                <?php echo $check['message']; ?>
            </div>
        <?php endforeach; ?>
        
        <?php if ($adminUser): ?>
            <h2>Admin User Details:</h2>
            <table>
                <tr>
                    <th>Field</th>
                    <th>Value</th>
                </tr>
                <tr>
                    <td>ID</td>
                    <td><?php echo $adminUser['id']; ?></td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td><?php echo htmlspecialchars($adminUser['email']); ?></td>
                </tr>
                <tr>
                    <td>Name</td>
                    <td><?php echo htmlspecialchars($adminUser['name']); ?></td>
                </tr>
                <tr>
                    <td>Active</td>
                    <td><?php echo $adminUser['is_active'] ? 'Yes' : 'No'; ?></td>
                </tr>
                <tr>
                    <td>Password Hash (first 50 chars)</td>
                    <td style="font-family: monospace; font-size: 10px;">
                        <?php echo substr($adminUser['password_hash'], 0, 50); ?>...
                    </td>
                </tr>
            </table>
        <?php endif; ?>
        
        <h2>Recommended Actions:</h2>
        
        <?php if (isset($checks['password']) && $checks['password']['status'] === 'error'): ?>
            <div class="error">
                <strong>⚠️ Password hash is incorrect!</strong><br><br>
                <strong>Solution:</strong> Use the RESET-ADMIN-PASSWORD.php tool to reset the password.<br>
                This will generate a fresh password hash on YOUR server, ensuring compatibility.
            </div>
        <?php elseif (isset($checks['user']) && $checks['user']['status'] === 'error'): ?>
            <div class="error">
                <strong>⚠️ Admin user doesn't exist!</strong><br><br>
                <strong>Solution:</strong> Use the RESET-ADMIN-PASSWORD.php tool to create the admin user.
            </div>
        <?php elseif (isset($checks['password']) && $checks['password']['status'] === 'success'): ?>
            <div class="success">
                <strong>✅ Everything looks good!</strong><br><br>
                Password should work. Try logging in with:<br>
                Email: <strong>syed.ali.76730@gmail.com</strong><br>
                Password: <strong>Admin@2025</strong><br><br>
                If login still fails, check the browser console for JavaScript errors.
            </div>
        <?php endif; ?>
        
        <div class="delete-warning" style="margin-top: 30px;">
            <strong>🗑️ NEXT STEPS:</strong><br>
            1. Take note of any errors above<br>
            2. Use RESET-ADMIN-PASSWORD.php if needed<br>
            3. DELETE THIS FILE (CHECK-ADMIN-LOGIN.php)<br>
            4. DELETE RESET-ADMIN-PASSWORD.php after use
        </div>
    </div>
</body>
</html>
