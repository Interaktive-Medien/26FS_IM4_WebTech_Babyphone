<?php
header('Content-Type: application/json');
include_once '../../system/config.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Please login first']);
    exit();
}

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->friendEmail)) {
    echo json_encode(['error' => 'Friend email is required']);
    exit();
}

try {
    // Find friend's user ID
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$data->friendEmail]);
    $friend = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$friend) {
        echo json_encode(['error' => 'User not found']);
        exit();
    }

    // Delete friendship both ways
    $stmt = $pdo->prepare("DELETE FROM friendships WHERE 
        (user_id = ? AND friend_id = ?) OR 
        (user_id = ? AND friend_id = ?)");
    $stmt->execute([
        $_SESSION['user_id'], $friend['id'],
        $friend['id'], $_SESSION['user_id']
    ]);

    echo json_encode(['message' => 'Friend removed successfully']);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Error removing friend']);
}
?>