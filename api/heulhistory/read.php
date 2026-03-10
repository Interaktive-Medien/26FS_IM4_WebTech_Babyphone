<?php
header('Content-Type: application/json');
include_once '../../system/config.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Please login first']);
    exit();
}

try {
    $query = "SELECT id, starttime, endtime
              FROM heulhistory
              WHERE user_id = ?
              ORDER BY starttime DESC";
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
