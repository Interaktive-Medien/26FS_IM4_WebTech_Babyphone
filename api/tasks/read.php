<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../../system/config.php';

try {
    $query = "SELECT * FROM tasks";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($tasks);
} catch(PDOException $e) {
    echo json_encode([
        'error' => 'Error fetching tasks: ' . $e->getMessage()
    ]);
}
?>