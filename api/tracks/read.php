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

    // LEFT JOIN so every track appears; selected = 1 when the user has it in user_tracks
    $query = "SELECT t.id, t.title,
                     CASE WHEN ut.user_id IS NOT NULL THEN 1 ELSE 0 END AS selected
              FROM tracks t
              LEFT JOIN user_tracks ut ON ut.track_id = t.id AND ut.user_id = ?
              ORDER BY t.title ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$userId]);

    $tracks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($tracks);
} catch (PDOException $e) {
    echo json_encode([
        'error' => 'Error fetching tracks: ' . $e->getMessage()
    ]);
}
?>
