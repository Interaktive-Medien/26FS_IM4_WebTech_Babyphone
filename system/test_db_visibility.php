<?php
declare(strict_types=1);

header('Content-Type: text/plain; charset=utf-8');

require_once __DIR__ . '/config.php';

function normalize_table_name(string $name): string
{
    return strtolower((string)preg_replace('/[^a-zA-Z0-9_]/', '', $name));
}

function quote_identifier(string $identifier): string
{
    return '`' . str_replace('`', '``', $identifier) . '`';
}

try {
    $metaStmt = $pdo->query('SELECT DATABASE() AS db_name, USER() AS db_user, @@hostname AS db_host');
    $meta = $metaStmt->fetch(PDO::FETCH_ASSOC) ?: [];

    echo "=== Connection Info ===\n";
    echo "Database: " . ($meta['db_name'] ?? 'unknown') . "\n";
    echo "User: " . ($meta['db_user'] ?? 'unknown') . "\n";
    echo "Host: " . ($meta['db_host'] ?? 'unknown') . "\n\n";

    echo "=== Visible Tables (with HEX names) ===\n";
    $tablesStmt = $pdo->prepare(
        'SELECT TABLE_NAME, HEX(TABLE_NAME) AS TABLE_NAME_HEX
         FROM information_schema.TABLES
         WHERE TABLE_SCHEMA = DATABASE()
         ORDER BY TABLE_NAME'
    );
    $tablesStmt->execute();
    $tables = $tablesStmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($tables) === 0) {
        echo "(no tables found)\n\n";
    } else {
        foreach ($tables as $tableRow) {
            $name = (string)$tableRow['TABLE_NAME'];
            $hex = (string)$tableRow['TABLE_NAME_HEX'];
            $normalized = normalize_table_name($name);
            echo "- {$name} | HEX={$hex} | normalized={$normalized}\n";
        }
        echo "\n";
    }

    $expectedTables = ['heulhistory', 'tracks', 'users'];
    echo "=== Expected Tables Check ===\n";
    foreach ($expectedTables as $tableName) {
        try {
            $actualTableName = $tableName;

            foreach ($tables as $tableRow) {
                $candidate = (string)$tableRow['TABLE_NAME'];
                if (normalize_table_name($candidate) === $tableName) {
                    $actualTableName = $candidate;
                    break;
                }
            }

            $sql = "SELECT COUNT(*) AS c FROM " . quote_identifier($actualTableName);
            $countStmt = $pdo->query($sql);
            $count = (int)($countStmt->fetch(PDO::FETCH_ASSOC)['c'] ?? 0);
            if ($actualTableName !== $tableName) {
                echo "[OK] {$tableName} reachable as '{$actualTableName}', rows: {$count}\n";
            } else {
                echo "[OK] {$tableName} exists, rows: {$count}\n";
            }
        } catch (Throwable $tableError) {
            echo "[MISSING/NO ACCESS] {$tableName}: " . $tableError->getMessage() . "\n";
        }
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo "ERROR while checking DB visibility.\n";
    echo "Message: " . $e->getMessage() . "\n";
}

