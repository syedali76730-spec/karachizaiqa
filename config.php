<?php
// Karachi Zaiqa Configuration File
// config.php

// Error Reporting (Set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'u455754869_rix03');  // Change to your database username
define('DB_PASS', 'Pf2468###');      // Change to your database password
define('DB_NAME', 'u455754869_karachi_zaiqa');

// Owner Contact Information
define('OWNER_EMAIL', 'syed.ali.76730@gmail.com');
define('OWNER_PHONE', '+92 309 7480397');
define('BUSINESS_NAME', 'Karachi Zaiqa');

// Pickup Time Range
define('PICKUP_START', '14:00');  // 2:00 PM
define('PICKUP_END', '19:30');    // 7:30 PM

// EmailJS Configuration (Customer will configure)
define('EMAILJS_SERVICE_ID', 'service_i0c2wnu');
define('EMAILJS_TEMPLATE_ID_CUSTOMER', 'template_je5rlah');
define('EMAILJS_TEMPLATE_ID_OWNER', 'template_2tqdwrj');
define('EMAILJS_PUBLIC_KEY', 'X5d7z34vt6I-VihPm');

// Timezone
date_default_timezone_set('Australia/Sydney');

// Database Connection Function
function getDBConnection() {
    try {
        $conn = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $conn;
    } catch(PDOException $e) {
        error_log("Database Connection Error: " . $e->getMessage());
        die("Connection failed. Please try again later.");
    }
}

// Generate Unique Order Number
function generateOrderNumber() {
    return 'KZ' . date('Ymd') . rand(1000, 9999);
}

// Validate Email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Validate Phone (Basic)
function validatePhone($phone) {
    // Remove spaces and check if it contains numbers
    $cleaned = preg_replace('/\s+/', '', $phone);
    return strlen($cleaned) >= 10 && preg_match('/[0-9+]/', $cleaned);
}

// Send Email Notification (Fallback using PHP mail())
function sendEmailNotification($to, $subject, $message) {
    $headers = "From: " . BUSINESS_NAME . " <noreply@karachizaiqa.com>\r\n";
    $headers .= "Reply-To: " . OWNER_EMAIL . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    return mail($to, $subject, $message, $headers);
}

// Format Currency
function formatCurrency($amount) {
    return 'A$' . number_format($amount, 2);
}

// Sanitize Input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Check if user is logged in (Admin)
function isLoggedIn() {
    session_start();
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Require Admin Login
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: admin-login.php');
        exit;
    }
}

// JSON Response Helper
function jsonResponse($success, $message = '', $data = []) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}
?>
