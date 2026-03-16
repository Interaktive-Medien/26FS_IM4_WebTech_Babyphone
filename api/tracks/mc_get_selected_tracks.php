<?php
 /*************************************************************
 * api/sensordata/get_selected_tracks.php
 * This script receives HTTP GET messages from the mc. It asks for 
 * data from database. Then it passes them to mc as a JSON string 
 * 
 * Server-seitiger Code: wird auf dem Server ausgeführt
 * Aufgerufen clientseitig am ESP32 (mc.ino) beim Starten des Geräts
 * verwendete Datenbanktabellen: tracks, device_tracks
 *************************************************************/

include_once '../../system/config.php';

header('Content-Type: application/json'); // sets Content-Type of the answer to JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, true);
    $device_id = $input["device_id"];  // mc muss bei der Anfrage eine device_id mitschicken

    try{ 
        $sql = "SELECT t.id, t.title FROM tracks t JOIN device_tracks dt ON t.id = dt.track_id WHERE dt.device_id = :device_id;"; 
        $stmt = $pdo->prepare($sql);
        $stmt->execute(
            ['device_id' => $device_id]
        );
        $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($response);
    }
    catch (Exception $e) {
        echo json_encode([
            "status" => "error", 
            "message" => "Database error: " . $e->getMessage()
        ]);
        exit;
    }
}
?>