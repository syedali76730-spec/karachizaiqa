<?php
require_once 'config.php';
requireLogin();

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'quick_stats':
        getQuickStats();
        break;
    case 'visitor_trend':
        getVisitorTrend();
        break;
    case 'conversion_data':
        getConversionData();
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
}

function getQuickStats() {
    try {
        $conn = getDBConnection();

        $today = date('Y-m-d');
        $stmt = $conn->prepare("
            SELECT COUNT(*) as total_orders, COALESCE(SUM(total_amount), 0) as total_revenue
            FROM orders
            WHERE DATE(created_at) = ?
        ");
        $stmt->execute([$today]);
        $result = $stmt->fetch();

        echo json_encode([
            'visitors_today'  => null,
            'orders_today'    => (int) $result['total_orders'],
            'revenue_today'   => (float) $result['total_revenue'],
            'conversion_rate' => null
        ]);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error']);
    }
}

function getVisitorTrend() {
    echo json_encode([
        'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        'data'   => [45, 52, 48, 67, 89, 134, 98]
    ]);
}

function getConversionData() {
    try {
        $conn = getDBConnection();
        $stmt = $conn->query("
            SELECT COUNT(*) as total_orders
            FROM orders
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        ");
        $result = $stmt->fetch();

        echo json_encode([
            'orders'          => (int) $result['total_orders'],
            'abandoned_carts' => 0,
            'visitors'        => null
        ]);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error']);
    }
}
?>
