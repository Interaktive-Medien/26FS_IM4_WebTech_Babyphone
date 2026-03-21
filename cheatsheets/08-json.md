# Cheatsheet `JSON in PHP`

JSON (JavaScript Object Notation) ist das Standard-Datenformat für die Kommunikation zwischen PHP-Backend und JavaScript-Frontend.

## json_encode – PHP → JSON

```php
<?php
// Array in JSON umwandeln
$daten = ['status' => 'success', 'user_id' => 42];
echo json_encode($daten);
// gibt aus: {"status":"success","user_id":42}

// Verschachteltes Array
$tracks = [
    ['id' => 1, 'title' => 'Creep'],
    ['id' => 2, 'title' => 'Wonderwall'],
];
echo json_encode($tracks);
// gibt aus: [{"id":1,"title":"Creep"},{"id":2,"title":"Wonderwall"}]
```

## json_decode – JSON → PHP

```php
<?php
// JSON-String in PHP-Array umwandeln
$jsonString = '{"track_id": 3, "selected": 1}';
$daten = json_decode($jsonString, true); // true → assoziatives Array

echo $daten['track_id']; // gibt 3 aus
echo $daten['selected'];  // gibt 1 aus
```

## JSON aus dem Request-Body lesen

So empfängt PHP JSON-Daten, die JavaScript per `fetch()` mit `application/json` schickt:

```php
<?php
$inputJSON = file_get_contents('php://input'); // rohen JSON-String lesen
$input     = json_decode($inputJSON, true);    // in PHP-Array umwandeln

$trackId = $input['track_id'];
```

## Content-Type Header setzen

```php
<?php
// ✅ Immer als erstes setzen – vor jeder Ausgabe
header('Content-Type: application/json');

echo json_encode(['status' => 'ok']);
```

> ⚠️ Wenn `header()` nicht gesetzt wird, gibt PHP `text/html` zurück. JavaScript kann die Antwort dann möglicherweise nicht mit `response.json()` parsen.

## Fehlerantworten

```php
<?php
header('Content-Type: application/json');

// HTTP-Statuscode setzen + Fehlermeldung zurückgeben
http_response_code(401); // 401 Unauthorized
echo json_encode(['error' => 'Nicht eingeloggt']);
exit();
```

Häufige HTTP-Statuscodes:

| Code | Bedeutung             |
| ---- | --------------------- |
| 200  | OK (Standard)         |
| 201  | Created               |
| 400  | Bad Request           |
| 401  | Unauthorized          |
| 404  | Not Found             |
| 500  | Internal Server Error |

### Quellen
- [json_encode](https://www.php.net/manual/de/function.json-encode.php)
- [json_decode](https://www.php.net/manual/de/function.json-decode.php)
- [HTTP Statuscodes](https://developer.mozilla.org/en-US/docs/Web/HTTP/Status)

---

← Zurück: [07 SQL – Daten schreiben](./07-sql-schreiben.md) · → Weiter: [09 Sessions](./09-sessions.md)
