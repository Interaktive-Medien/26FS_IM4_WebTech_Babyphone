<?php
 /*************************************************************
 * get_selected_tracks.php
 * This script receives HTTP GET messages from the mc. It asks for 
 * data from database. Then it passes them to mc as a JSON string 
 * 
 * Server-seitiger Code: wird auf dem Server ausgeführt
 * Aufgerufen clientseitig am ESP32 (mc.ino) beim Starten des Geräts
 * verwendete Datenbanktabellen: device_tracks
 *************************************************************/


require_once("config.php");


header('Content-Type: application/json'); // sets Content-Type of the answer to JSON


try{ 
    $sql = "SELECT id, title FROM tracks WHERE selected = 1"; // SQL query to select all tracks where 'selected' is 1
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($data);
}
catch (Exception $e) {
    echo json_encode([
        "status" => "error", 
        "message" => "Database error: " . $e->getMessage()
    ]);
    exit;
}
?>