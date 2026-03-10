<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
include_once '../../system/config.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Please login first']);
    exit();
}

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->track_id) || !isset($data->selected)) {
    echo json_encode(['error' => 'track_id and selected are required']);
    exit();
}

try {
    $query = "UPDATE tracks SET selected = ? WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([(int)!!$data->selected, (int)$data->track_id]);

    echo json_encode(['success' => true, 'message' => 'Track setting updated']);
} catch (PDOException $e) {
    echo json_encode([
        'error' => 'Error updating track: ' . $e->getMessage()
    ]);
}
?>
