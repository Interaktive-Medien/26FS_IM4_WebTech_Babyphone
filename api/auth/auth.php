<?php
// index.php (API that returns JSON about the logged-in user)
session_start();

if (!isset($_SESSION['user_id'])) {
    // Instead of redirect, return a 401 JSON response
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

// If they are logged in:
header('Content-Type: application/json');

// Maybe return the user’s email or user_id here
// (assuming you stored email at login time)
echo json_encode([
    "email" => $_SESSION['email'] ?? null,
    "user_id" => $_SESSION['user_id']
]);
