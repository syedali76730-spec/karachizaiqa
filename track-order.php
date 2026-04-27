<?php
// track-order.php
// Public API for customers to track their orders

require_once 'config.php';

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method');
}

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || empty($data['search'])) {
    jsonResponse(false, 'Search term is required');
}

$searchTerm = sanitizeInput($data['search']);

try {
    $conn = getDBConnection();
    
    // Search by order number, phone, or email
    // Use OR conditions to search across multiple fields
    $stmt = $conn->prepare("
        SELECT 
            o.id,
            o.order_number,
            o.customer_name,
            o.customer_phone,
            o.customer_email,
            o.pickup_date,
            o.pickup_time,
            o.total_amount,
            o.status,
            o.created_at,
            p.name as product_name,
            oi.quantity as product_quantity,
            GROUP_CONCAT(DISTINCT a.name SEPARATOR ', ') as addon_names
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN products p ON oi.product_id = p.id
        LEFT JOIN order_addons oa ON o.id = oa.order_id
        LEFT JOIN addons a ON oa.addon_id = a.id
        WHERE 
            o.order_number = ? OR
            o.customer_phone LIKE ? OR
            o.customer_email LIKE ?
        GROUP BY o.id
        ORDER BY o.created_at DESC
        LIMIT 10
    ");
    
    $likeSearch = '%' . $searchTerm . '%';
    $stmt->execute([$searchTerm, $likeSearch, $likeSearch]);
    
    $orders = [];
    while ($order = $stmt->fetch()) {
        // Build items summary
        $itemsSummary = $order['product_quantity'] . 'x ' . $order['product_name'];
        if ($order['addon_names']) {
            $itemsSummary .= ' + ' . $order['addon_names'];
        }
        
        // Format created_at
        $createdDate = new DateTime($order['created_at']);
        $formattedDate = $createdDate->format('M d, Y g:i A');
        
        $orders[] = [
            'id' => $order['id'],
            'order_number' => $order['order_number'],
            'customer_name' => $order['customer_name'],
            // Don't expose full phone/email for privacy
            'customer_phone' => $order['customer_phone'] ? '***' . substr($order['customer_phone'], -4) : null,
            'customer_email' => $order['customer_email'] ? substr($order['customer_email'], 0, 3) . '***' : null,
            'pickup_date' => $order['pickup_date'],
            'pickup_time' => $order['pickup_time'],
            'total_amount' => $order['total_amount'],
            'status' => $order['status'],
            'items_summary' => $itemsSummary,
            'created_at' => $formattedDate
        ];
    }
    
    if (empty($orders)) {
        jsonResponse(false, 'No orders found', []);
    } else {
        jsonResponse(true, 'Orders found', $orders);
    }
    
} catch (Exception $e) {
    error_log("Track Order Error: " . $e->getMessage());
    jsonResponse(false, 'Failed to search orders');
}
?>
