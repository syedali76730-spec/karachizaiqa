<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

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
    global $conn;

    $today = date('Y-m-d');
    $stmt = $conn->prepare("
        SELECT COUNT(*) as total_orders, COALESCE(SUM(total_amount), 0) as total_revenue
        FROM orders
        WHERE DATE(created_at) = ?
    ");
    $stmt->bind_param('s', $today);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    $orders_today = (int) $result['total_orders'];
    $revenue_today = (float) $result['total_revenue'];

    // visitors_today requires GA4 Data API integration; placeholder until configured
    $visitors_today = null;
    $conversion_rate = null;

    echo json_encode([
        'visitors_today'  => $visitors_today,
        'orders_today'    => $orders_today,
        'revenue_today'   => $revenue_today,
        'conversion_rate' => $conversion_rate
    ]);
}

function getVisitorTrend() {
    // Placeholder — replace with GA4 Data API once credentials are configured
    echo json_encode([
        'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        'data'   => [45, 52, 48, 67, 89, 134, 98]
    ]);
}

function getConversionData() {
    global $conn;

    $stmt = $conn->query("
        SELECT COUNT(*) as total_orders
        FROM orders
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ");
    $result = $stmt->fetch_assoc();

    echo json_encode([
        'orders'         => (int) $result['total_orders'],
        'abandoned_carts' => 0,
        'visitors'        => null
    ]);
}
?>
