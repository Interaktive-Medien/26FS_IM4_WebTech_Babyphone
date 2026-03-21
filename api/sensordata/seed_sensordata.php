<?php

/*********************************************************
* api/sensordata/seed_sensordata.php
* - Fügt Demo-Sensordata-Einträge für das erste verbundene Gerät des Benutzers ein (nur wenn noch keine Einträge vorhanden)
* - Vorausgesetzt: Benutzer-Authentifizierung ist gegeben / Session ist aktiv

* Server-seitiger Code: wird auf dem Server ausgeführt
* Aufgerufen clientseitig in js/index.js
* Verwendete Datenbanktabellen: sensordata, user_has_device
*********************************************************/

header('Content-Type: application/json');
include_once '../../system/config.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Please login first']);
    exit();
}

$userId = $_SESSION['user_id'];

try {
    // Get the user's first connected device
    $deviceStmt = $pdo->prepare("SELECT device_id FROM user_has_device WHERE user_id = ? LIMIT 1");
    $deviceStmt->execute([$userId]);
    $deviceRow = $deviceStmt->fetch(PDO::FETCH_ASSOC);

    if (!$deviceRow) {
        echo json_encode(['error' => 'No device connected. Please connect a device first.']);
        exit();
    }

    $deviceId = $deviceRow['device_id'];

    // Only seed if device has zero entries
    $check = $pdo->prepare("SELECT COUNT(*) FROM sensordata WHERE device_id = ?");
    $check->execute([$deviceId]);
    $count = (int) $check->fetchColumn();

    if ($count > 0) {
        echo json_encode(['status' => 'skipped', 'message' => 'Device already has sensordata entries']);
        exit();
    }

    $insert = $pdo->prepare("
        INSERT INTO sensordata (device_id, starttime, endtime) VALUES
        (?, NOW() - INTERVAL 3 DAY + INTERVAL 2 HOUR,  NOW() - INTERVAL 3 DAY + INTERVAL 2 HOUR + INTERVAL 12 MINUTE),
        (?, NOW() - INTERVAL 3 DAY + INTERVAL 5 HOUR,  NOW() - INTERVAL 3 DAY + INTERVAL 5 HOUR + INTERVAL 4 MINUTE),
        (?, NOW() - INTERVAL 2 DAY + INTERVAL 9 HOUR,  NOW() - INTERVAL 2 DAY + INTERVAL 9 HOUR + INTERVAL 7 MINUTE),
        (?, NOW() - INTERVAL 2 DAY + INTERVAL 22 HOUR, NOW() - INTERVAL 2 DAY + INTERVAL 22 HOUR + INTERVAL 15 MINUTE),
        (?, NOW() - INTERVAL 1 DAY + INTERVAL 1 HOUR,  NOW() - INTERVAL 1 DAY + INTERVAL 1 HOUR + INTERVAL 18 MINUTE),
        (?, NOW() - INTERVAL 1 DAY + INTERVAL 14 HOUR, NOW() - INTERVAL 1 DAY + INTERVAL 14 HOUR + INTERVAL 6 MINUTE)
    ");
    $insert->execute([$deviceId, $deviceId, $deviceId, $deviceId, $deviceId, $deviceId]);

    echo json_encode(['status' => 'success', 'message' => '6 demo entries created']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Seed failed: ' . $e->getMessage()]);
}
?>
