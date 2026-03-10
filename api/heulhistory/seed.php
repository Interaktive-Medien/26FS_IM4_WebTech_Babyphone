<?php
// Seed 3 default heulhistory entries for the logged-in user (only if they have none)
header('Content-Type: application/json');
include_once '../../system/config.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Please login first']);
    exit();
}

$userId = $_SESSION['user_id'];

try {
    // Only seed if user has zero entries
    $check = $pdo->prepare("SELECT COUNT(*) FROM heulhistory WHERE user_id = ?");
    $check->execute([$userId]);
    $count = (int) $check->fetchColumn();

    if ($count > 0) {
        echo json_encode(['status' => 'skipped', 'message' => 'User already has heulhistory entries']);
        exit();
    }

    $insert = $pdo->prepare("
        INSERT INTO heulhistory (user_id, starttime, endtime) VALUES
        (?, NOW() - INTERVAL 3 DAY + INTERVAL 2 HOUR,
            NOW() - INTERVAL 3 DAY + INTERVAL 2 HOUR + INTERVAL 12 MINUTE),
        (?, NOW() - INTERVAL 2 DAY + INTERVAL 9 HOUR,
            NOW() - INTERVAL 2 DAY + INTERVAL 9 HOUR + INTERVAL 7 MINUTE),
        (?, NOW() - INTERVAL 1 DAY + INTERVAL 1 HOUR,
            NOW() - INTERVAL 1 DAY + INTERVAL 1 HOUR + INTERVAL 18 MINUTE)
    ");
    $insert->execute([$userId, $userId, $userId]);

    echo json_encode(['status' => 'success', 'message' => '3 demo entries created']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Seed failed: ' . $e->getMessage()]);
}
?>
