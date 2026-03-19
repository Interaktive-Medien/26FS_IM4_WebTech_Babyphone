# Cheatsheet `POST & GET – Daten empfangen`

Wenn JavaScript Daten an eine PHP-Datei schickt, kann PHP diese über `$_POST`, `$_GET` oder den Request-Body empfangen.

## GET – Daten in der URL

```
https://example.com/api/tracks/read.php?device_id=3&limit=10
```

```php
<?php
// Wert aus der URL lesen
$deviceId = $_GET['device_id']; // gibt "3" zurück
$limit    = $_GET['limit'];     // gibt "10" zurück

// Sicher mit Prüfung
if (!isset($_GET['device_id'])) {
    echo json_encode(['error' => 'device_id fehlt']);
    exit();
}
```

> GET-Parameter sind in der URL sichtbar → **niemals** für Passwörter oder sensible Daten verwenden.

## POST – Formulardaten (application/x-www-form-urlencoded)

So schickt JavaScript Formulardaten:

```javascript
// JavaScript (Frontend)
fetch("api/auth/login.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: new URLSearchParams({ email: "anna@test.ch", password: "123" }),
});
```

```php
<?php
// PHP (Backend) empfängt die Daten
$email    = $_POST['email'];    // gibt "anna@test.ch" zurück
$password = $_POST['password']; // gibt "123" zurück
```

## POST – JSON-Body (application/json)

So schickt JavaScript JSON-Daten:

```javascript
// JavaScript (Frontend)
fetch("api/tracks/update_selected_tracks.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ track_id: 3, selected: 1 }),
});
```

```php
<?php
// PHP (Backend) – JSON aus dem Request-Body lesen
$inputJSON = file_get_contents('php://input'); // rohen JSON-String lesen
$input     = json_decode($inputJSON, true);    // in PHP-Array umwandeln

$trackId  = $input['track_id'];  // gibt 3 zurück
$selected = $input['selected'];  // gibt 1 zurück
```

## Methode prüfen

```php
<?php
// Nur POST-Requests erlauben
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Nur POST erlaubt']);
    exit();
}
```

### Quellen
- [$_POST](https://www.php.net/manual/de/reserved.variables.post.php)
- [$_GET](https://www.php.net/manual/de/reserved.variables.get.php)
- [file_get_contents('php://input')](https://www.php.net/manual/de/wrappers.php.php)
- [json_decode](https://www.php.net/manual/de/function.json-decode.php)

---

← Zurück: [03 Bedingungen](./03-bedingungen.md) · → Weiter: [05 PDO – Datenbankverbindung](./05-pdo-datenbankverbindung.md)
