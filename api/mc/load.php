<?php

 /*************************************************************
 * load.php
 * - receive data as a JSON string from the mc on the server 
 * - insert into database (-> Tabelle sensordata)
 
 * Server-seitiger Code: wird auf dem Server ausgeführt
 * Aufgerufen clientseitig am ESP32 (mc.ino)
 * verwendete Datenbanktabellen: sensordata
 *************************************************************/

include_once '../../system/config.php';
// echo "This script receives HTTP POST messages and pushes their content into the database.";



###################################### receive JSON data

$inputJSON = file_get_contents('php://input'); // JSON-Daten aus dem Body der Anfrage
$input = json_decode($inputJSON, true); // Dekodieren der JSON-Daten in ein Array

$is_screaming = $input["is_heulsession"];
$scream_id = $input["heulsession_id"];   

try{ 
    if ($is_screaming == 1){
        $sql = "INSERT INTO sensordata (starttime) VALUES (NOW())";
        $result = $pdo->prepare($sql);
        $result->execute();
        $scream_id = $pdo->lastInsertId();
        echo json_encode([
            "status" => "success", 
            "message" => "Session started", 
            "heulsession_id" => $scream_id
        ]);
    }
    else if ($is_screaming == 0){
        $sql = "UPDATE sensordata SET endtime = NOW() WHERE id = :heulsession_id";
        $result = $pdo->prepare($sql);
        $result->execute(['heulsession_id' => $scream_id]);
        echo json_encode([
            "status" => "success", 
            "message" => "Session ended", 
            "heulsession_id" => $scream_id
        ]);
    }
} 

catch (Exception $e) {
    echo "Database error: " . $e->getMessage();
    exit;
}
?>