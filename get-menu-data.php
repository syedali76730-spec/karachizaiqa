<?php
// get-menu-data.php
// Public API to fetch current menu items, prices, quantities, and addon availability

require_once 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $conn = getDBConnection();
    
    // Fetch active products with current prices and quantities
    $productsStmt = $conn->query("
        SELECT id, name, description, price, quantity, is_active
        FROM products
        WHERE is_active = 1
        ORDER BY id ASC
    ");
    
    $products = [];
    while ($row = $productsStmt->fetch()) {
        $products[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'description' => $row['description'],
            'price' => floatval($row['price']),
            'quantity' => intval($row['quantity']),
            'available' => intval($row['quantity']) > 0
        ];
    }
    
    // Fetch addons with current prices and active status
    $addonsStmt = $conn->query("
        SELECT id, name, price, is_active
        FROM addons
        ORDER BY id ASC
    ");
    
    $addons = [];
    while ($row = $addonsStmt->fetch()) {
        $addons[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'price' => floatval($row['price']),
            'active' => boolval($row['is_active'])
        ];
    }
    
    jsonResponse(true, 'Menu data loaded', [
        'products' => $products,
        'addons' => $addons
    ]);
    
} catch (Exception $e) {
    error_log("Get Menu Data Error: " . $e->getMessage());
    jsonResponse(false, 'Failed to load menu data');
}
?>
