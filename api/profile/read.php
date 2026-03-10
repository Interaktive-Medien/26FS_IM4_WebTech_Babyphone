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

    // Then get total cries
    $scoreQuery = "SELECT COUNT(*) as total_cries
                   FROM heulhistory
                   WHERE user_id = ?";
    
    $stmt = $pdo->prepare($scoreQuery);
    $stmt->execute([$_SESSION['user_id']]);
    $scoreInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    $userInfo['total_cries'] = (int)$scoreInfo['total_cries'];

    // Finally get latest crying events
    $activitiesQuery = "SELECT starttime, endtime
                       FROM heulhistory
                       WHERE user_id = ?
                       ORDER BY starttime DESC
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