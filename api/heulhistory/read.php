<?php
header('Content-Type: application/json');
include_once '../../system/config.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Please login first']);
    exit();
}

try {
    // Fetch heulhistory for all devices connected to this user
    $query = "SELECT h.id, h.starttime, h.endtime
              FROM heulhistory h
              INNER JOIN user_has_device uhd ON uhd.device_id = h.device_id
              WHERE uhd.user_id = ?
              ORDER BY h.starttime DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$_SESSION['user_id']]);

    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($history);
} catch (PDOException $e) {
    echo json_encode([
        'error' => 'Error fetching heulhistory: ' . $e->getMessage()
    ]);
}
?>
