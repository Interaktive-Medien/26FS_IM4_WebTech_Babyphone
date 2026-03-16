<?php



// // Seed 3 default heulhistory entries for the user's first connected device (only if it has none)
// header('Content-Type: application/json');
// include_once '../../system/config.php';

// session_start();
// if (!isset($_SESSION['user_id'])) {
//     http_response_code(401);
//     echo json_encode(['error' => 'Please login first']);
//     exit();
// }

// $userId = $_SESSION['user_id'];

// try {
//     // Get the user's first connected device
//     $deviceStmt = $pdo->prepare("SELECT device_id FROM user_has_device WHERE user_id = ? LIMIT 1");
//     $deviceStmt->execute([$userId]);
//     $deviceRow = $deviceStmt->fetch(PDO::FETCH_ASSOC);

//     if (!$deviceRow) {
//         echo json_encode(['error' => 'No device connected. Please connect a device first.']);
//         exit();
//     }

//     $deviceId = $deviceRow['device_id'];

//     // Only seed if device has zero entries
//     $check = $pdo->prepare("SELECT COUNT(*) FROM heulhistory WHERE device_id = ?");
//     $check->execute([$deviceId]);
//     $count = (int) $check->fetchColumn();

//     if ($count > 0) {
//         echo json_encode(['status' => 'skipped', 'message' => 'Device already has heulhistory entries']);
//         exit();
//     }

//     $insert = $pdo->prepare("
//         INSERT INTO heulhistory (device_id, starttime, endtime) VALUES
//         (?, NOW() - INTERVAL 3 DAY + INTERVAL 2 HOUR,
//             NOW() - INTERVAL 3 DAY + INTERVAL 2 HOUR + INTERVAL 12 MINUTE),
//         (?, NOW() - INTERVAL 2 DAY + INTERVAL 9 HOUR,
//             NOW() - INTERVAL 2 DAY + INTERVAL 9 HOUR + INTERVAL 7 MINUTE),
//         (?, NOW() - INTERVAL 1 DAY + INTERVAL 1 HOUR,
//             NOW() - INTERVAL 1 DAY + INTERVAL 1 HOUR + INTERVAL 18 MINUTE)
//     ");
//     $insert->execute([$deviceId, $deviceId, $deviceId]);

//     echo json_encode(['status' => 'success', 'message' => '3 demo entries created']);
// } catch (PDOException $e) {
//     http_response_code(500);
//     echo json_encode(['error' => 'Seed failed: ' . $e->getMessage()]);
// }
// ?>
