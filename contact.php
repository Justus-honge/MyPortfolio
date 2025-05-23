<?php
header('Content-Type: application/json');

// Configuration: update these with your database credentials
$host = 'localhost';
$db   = 'portfolio_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

// Connect to database
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Get and sanitize inputs
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

// Validate inputs
if (!$name || strlen($name) < 2) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please provide a valid name (at least 2 characters).']);
    exit;
}

if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please provide a valid email address.']);
    exit;
}

if (!$message || strlen($message) < 10) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please provide a message of at least 10 characters.']);
    exit;
}

// Insert into database
try {
    $stmt = $pdo->prepare('INSERT INTO contact_messages (name, email, message, submitted_at) VALUES (?, ?, ?, NOW())');
    $stmt->execute([$name, $email, $message]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save your message. Please try again later.']);
    exit;
}

// Success
echo json_encode(['success' => true, 'message' => 'Message sent successfully! Thank you for contacting me.']);
