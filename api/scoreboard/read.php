<?php
header('Content-Type: application/json');
include_once '../../system/config.php';

// Check if user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Please login first']);
    exit();
}

try {
    // Get scores for user and their friends
    $query = "SELECT u.name, u.email, COALESCE(SUM(t.score), 0) as total_score 
              FROM users u 
              LEFT JOIN user_has_task uht ON u.id = uht.user_id 
              LEFT JOIN tasks t ON uht.task_id = t.id 
              WHERE u.id = ? OR u.id IN (
                  SELECT friend_id FROM friendships WHERE user_id = ?
              )
              GROUP BY u.name, u.email
              ORDER BY total_score DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
    
    $scores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Add current user's email to each score row
    $currentUserEmail = $_SESSION['email'];  // Make sure this exists in session
    foreach ($scores as &$score) {
        $score['currentUserEmail'] = $currentUserEmail;
    }

    echo json_encode($scores);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Error loading scoreboard']);
}
?>