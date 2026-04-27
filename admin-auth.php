<?php
// admin-auth.php
// Handle admin login authentication

require_once 'config.php';

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method');
}

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || empty($data['email']) || empty($data['password'])) {
    jsonResponse(false, 'Email and password are required');
}

$email = sanitizeInput($data['email']);
$password = $data['password'];

try {
    $conn = getDBConnection();
    
    // Get admin user
    $stmt = $conn->prepare("SELECT id, email, password_hash, name, is_active FROM admin_users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user) {
        jsonResponse(false, 'Invalid email or password');
    }
    
    if (!$user['is_active']) {
        jsonResponse(false, 'Account is disabled');
    }
    
    // Verify password
    if (!password_verify($password, $user['password_hash'])) {
        jsonResponse(false, 'Invalid email or password');
    }
    
    // Start session and store user info
    session_start();
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_id'] = $user['id'];
    $_SESSION['admin_email'] = $user['email'];
    $_SESSION['admin_name'] = $user['name'];
    
    jsonResponse(true, 'Login successful', [
        'email' => $user['email'],
        'name' => $user['name']
    ]);
    
} catch (Exception $e) {
    error_log("Login Error: " . $e->getMessage());
    jsonResponse(false, 'Login failed. Please try again.');
}
?>
