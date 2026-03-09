<?php
header('Content-Type: application/json');
include_once '../../system/config.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Please login first']);
    exit();
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if (empty($search)) {
    echo json_encode([]);
    exit();
}

try {
    // Get users that match the search term and are not friends
    $query = "SELECT u.email, u.name 
              FROM users u 
              WHERE (u.email LIKE ? OR u.name LIKE ?)
              AND u.id != ?
              AND u.id NOT IN (
                  SELECT friend_id 
                  FROM friendships 
                  WHERE user_id = ?
              )
              LIMIT 5";
    
    $searchTerm = "%$search%";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$searchTerm, $searchTerm, $_SESSION['user_id'], $_SESSION['user_id']]);
    
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Error searching users']);
}
?>
