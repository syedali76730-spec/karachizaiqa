<?php
// admin-api.php
// API endpoint for admin dashboard operations

require_once 'config.php';
requireLogin();

// Get action from GET or POST
$action = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // For POST requests, check JSON body first
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    $action = $data['action'] ?? $_POST['action'] ?? '';
} else {
    // For GET requests
    $action = $_GET['action'] ?? '';
}

switch ($action) {
    case 'dashboard':
        getDashboardStats();
        break;
    
    case 'orders':
        getOrders();
        break;
    
    case 'orderDetails':
        getOrderDetails();
        break;
    
    case 'updateStatus':
        updateOrderStatus();
        break;
    
    case 'pricing':
        getPricing();
        break;
    
    case 'updatePricing':
        updatePricing();
        break;
    
    case 'updateInventory':
        updateInventory();
        break;
    
    case 'toggleAddon':
        toggleAddon();
        break;
    
    case 'cashflow':
        getCashFlow();
        break;
    
    case 'export':
        exportOrders();
        break;

    case 'deleteOrder':
        deleteOrder();
        break;

    default:
        jsonResponse(false, 'Invalid action: ' . $action);
}

// Dashboard Statistics
function getDashboardStats() {
    try {
        $conn = getDBConnection();
        
        // Total orders
        $stmt = $conn->query("SELECT COUNT(*) as count FROM orders");
        $totalOrders = $stmt->fetch()['count'];
        
        // Today's orders
        $stmt = $conn->query("SELECT COUNT(*) as count FROM orders WHERE DATE(created_at) = CURDATE()");
        $todayOrders = $stmt->fetch()['count'];
        
        // Pending orders (not picked up)
        $stmt = $conn->query("SELECT COUNT(*) as count FROM orders WHERE status != 'picked_up'");
        $pendingOrders = $stmt->fetch()['count'];
        
        // Total revenue
        $stmt = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE status = 'picked_up'");
        $totalRevenue = $stmt->fetch()['total'] ?? 0;
        
        // Recent orders (last 10)
        $stmt = $conn->query("
            SELECT o.*, 
                   p.name as product_name,
                   GROUP_CONCAT(a.name SEPARATOR ', ') as addon_names
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            LEFT JOIN products p ON oi.product_id = p.id
            LEFT JOIN order_addons oa ON o.id = oa.order_id
            LEFT JOIN addons a ON oa.addon_id = a.id
            GROUP BY o.id
            ORDER BY o.created_at DESC
            LIMIT 10
        ");
        
        $recentOrders = [];
        while ($order = $stmt->fetch()) {
            $order['items_summary'] = $order['product_name'] . ($order['addon_names'] ? ' + addons' : '');
            $recentOrders[] = $order;
        }
        
        jsonResponse(true, '', [
            'totalOrders' => $totalOrders,
            'todayOrders' => $todayOrders,
            'pendingOrders' => $pendingOrders,
            'totalRevenue' => (float)$totalRevenue,
            'recentOrders' => $recentOrders
        ]);
        
    } catch (Exception $e) {
        error_log("Dashboard Error: " . $e->getMessage());
        jsonResponse(false, 'Failed to load dashboard');
    }
}

// Get Orders with Filters
function getOrders() {
    try {
        $conn = getDBConnection();
        
        $status = $_GET['status'] ?? '';
        $dateFrom = $_GET['dateFrom'] ?? '';
        $dateTo = $_GET['dateTo'] ?? '';
        
        $where = [];
        $params = [];
        
        if ($status) {
            $where[] = "o.status = ?";
            $params[] = $status;
        }
        
        if ($dateFrom) {
            $where[] = "DATE(o.created_at) >= ?";
            $params[] = $dateFrom;
        }
        
        if ($dateTo) {
            $where[] = "DATE(o.created_at) <= ?";
            $params[] = $dateTo;
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $stmt = $conn->prepare("
            SELECT o.*, 
                   p.name as product_name,
                   oi.quantity as product_quantity,
                   GROUP_CONCAT(DISTINCT a.name SEPARATOR ', ') as addon_names
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            LEFT JOIN products p ON oi.product_id = p.id
            LEFT JOIN order_addons oa ON o.id = oa.order_id
            LEFT JOIN addons a ON oa.addon_id = a.id
            $whereClause
            GROUP BY o.id
            ORDER BY o.created_at DESC
        ");
        
        $stmt->execute($params);
        
        $orders = [];
        while ($order = $stmt->fetch()) {
            $order['items_summary'] = $order['product_quantity'] . 'x ' . $order['product_name'];
            if ($order['addon_names']) {
                $order['items_summary'] .= ' + ' . $order['addon_names'];
            }
            $orders[] = $order;
        }
        
        jsonResponse(true, '', $orders);
        
    } catch (Exception $e) {
        error_log("Get Orders Error: " . $e->getMessage());
        jsonResponse(false, 'Failed to load orders');
    }
}

// Get Order Details
function getOrderDetails() {
    try {
        $orderId = $_GET['orderId'] ?? 0;
        
        if (!$orderId) {
            jsonResponse(false, 'Order ID required');
        }
        
        $conn = getDBConnection();
        
        $stmt = $conn->prepare("
            SELECT o.*, 
                   p.name as product_name,
                   p.price as product_price,
                   oi.quantity as product_quantity
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE o.id = ?
        ");
        
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();
        
        if (!$order) {
            jsonResponse(false, 'Order not found');
        }
        
        // Get add-ons
        $stmt = $conn->prepare("
            SELECT a.name, a.price
            FROM order_addons oa
            JOIN addons a ON oa.addon_id = a.id
            WHERE oa.order_id = ?
        ");
        
        $stmt->execute([$orderId]);
        $addons = $stmt->fetchAll();
        
        // Build items detail
        $itemsDetail = $order['product_quantity'] . 'x ' . $order['product_name'] . ' (A$' . number_format($order['product_price'], 2) . ' each)';
        
        if (!empty($addons)) {
            $itemsDetail .= '<br><strong>Add-ons:</strong><br>';
            foreach ($addons as $addon) {
                $itemsDetail .= '• ' . $addon['name'] . ' (A$' . number_format($addon['price'], 2) . ')<br>';
            }
        }
        
        $order['items_detail'] = $itemsDetail;
        
        jsonResponse(true, '', $order);
        
    } catch (Exception $e) {
        error_log("Get Order Details Error: " . $e->getMessage());
        jsonResponse(false, 'Failed to load order details');
    }
}

// Update Order Status
function updateOrderStatus() {
    try {
        // Get data from JSON body
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        // Also check POST array as fallback
        $orderId = $data['orderId'] ?? $_POST['orderId'] ?? 0;
        $status = $data['status'] ?? $_POST['status'] ?? '';
        
        if (!$orderId || !$status) {
            jsonResponse(false, 'Order ID and status required');
        }
        
        $validStatuses = ['received', 'preparing', 'ready', 'picked_up'];
        if (!in_array($status, $validStatuses)) {
            jsonResponse(false, 'Invalid status: ' . $status);
        }
        
        $conn = getDBConnection();
        
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $orderId]);
        
        if ($stmt->rowCount() > 0) {
            jsonResponse(true, 'Status updated successfully');
        } else {
            jsonResponse(false, 'Order not found or status unchanged');
        }
        
    } catch (Exception $e) {
        error_log("Update Status Error: " . $e->getMessage());
        jsonResponse(false, 'Failed to update status: ' . $e->getMessage());
    }
}

// Get Pricing
function getPricing() {
    try {
        $conn = getDBConnection();
        
        $stmt = $conn->query("SELECT id, name, price, quantity FROM products WHERE is_active = 1 ORDER BY id");
        $products = $stmt->fetchAll();
        
        $stmt = $conn->query("SELECT id, name, price, is_active FROM addons ORDER BY id");
        $addons = $stmt->fetchAll();
        
        jsonResponse(true, '', [
            'products' => $products,
            'addons' => $addons
        ]);
        
    } catch (Exception $e) {
        error_log("Get Pricing Error: " . $e->getMessage());
        jsonResponse(false, 'Failed to load pricing');
    }
}

// Update Pricing
function updatePricing() {
    try {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        $prices = $data['prices'] ?? [];
        
        if (empty($prices)) {
            jsonResponse(false, 'No prices provided');
        }
        
        $conn = getDBConnection();
        $conn->beginTransaction();
        
        foreach ($prices as $item) {
            $table = $item['type'] === 'product' ? 'products' : 'addons';
            $stmt = $conn->prepare("UPDATE $table SET price = ? WHERE id = ?");
            $stmt->execute([$item['price'], $item['id']]);
        }
        
        $conn->commit();
        
        jsonResponse(true, 'Prices updated successfully');
        
    } catch (Exception $e) {
        if (isset($conn) && $conn->inTransaction()) {
            $conn->rollBack();
        }
        error_log("Update Pricing Error: " . $e->getMessage());
        jsonResponse(false, 'Failed to update pricing');
    }
}

// Get Cash Flow
function getCashFlow() {
    try {
        $period = $_GET['period'] ?? 'month';
        
        $conn = getDBConnection();
        
        $where = '';
        switch ($period) {
            case 'today':
                $where = "WHERE DATE(created_at) = CURDATE()";
                break;
            case 'week':
                $where = "WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                break;
            case 'month':
                $where = "WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
                break;
            case 'all':
            default:
                $where = '';
        }
        
        // Total revenue
        $stmt = $conn->query("SELECT SUM(total_amount) as total FROM orders $where");
        $totalRevenue = $stmt->fetch()['total'] ?? 0;
        
        // Total orders
        $stmt = $conn->query("SELECT COUNT(*) as count FROM orders $where");
        $totalOrders = $stmt->fetch()['count'];
        
        // Average order value
        $averageOrder = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        
        // Get orders list
        $stmt = $conn->query("
            SELECT id, order_number, customer_name, total_amount, status, 
                   DATE_FORMAT(created_at, '%Y-%m-%d %H:%i') as created_at
            FROM orders 
            $where
            ORDER BY created_at DESC
        ");
        
        $orders = $stmt->fetchAll();
        
        jsonResponse(true, '', [
            'totalRevenue' => (float)$totalRevenue,
            'totalOrders' => $totalOrders,
            'averageOrder' => (float)$averageOrder,
            'orders' => $orders
        ]);
        
    } catch (Exception $e) {
        error_log("Cash Flow Error: " . $e->getMessage());
        jsonResponse(false, 'Failed to load cash flow data');
    }
}

// Export Orders to CSV
function exportOrders() {
    try {
        $conn = getDBConnection();
        
        $stmt = $conn->query("
            SELECT o.order_number, o.customer_name, o.customer_phone, o.customer_email,
                   o.pickup_date, o.pickup_time, o.special_instructions,
                   p.name as product, oi.quantity,
                   o.total_amount, o.status, o.created_at
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            LEFT JOIN products p ON oi.product_id = p.id
            ORDER BY o.created_at DESC
        ");
        
        $orders = $stmt->fetchAll();
        
        // Set CSV headers
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="karachi-zaiqa-orders-' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // CSV column headers
        fputcsv($output, [
            'Order Number',
            'Customer Name',
            'Phone',
            'Email',
            'Product',
            'Quantity',
            'Pickup Date',
            'Pickup Time',
            'Special Instructions',
            'Total Amount',
            'Status',
            'Order Date'
        ]);
        
        // CSV data
        foreach ($orders as $order) {
            fputcsv($output, [
                $order['order_number'],
                $order['customer_name'],
                $order['customer_phone'] ?? '',
                $order['customer_email'] ?? '',
                $order['product'],
                $order['quantity'],
                $order['pickup_date'],
                $order['pickup_time'],
                $order['special_instructions'] ?? '',
                'A$' . number_format($order['total_amount'], 2),
                $order['status'],
                $order['created_at']
            ]);
        }
        
        fclose($output);
        exit;
        
    } catch (Exception $e) {
        error_log("Export Error: " . $e->getMessage());
        die('Export failed');
    }
}

// Update Inventory Quantity (Fix #4)
function updateInventory() {
    try {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        $productId = $data['productId'] ?? $_POST['productId'] ?? 0;
        $quantity = $data['quantity'] ?? $_POST['quantity'] ?? 0;
        
        if (!$productId || $quantity < 0) {
            jsonResponse(false, 'Product ID and valid quantity required');
        }
        
        $conn = getDBConnection();
        
        $stmt = $conn->prepare("UPDATE products SET quantity = ? WHERE id = ?");
        $stmt->execute([$quantity, $productId]);
        
        jsonResponse(true, 'Inventory updated successfully');
        
    } catch (Exception $e) {
        error_log("Update Inventory Error: " . $e->getMessage());
        jsonResponse(false, 'Failed to update inventory');
    }
}

// Delete Order
function deleteOrder() {
    try {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        $orderId = isset($data['orderId']) ? (int)$data['orderId'] : 0;

        if (!$orderId) {
            jsonResponse(false, 'Order ID required');
        }

        $conn = getDBConnection();

        $stmt = $conn->prepare("SELECT id, order_number, status FROM orders WHERE id = ?");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();

        if (!$order) {
            jsonResponse(false, 'Order not found');
        }

        $deletableStatuses = ['cancelled', 'picked_up'];
        if (!in_array($order['status'], $deletableStatuses)) {
            jsonResponse(false, 'Only cancelled or picked-up orders can be deleted');
        }

        $conn->beginTransaction();

        $stmt = $conn->prepare("DELETE FROM order_addons WHERE order_id = ?");
        $stmt->execute([$orderId]);

        $stmt = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
        $stmt->execute([$orderId]);

        $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->execute([$orderId]);

        $conn->commit();

        jsonResponse(true, 'Order deleted successfully');

    } catch (Exception $e) {
        if (isset($conn) && $conn->inTransaction()) {
            $conn->rollBack();
        }
        error_log("Delete Order Error: " . $e->getMessage());
        jsonResponse(false, 'Failed to delete order');
    }
}

// Toggle Addon Status (Fix #3)
function toggleAddon() {
    try {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        $addonId = $data['addonId'] ?? $_POST['addonId'] ?? 0;
        $isActive = isset($data['isActive']) ? $data['isActive'] : (isset($_POST['isActive']) ? $_POST['isActive'] : null);
        
        if (!$addonId || $isActive === null) {
            jsonResponse(false, 'Addon ID and active status required');
        }
        
        $conn = getDBConnection();
        
        $stmt = $conn->prepare("UPDATE addons SET is_active = ? WHERE id = ?");
        $stmt->execute([boolval($isActive), $addonId]);
        
        jsonResponse(true, 'Addon status updated successfully');
        
    } catch (Exception $e) {
        error_log("Toggle Addon Error: " . $e->getMessage());
        jsonResponse(false, 'Failed to update addon status');
    }
}
?>
