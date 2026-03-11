<?php
header('Content-Type: application/json');
include_once '../../system/config.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Please login first']);
    exit();
}

try {
    $userId = $_SESSION['user_id'];

    // Get the user's first connected device
    $deviceStmt = $pdo->prepare("SELECT device_id FROM user_has_device WHERE user_id = ? LIMIT 1");
    $deviceStmt->execute([$userId]);
    $deviceRow = $deviceStmt->fetch(PDO::FETCH_ASSOC);

    if (!$deviceRow) {
        echo json_encode(['error' => 'No device connected. Please connect a device first.']);
        exit();
    }

    $deviceId = $deviceRow['device_id'];

    // LEFT JOIN so every track appears; selected = 1 when the device has it in device_tracks
    $query = "SELECT t.id, t.title,
                     CASE WHEN dt.device_id IS NOT NULL THEN 1 ELSE 0 END AS selected
              FROM tracks t
              LEFT JOIN device_tracks dt ON dt.track_id = t.id AND dt.device_id = ?
              ORDER BY t.title ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$deviceId]);

    $tracks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($tracks);
} catch (PDOException $e) {
    echo json_encode([
        'error' => 'Error fetching tracks: ' . $e->getMessage()
    ]);
}
?>
