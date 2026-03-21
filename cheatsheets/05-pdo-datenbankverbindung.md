# Cheatsheet `PDO – Datenbankverbindung`

PDO (PHP Data Objects) ist die empfohlene Methode, um in PHP mit einer MySQL/MariaDB-Datenbank zu kommunizieren.

## Verbindung herstellen

```php
<?php
$host = 'localhost';
$db   = 'meine_datenbank';
$user = 'mein_user';
$pass = 'mein_passwort';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'DB-Verbindung fehlgeschlagen']);
    exit();
}
```

> In diesem Projekt steht die Verbindung in `system/config.php`. API-Dateien binden diese mit `include_once` ein, anstatt die Verbindung jedes Mal neu zu schreiben.

```php
<?php
// So wird config.php in einer API-Datei eingebunden
include_once '../../system/config.php'; // Pfad relativ zur aktuellen Datei
// Ab hier ist $pdo verfügbar
```

## Warum PDO und nicht direkt SQL?

```php
<?php
// ❌ SQL-Injection möglich – NIEMALS so machen!
$email = $_POST['email'];
$result = $pdo->query("SELECT * FROM users WHERE email = '$email'");
// Angreifer könnte email = "' OR '1'='1" eingeben → alle User werden zurückgegeben

// ✅ Prepared Statement – sicher
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute([':email' => $email]);
```

Prepared Statements trennen den SQL-Code von den Daten – so können Angreifer keinen eigenen SQL-Code einschleusen.

## try/catch für Fehlerbehandlung

```php
<?php
try {
    $stmt = $pdo->prepare("SELECT * FROM users");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Datenbankfehler: ' . $e->getMessage()]);
}
```

### Quellen
- [PDO Einführung](https://www.php.net/manual/de/intro.pdo.php)
- [PDO Verbindung](https://www.php.net/manual/de/pdo.construct.php)
- [Prepared Statements](https://www.php.net/manual/de/pdo.prepared-statements.php)

---

← Zurück: [04 POST & GET](./04-post-get.md) · → Weiter: [06 SQL – Daten lesen](./06-sql-lesen.md)
