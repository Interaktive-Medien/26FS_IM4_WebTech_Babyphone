<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

include_once '../../system/config.php';

// Start session and check authentication
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized - Please login first']);
    exit();
}

$data = json_decode(file_get_contents("php://input"));

if(!isset($data->task_id)) {
    echo json_encode(['error' => 'Missing task_id']);
    exit();
}

try {
    // Use the logged-in user's ID from session instead of requiring it in the request
    $query = "INSERT INTO user_has_task (user_id, task_id) VALUES (?, ?)";
    $stmt = $pdo->prepare($query);
    
    if($stmt->execute([$_SESSION['user_id'], $data->task_id])) {
        echo json_encode(['message' => 'Task completed successfully']);
    }
} catch(PDOException $e) {
    echo json_encode([
        'error' => 'Error completing task: ' . $e->getMessage()
    ]);
}
?>