<?php
header('Content-Type: application/json');
include_once '../../system/config.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Please login first']);
    exit();
}

try {
    $query = "SELECT id, title, selected FROM tracks ORDER BY title ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $tracks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($tracks);
} catch (PDOException $e) {
    echo json_encode([
        'error' => 'Error fetching tracks: ' . $e->getMessage()
    ]);
}
?>
