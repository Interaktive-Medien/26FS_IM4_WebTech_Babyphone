# Cheatsheet `SQL – Daten schreiben (INSERT, UPDATE, DELETE)`

## INSERT – Neuen Eintrag erstellen

```php
<?php
$name  = "Anna";
$email = "anna@example.com";
$pass  = password_hash("geheim123", PASSWORD_DEFAULT); // Passwort hashen!

$stmt = $pdo->prepare("
    INSERT INTO users (name, email, password)
    VALUES (:name, :email, :pass)
");
$stmt->execute([
    ':name'  => $name,
    ':email' => $email,
    ':pass'  => $pass,
]);

// ID des neu erstellten Eintrags bekommen
$newId = $pdo->lastInsertId();
echo json_encode(['status' => 'success', 'id' => $newId]);
```

## UPDATE – Bestehenden Eintrag ändern

```php
<?php
$userId  = $_SESSION['user_id'];
$neuName = $input['name'];

$stmt = $pdo->prepare("
    UPDATE users
    SET name = :name
    WHERE id = :id
");
$stmt->execute([
    ':name' => $neuName,
    ':id'   => $userId,
]);

echo json_encode(['status' => 'success']);
```

> ⚠️ Immer eine `WHERE`-Bedingung bei `UPDATE` und `DELETE` angeben – sonst werden **alle** Zeilen der Tabelle verändert/gelöscht!

## DELETE – Eintrag löschen

```php
<?php
$deviceId = $input['device_id'];
$userId   = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    DELETE FROM user_has_device
    WHERE device_id = :device_id AND user_id = :user_id
");
$stmt->execute([
    ':device_id' => $deviceId,
    ':user_id'   => $userId,
]);

echo json_encode(['status' => 'success']);
```

## Betroffene Zeilen prüfen

```php
<?php
$stmt = $pdo->prepare("DELETE FROM user_has_device WHERE device_id = :id");
$stmt->execute([':id' => $deviceId]);

$betroffeneZeilen = $stmt->rowCount();

if ($betroffeneZeilen === 0) {
    echo json_encode(['error' => 'Gerät nicht gefunden']);
} else {
    echo json_encode(['status' => 'success']);
}
```

## Vollständiges Beispiel: Gerät verbinden

```php
<?php
include_once '../../system/config.php';
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Nicht eingeloggt']);
    exit();
}

$input    = json_decode(file_get_contents('php://input'), true);
$code     = $input['device_code'];
$userId   = $_SESSION['user_id'];

try {
    // 1. Gerät suchen
    $stmt = $pdo->prepare("SELECT id FROM devices WHERE device_code = :code");
    $stmt->execute([':code' => $code]);
    $device = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$device) {
        echo json_encode(['error' => 'Gerät nicht gefunden']);
        exit();
    }

    // 2. Verbindung erstellen
    $stmt = $pdo->prepare("INSERT INTO user_has_device (user_id, device_id) VALUES (:uid, :did)");
    $stmt->execute([':uid' => $userId, ':did' => $device['id']]);

    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
```

### Quellen
- [INSERT Statement](https://dev.mysql.com/doc/refman/8.0/en/insert.html)
- [UPDATE Statement](https://dev.mysql.com/doc/refman/8.0/en/update.html)
- [DELETE Statement](https://dev.mysql.com/doc/refman/8.0/en/delete.html)
- [lastInsertId()](https://www.php.net/manual/de/pdo.lastinsertid.php)
- [rowCount()](https://www.php.net/manual/de/pdostatement.rowcount.php)

---

← Zurück: [06 SQL – Daten lesen](./06-sql-lesen.md) · → Weiter: [08 JSON in PHP](./08-json.md)
