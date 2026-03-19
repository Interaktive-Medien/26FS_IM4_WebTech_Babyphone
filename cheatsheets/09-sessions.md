# Cheatsheet `Sessions`

Sessions ermöglichen es dem Server, sich zwischen verschiedenen HTTP-Requests zu "merken", wer eingeloggt ist. HTTP ist von sich aus zustandslos – ohne Sessions vergisst der Server nach jedem Request, wer du bist.

## Wie Sessions funktionieren

```
1. User loggt sich ein
2. Server erstellt eine Session und speichert darin die user_id
3. Server schickt ein Session-Cookie (PHPSESSID) an den Browser
4. Browser schickt dieses Cookie bei jedem weiteren Request automatisch mit
5. Server liest das Cookie → findet die Session → weiss wieder wer eingeloggt ist
```

## session_start()

```php
<?php
// Muss am Anfang jeder PHP-Datei stehen, die Sessions nutzt
// Muss vor jeder Ausgabe (echo, header) aufgerufen werden
session_start();
```

## Session-Werte setzen (beim Login)

```php
<?php
session_start();

// Nach erfolgreichem Login: User-Daten in Session speichern
session_regenerate_id(true); // neue Session-ID generieren (Sicherheit!)
$_SESSION['user_id'] = $user['id'];
$_SESSION['email']   = $user['email'];

echo json_encode(['status' => 'success']);
```

## Session-Werte lesen

```php
<?php
session_start();

// Prüfen ob User eingeloggt ist
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Bitte einloggen']);
    exit();
}

// User-ID aus Session lesen
$userId = $_SESSION['user_id'];
```

## Session beenden (beim Logout)

```php
<?php
session_start();

$_SESSION = [];        // Alle Session-Daten löschen
session_destroy();     // Session auf dem Server zerstören

echo json_encode(['status' => 'success']);
```

## Das Auth-Pattern

Dieses Muster steht am Anfang jeder geschützten API-Datei in diesem Projekt:

```php
<?php
header('Content-Type: application/json');
include_once '../../system/config.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Please login first']);
    exit();
}

// Ab hier: User ist eingeloggt, $userId ist verfügbar
$userId = $_SESSION['user_id'];

// ... Datenbankabfragen etc.
```

### Quellen
- [session_start()](https://www.php.net/manual/de/function.session-start.php)
- [PHP Sessions](https://www.php.net/manual/de/book.session.php)
- [session_regenerate_id()](https://www.php.net/manual/de/function.session-regenerate-id.php)

---

← Zurück: [08 JSON in PHP](./08-json.md) · → Weiter: [10 Passwort-Hashing](./10-passwort-hashing.md)
