<?php
// List all devices connected to the logged-in user
header('Content-Type: application/json');
include_once '../../system/config.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Please login first']);
    exit();
}

try {
    $stmt = $pdo->prepare("
        SELECT d.id, d.device_code
        FROM devices d
        INNER JOIN user_has_device uhd ON uhd.device_id = d.id
        WHERE uhd.user_id = ?
        ORDER BY uhd.id ASC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $devices = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($devices);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error fetching devices: ' . $e->getMessage()]);
}
?>
