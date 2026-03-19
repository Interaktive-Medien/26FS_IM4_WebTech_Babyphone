# Cheatsheet `API-Endpunkt bauen`

Ein API-Endpunkt ist eine PHP-Datei, die Daten als JSON zurückgibt. Alle Endpunkte in diesem Projekt folgen dem gleichen Aufbau.

## Aufbau eines GET-Endpunkts (Daten lesen)

```php
<?php
/*
 * api/sensordata/read_sensordata.php
 * Gibt alle Weinevents des eingeloggten Users zurück
 */

// 1. Header setzen – immer als erstes
header('Content-Type: application/json');

// 2. Datenbankverbindung laden
include_once '../../system/config.php';

// 3. Session prüfen (Auth-Check)
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Please login first']);
    exit();
}

// 4. Daten aus der DB laden
$userId = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("
        SELECT s.id, s.starttime, s.endtime
        FROM sensordata s
        INNER JOIN user_has_device uhd ON uhd.device_id = s.device_id
        WHERE uhd.user_id = ?
        ORDER BY s.starttime DESC
    ");
    $stmt->execute([$userId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 5. JSON zurückgeben
    echo json_encode($rows);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
```

## Aufbau eines POST-Endpunkts (Daten schreiben)

```php
<?php
/*
 * api/profile/update_profile.php
 * Ändert den Namen des eingeloggten Users
 */

header('Content-Type: application/json');
include_once '../../system/config.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Please login first']);
    exit();
}

// JSON-Body lesen
$input = json_decode(file_get_contents('php://input'), true);

// Eingabe prüfen
if (!isset($input['name']) || trim($input['name']) === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Name fehlt']);
    exit();
}

$neuName = trim($input['name']);
$userId  = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("UPDATE users SET name = :name WHERE id = :id");
    $stmt->execute([':name' => $neuName, ':id' => $userId]);

    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
```

## Vom Frontend aufrufen (JavaScript)

```javascript
// GET-Endpunkt aufrufen
const response = await fetch("api/sensordata/read_sensordata.php", {
    credentials: "include", // Session-Cookie mitsenden!
});
const data = await response.json();

// POST-Endpunkt aufrufen
const response = await fetch("api/profile/update_profile.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    credentials: "include",
    body: JSON.stringify({ name: "Neuer Name" }),
});
const result = await response.json();
```

## Checkliste für jeden Endpunkt

- [ ] `header('Content-Type: application/json')` als erstes
- [ ] `include_once` für `config.php`
- [ ] `session_start()` + Auth-Check falls geschützt
- [ ] Eingabe-Validierung vor der DB-Abfrage
- [ ] Prepared Statement (kein direktes Einsetzen von Variablen in SQL)
- [ ] `try/catch` um die DB-Abfrage
- [ ] `echo json_encode(...)` als Antwort

### Quellen
- [REST API Design](https://restfulapi.net/)
- [PDO Prepared Statements](https://www.php.net/manual/de/pdo.prepared-statements.php)
- [HTTP Status Codes](https://developer.mozilla.org/en-US/docs/Web/HTTP/Status)
