<?php
declare(strict_types=1);

header('Content-Type: text/plain; charset=utf-8');

require_once __DIR__ . '/config.php';

try {
    $stmt = $pdo->query('SELECT * FROM tracks');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "OK: SELECT on tracks works.\n";
    echo "Rows returned: " . count($rows) . "\n\n";

    if (count($rows) > 0) {
        echo "Sample output (up to 10 rows):\n";
        echo json_encode(array_slice($rows, 0, 10), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    } else {
        echo "Table is readable, but currently empty.\n";
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo "ERROR: SELECT on tracks failed.\n";
    echo "Message: " . $e->getMessage() . "\n";
}

