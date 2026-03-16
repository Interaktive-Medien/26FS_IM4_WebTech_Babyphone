<?php

/*********************************************************
* api/device/list.php
* Haupt-Funktionalitäten:
* - Gibt eine Liste aller Geräte des eingeloggten Benutzers als JSON zurück
* - vorausgesetzt: Benutzer-Authentifizierung ist gegeben / Session ist aktiv (Prüfung zu Beginn)

* Server-seitiger Code: wird auf dem Server ausgeführt (API-Endpunkt)
* verwendet Datenbanktabellen: devices, user_has_device
* Client-seitig aufgerufen in: 
* verwendete Datenbanktabellen: devices, user_has_device
*********************************************************/

// header('Content-Type: application/json');
// include_once '../../system/config.php';

// session_start();
// if (!isset($_SESSION['user_id'])) {
//     http_response_code(401);
//     echo json_encode(['error' => 'Please login first']);
//     exit();
// }

// try {
//     $stmt = $pdo->prepare("
//         SELECT d.id, d.device_code
//         FROM devices d
//         INNER JOIN user_has_device uhd ON uhd.device_id = d.id
//         WHERE uhd.user_id = ?
//         ORDER BY uhd.id ASC
//     ");
//     $stmt->execute([$_SESSION['user_id']]);
//     $devices = $stmt->fetchAll(PDO::FETCH_ASSOC);

//     echo json_encode($devices);
// } catch (PDOException $e) {
//     http_response_code(500);
//     echo json_encode(['error' => 'Error fetching devices: ' . $e->getMessage()]);
// }
