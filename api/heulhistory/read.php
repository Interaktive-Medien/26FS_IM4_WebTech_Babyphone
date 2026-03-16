<?php

/*********************************************************
* api/heulhistory/read.php
* - Liest Heulhistorie aller dem Benutzer zugeordneten Geräte
* - Sortiert Ergebnisse nach Startzeit (absteigend)
* - Gibt Daten bzw. Fehler als JSON zurück
* - vorausgesetzt: Benutzer-Authentifizierung ist gegeben / Session ist aktiv (Prüfung zu Beginn)

* Server-seitiger Code: wird auf dem Server ausgeführt
* Aufgerufen clientseitig in js/index.js; durch ein Client-Login-Formular (index.html)
* Server-Interaktion mit: ../../system/config.php (PDO/DB-Verbindung), PHP-Session
* Verwendete Datenbanktabellen: heulhistory, user_has_device
*********************************************************/

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
