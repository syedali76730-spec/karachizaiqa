<?php
// process-order.php
// Handles order submission with MULTIPLE products and addon quantities

require_once 'config.php';

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method');
}

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    jsonResponse(false, 'Invalid JSON data');
}

// Validate required fields
if (empty($data['customerName'])) {
    jsonResponse(false, 'Customer name is required');
}

if (empty($data['customerPhone']) && empty($data['customerEmail'])) {
    jsonResponse(false, 'Either phone number or email is required');
}

if (empty($data['products']) || !is_array($data['products']) || count($data['products']) === 0) {
    jsonResponse(false, 'Please select at least one product');
}

if (empty($data['pickupDate']) || empty($data['pickupTime'])) {
    jsonResponse(false, 'Pickup date and time are required');
}

// Validate pickup time (2 PM - 8 PM)
$pickupTime = $data['pickupTime'];
list($hours, $minutes) = explode(':', $pickupTime);
$timeInMinutes = ((int)$hours * 60) + (int)$minutes;
$startTime = 14 * 60; // 2 PM
$endTime = 20 * 60; // 8 PM

if ($timeInMinutes < $startTime || $timeInMinutes > $endTime) {
    jsonResponse(false, 'Pickup time must be between 2:00 PM and 8:00 PM');
}

// Sanitize inputs
$customerName = sanitizeInput($data['customerName']);
$customerPhone = !empty($data['customerPhone']) ? sanitizeInput($data['customerPhone']) : null;
$customerEmail = !empty($data['customerEmail']) ? sanitizeInput($data['customerEmail']) : null;
$pickupDate = $data['pickupDate'];
$pickupTime = $data['pickupTime'];
$specialInstructions = !empty($data['specialInstructions']) ? sanitizeInput($data['specialInstructions']) : null;
$products = $data['products']; // { productId: quantity }
$addons = isset($data['addons']) && is_array($data['addons']) ? $data['addons'] : []; // { addonId: quantity }

try {
    $conn = getDBConnection();
    
    // Calculate total
    $subtotal = 0;
    $productDetails = [];
    
    // Validate and calculate products
    foreach ($products as $productId => $quantity) {
        $productId = (int)$productId;
        $quantity = max(1, (int)$quantity);
        
        $stmt = $conn->prepare("SELECT id, name, price FROM products WHERE id = ? AND is_active = 1");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();
        
        if (!$product) {
            jsonResponse(false, "Invalid product ID: $productId");
        }
        
        $productDetails[$productId] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity
        ];
        
        $subtotal += $product['price'] * $quantity;
    }
    
    // Validate and calculate add-ons
    $addonDetails = [];
    if (!empty($addons)) {
        foreach ($addons as $addonId => $quantity) {
            $addonId = (int)$addonId;
            $quantity = max(1, (int)$quantity);
            
            $stmt = $conn->prepare("SELECT id, name, price FROM addons WHERE id = ? AND is_active = 1");
            $stmt->execute([$addonId]);
            $addon = $stmt->fetch();
            
            if (!$addon) {
                continue; // Skip invalid addons
            }
            
            $addonDetails[$addonId] = [
                'id' => $addon['id'],
                'name' => $addon['name'],
                'price' => $addon['price'],
                'quantity' => $quantity
            ];
            
            $subtotal += $addon['price'] * $quantity;
        }
    }
    
    $total = $subtotal;
    
    // Generate unique order number
    do {
        $orderNumber = generateOrderNumber();
        $stmt = $conn->prepare("SELECT id FROM orders WHERE order_number = ?");
        $stmt->execute([$orderNumber]);
    } while ($stmt->fetch());
    
    // Start transaction
    $conn->beginTransaction();
    
    // Insert order
    $stmt = $conn->prepare("
        INSERT INTO orders (
            order_number, customer_name, customer_phone, customer_email,
            pickup_date, pickup_time, special_instructions,
            subtotal, total_amount, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'received')
    ");
    
    $stmt->execute([
        $orderNumber,
        $customerName,
        $customerPhone,
        $customerEmail,
        $pickupDate,
        $pickupTime,
        $specialInstructions,
        $total,
        $total
    ]);
    
    $orderId = $conn->lastInsertId();
    
    // Insert order items (multiple products)
    $stmt = $conn->prepare("
        INSERT INTO order_items (order_id, product_id, quantity, price)
        VALUES (?, ?, ?, ?)
    ");
    
    foreach ($productDetails as $product) {
        $stmt->execute([
            $orderId,
            $product['id'],
            $product['quantity'],
            $product['price']
        ]);
        
        // Decrement inventory (Fix #4)
        $updateStmt = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ? AND quantity >= ?");
        $updateStmt->execute([$product['quantity'], $product['id'], $product['quantity']]);
        
        // Check if inventory update was successful
        if ($updateStmt->rowCount() === 0) {
            $conn->rollBack();
            jsonResponse(false, 'Insufficient quantity for ' . $product['name']);
            exit;
        }
    }
    
    // Insert add-ons with quantities
    if (!empty($addonDetails)) {
        $stmt = $conn->prepare("
            INSERT INTO order_addons (order_id, addon_id, quantity, price)
            VALUES (?, ?, ?, ?)
        ");
        
        foreach ($addonDetails as $addon) {
            $stmt->execute([
                $orderId,
                $addon['id'],
                $addon['quantity'],
                $addon['price']
            ]);
        }
    }
    
    // Commit transaction
    $conn->commit();
    
    // Build email body for owner
    $productsText = '';
    foreach ($productDetails as $product) {
        $productsText .= "{$product['quantity']}x {$product['name']} (A$" . number_format($product['price'], 2) . " each)<br>";
    }
    
    $addonsText = 'None';
    if (!empty($addonDetails)) {
        $addonsText = '';
        foreach ($addonDetails as $addon) {
            $addonsText .= "{$addon['quantity']}x {$addon['name']} (A$" . number_format($addon['price'], 2) . " each)<br>";
        }
    }
    
    // Send email notification to owner using PHP mail()
    $ownerEmailBody = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background: #f4f4f4; }
            .container { max-width: 600px; margin: 20px auto; background: white; }
            .header { background: #8B1A1A; color: white; padding: 30px 20px; text-align: center; }
            .header h1 { margin: 0; font-size: 28px; }
            .content { padding: 30px 20px; }
            .order-info { background: #f9f9f9; padding: 15px; margin: 15px 0; border-left: 4px solid #2C5F2D; }
            .order-info h3 { margin: 0 0 10px 0; color: #8B1A1A; }
            .total { background: #654321; color: white; padding: 20px; text-align: center; font-size: 24px; font-weight: bold; }
            .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; background: #f4f4f4; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🔔 New Order Received!</h1>
            </div>
            <div class='content'>
                <h2 style='color: #8B1A1A; border-bottom: 2px solid #D4A574; padding-bottom: 10px;'>Order #$orderNumber</h2>
                
                <div class='order-info'>
                    <h3>Customer Details:</h3>
                    <strong>Name:</strong> $customerName<br>
                    <strong>Phone:</strong> " . ($customerPhone ?: 'Not provided') . "<br>
                    <strong>Email:</strong> " . ($customerEmail ?: 'Not provided') . "
                </div>
                
                <div class='order-info'>
                    <h3>Order Items:</h3>
                    $productsText
                    <br>
                    <strong>Add-ons:</strong><br>
                    $addonsText
                </div>
                
                <div class='order-info'>
                    <h3>Pickup Information:</h3>
                    <strong>Date:</strong> $pickupDate<br>
                    <strong>Time:</strong> $pickupTime
                    " . ($specialInstructions ? "<br><br><strong>Special Instructions:</strong><br>$specialInstructions" : '') . "
                </div>
                
                <div class='total'>
                    Total Amount: A$" . number_format($total, 2) . "
                </div>
            </div>
            <div class='footer'>
                Karachi Zaiqa - Sydney's Authentic Haleem<br>
                Login to admin panel to manage this order
            </div>
        </div>
    </body>
    </html>
    ";
    
    sendEmailNotification(
        OWNER_EMAIL,
        "New Order #$orderNumber - Karachi Zaiqa",
        $ownerEmailBody
    );
    
    // Return success response with products and addons objects
    jsonResponse(true, 'Order placed successfully', [
        'orderId' => $orderId,
        'orderNumber' => $orderNumber,
        'customerName' => $customerName,
        'customerPhone' => $customerPhone,
        'customerEmail' => $customerEmail,
        'products' => $products,
        'addons' => $addons,
        'pickupDate' => $pickupDate,
        'pickupTime' => $pickupTime,
        'specialInstructions' => $specialInstructions,
        'total' => $total
    ]);
    
} catch (Exception $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    
    error_log("Order Processing Error: " . $e->getMessage());
    jsonResponse(false, 'Failed to process order. Please try again.');
}
?>
