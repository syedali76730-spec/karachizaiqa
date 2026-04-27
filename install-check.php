<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation Checker - Karachi Zaiqa</title>
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
        .check-item {
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .check-pass {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
        }
        .check-fail {
            background: #ffebee;
            border-left: 4px solid #f44336;
        }
        .check-warn {
            background: #fff3e0;
            border-left: 4px solid #ff9800;
        }
        .icon {
            font-size: 24px;
        }
        .details {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        .warning {
            background: #fff3e0;
            border: 1px solid #ff9800;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .delete-notice {
            background: #ffebee;
            border: 2px solid #f44336;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Karachi Zaiqa - Installation Checker</h1>
        
        <div class="delete-notice">
            ⚠️ SECURITY NOTICE: Delete this file (install-check.php) after setup is complete!
        </div>
        
        <?php
        // Check PHP version
        $phpVersion = phpversion();
        $phpOk = version_compare($phpVersion, '7.0', '>=');
        ?>
        
        <div class="check-item <?php echo $phpOk ? 'check-pass' : 'check-fail'; ?>">
            <span class="icon"><?php echo $phpOk ? '✅' : '❌'; ?></span>
            <div>
                <strong>PHP Version:</strong> <?php echo $phpVersion; ?>
                <div class="details">Minimum required: 7.0</div>
            </div>
        </div>
        
        <?php
        // Check required files
        $requiredFiles = [
            'config.php',
            'index.html',
            'process-order.php',
            'admin-login.php',
            'admin-auth.php',
            'admin-dashboard.php',
            'admin-api.php',
            'admin-logout.php',
            'database.sql'
        ];
        
        $missingFiles = [];
        foreach ($requiredFiles as $file) {
            if (!file_exists($file)) {
                $missingFiles[] = $file;
            }
        }
        ?>
        
        <div class="check-item <?php echo empty($missingFiles) ? 'check-pass' : 'check-fail'; ?>">
            <span class="icon"><?php echo empty($missingFiles) ? '✅' : '❌'; ?></span>
            <div>
                <strong>Required Files:</strong> <?php echo empty($missingFiles) ? 'All files present' : 'Missing files'; ?>
                <?php if (!empty($missingFiles)): ?>
                    <div class="details">Missing: <?php echo implode(', ', $missingFiles); ?></div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php
        // Check PDO MySQL
        $pdoOk = extension_loaded('pdo_mysql');
        ?>
        
        <div class="check-item <?php echo $pdoOk ? 'check-pass' : 'check-fail'; ?>">
            <span class="icon"><?php echo $pdoOk ? '✅' : '❌'; ?></span>
            <div>
                <strong>PDO MySQL Extension:</strong> <?php echo $pdoOk ? 'Installed' : 'Not installed'; ?>
                <div class="details">Required for database connection</div>
            </div>
        </div>
        
        <?php
        // Check database connection
        require_once 'config.php';
        $dbConnected = false;
        $dbError = '';
        
        try {
            $conn = getDBConnection();
            $dbConnected = true;
        } catch (Exception $e) {
            $dbError = $e->getMessage();
        }
        ?>
        
        <div class="check-item <?php echo $dbConnected ? 'check-pass' : 'check-fail'; ?>">
            <span class="icon"><?php echo $dbConnected ? '✅' : '❌'; ?></span>
            <div>
                <strong>Database Connection:</strong> <?php echo $dbConnected ? 'Connected' : 'Failed'; ?>
                <?php if (!$dbConnected): ?>
                    <div class="details">Error: Check database credentials in config.php</div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php
        // Check tables exist
        $tablesOk = false;
        if ($dbConnected) {
            try {
                $stmt = $conn->query("SHOW TABLES");
                $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                $requiredTables = ['products', 'addons', 'orders', 'order_items', 'order_addons', 'admin_users'];
                $tablesOk = count(array_intersect($requiredTables, $tables)) === count($requiredTables);
            } catch (Exception $e) {
                $tablesOk = false;
            }
        }
        ?>
        
        <div class="check-item <?php echo $tablesOk ? 'check-pass' : 'check-fail'; ?>">
            <span class="icon"><?php echo $tablesOk ? '✅' : '❌'; ?></span>
            <div>
                <strong>Database Tables:</strong> <?php echo $tablesOk ? 'All tables created' : 'Tables missing'; ?>
                <div class="details">Run database.sql to create tables</div>
            </div>
        </div>
        
        <?php
        // Check if products exist
        $productsOk = false;
        if ($dbConnected && $tablesOk) {
            try {
                $stmt = $conn->query("SELECT COUNT(*) FROM products");
                $count = $stmt->fetchColumn();
                $productsOk = $count > 0;
            } catch (Exception $e) {
                $productsOk = false;
            }
        }
        ?>
        
        <div class="check-item <?php echo $productsOk ? 'check-pass' : 'check-warn'; ?>">
            <span class="icon"><?php echo $productsOk ? '✅' : '⚠️'; ?></span>
            <div>
                <strong>Products Data:</strong> <?php echo $productsOk ? 'Products loaded' : 'No products'; ?>
                <div class="details">database.sql includes default products</div>
            </div>
        </div>
        
        <?php
        // Check EmailJS configuration
        $emailjsConfigured = (
            EMAILJS_SERVICE_ID !== 'YOUR_EMAILJS_SERVICE_ID' &&
            EMAILJS_PUBLIC_KEY !== 'YOUR_EMAILJS_PUBLIC_KEY'
        );
        ?>
        
        <div class="check-item <?php echo $emailjsConfigured ? 'check-pass' : 'check-warn'; ?>">
            <span class="icon"><?php echo $emailjsConfigured ? '✅' : '⚠️'; ?></span>
            <div>
                <strong>EmailJS Configuration:</strong> <?php echo $emailjsConfigured ? 'Configured' : 'Not configured'; ?>
                <div class="details">Required for email notifications - See README.md</div>
            </div>
        </div>
        
        <?php
        // Check write permissions
        $writable = is_writable(__DIR__);
        ?>
        
        <div class="check-item <?php echo $writable ? 'check-pass' : 'check-warn'; ?>">
            <span class="icon"><?php echo $writable ? '✅' : '⚠️'; ?></span>
            <div>
                <strong>Directory Permissions:</strong> <?php echo $writable ? 'Writable' : 'Limited'; ?>
                <div class="details">Some features may require write permissions</div>
            </div>
        </div>
        
        <div class="warning">
            <h3>⚠️ Next Steps:</h3>
            <ol>
                <li>Configure EmailJS settings in config.php and index.html</li>
                <li>Change default admin password (currently: admin123)</li>
                <li>Test customer ordering at: <a href="index.html">index.html</a></li>
                <li>Test admin login at: <a href="admin-login.php">admin-login.php</a></li>
                <li><strong>DELETE THIS FILE (install-check.php) after setup!</strong></li>
            </ol>
        </div>
        
        <div style="margin-top: 30px; padding: 20px; background: #e8f5e9; border-radius: 5px;">
            <h3 style="color: #2C5F2D;">📚 Documentation</h3>
            <p>For detailed setup instructions, see <strong>README.md</strong></p>
            <p>Default admin email: <strong>syed.ali.76730@gmail.com</strong></p>
            <p>Default password: <strong>admin123</strong> (CHANGE THIS!)</p>
        </div>
    </div>
</body>
</html>
