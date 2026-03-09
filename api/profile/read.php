<?php
header('Content-Type: application/json');
include_once '../../system/config.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Please login first']);
    exit();
}

try {
    // First, just get basic user info
    $userQuery = "SELECT name FROM users WHERE id = ?";
    $stmt = $pdo->prepare($userQuery);
    $stmt->execute([$_SESSION['user_id']]);
    $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$userInfo) {
        echo json_encode(['error' => 'User not found']);
        exit();
    }

    // Then get total score
    $scoreQuery = "SELECT COALESCE(SUM(t.score), 0) as total_score
                   FROM users u
                   LEFT JOIN user_has_task uht ON u.id = uht.user_id
                   LEFT JOIN tasks t ON uht.task_id = t.id
                   WHERE u.id = ?";
    
    $stmt = $pdo->prepare($scoreQuery);
    $stmt->execute([$_SESSION['user_id']]);
    $scoreInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    $userInfo['total_score'] = $scoreInfo['total_score'];

    // Finally get activities - changed created_at to timestamp
    $activitiesQuery = "SELECT t.name, t.score, t.emoji, uht.timestamp
                       FROM user_has_task uht
                       JOIN tasks t ON uht.task_id = t.id
                       WHERE uht.user_id = ?
                       ORDER BY uht.timestamp DESC
                       LIMIT 10";
    
    $stmt = $pdo->prepare($activitiesQuery);
    $stmt->execute([$_SESSION['user_id']]);
    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'user' => $userInfo,
        'activities' => $activities
    ]);
} catch(PDOException $e) {
    echo json_encode([
        'error' => 'Database error: ' . $e->getMessage(),
        'details' => $e->getTrace()
    ]);
}
?>