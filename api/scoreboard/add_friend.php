<?php
header('Content-Type: application/json');
include_once '../../system/config.php';

// Check if user is logged in
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

    // Add friendship both ways
    $stmt = $pdo->prepare("INSERT IGNORE INTO friendships (user_id, friend_id) VALUES (?, ?), (?, ?)");
    $stmt->execute([
        $_SESSION['user_id'], $friend['id'],
        $friend['id'], $_SESSION['user_id']
    ]);

    echo json_encode(['message' => 'Friend added successfully']);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Error adding friend']);
}
?>