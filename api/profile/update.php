<?php
header('Content-Type: application/json');
include_once '../../system/config.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Please login first']);
    exit();
}

// Get POST data
$data = json_decode(file_get_contents("php://input"));

if (!isset($data->name) || empty(trim($data->name))) {
    echo json_encode(['error' => 'Name is required']);
    exit();
}

try {
    $stmt = $pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
    $stmt->execute([trim($data->name), $_SESSION['user_id']]);
    
    echo json_encode(['success' => true, 'message' => 'Name updated successfully']);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Error updating name: ' . $e->getMessage()]);
}
?>