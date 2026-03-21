# Cheatsheet `Bedingungen`

## if / else

```php
<?php
$alter = 20;

if ($alter >= 18) {
    echo "Volljährig";
} else {
    echo "Minderjährig";
}
```

## Vergleichsoperatoren

| Operator | Bedeutung              | Beispiel            |
| -------- | ---------------------- | ------------------- |
| `==`     | gleich (nur Wert)      | `"5" == 5` → true   |
| `===`    | identisch (Wert + Typ) | `"5" === 5` → false |
| `!=`     | ungleich               | `$a != $b`          |
| `>`      | grösser als            | `$a > $b`           |
| `<`      | kleiner als            | `$a < $b`           |
| `>=`     | grösser oder gleich    | `$a >= $b`          |

> ⚠️ In PHP (und JavaScript) ist `===` sicherer als `==`, da es auch den Datentyp prüft.

## isset() – prüfen ob eine Variable existiert

In API-Dateien wird `isset()` sehr oft gebraucht, um zu prüfen ob Daten mitgeschickt wurden:

```php
<?php
// ❌ ohne Prüfung → PHP-Fehler, falls $_POST['email'] nicht existiert
$email = $_POST['email'];

// ✅ mit Prüfung → sicher
if (isset($_POST['email'])) {
    $email = $_POST['email'];
} else {
    echo json_encode(['error' => 'Email fehlt']);
    exit();
}
```

## Logische Operatoren

```php
<?php
$istAdmin = true;
$istEingeloggt = true;

// UND: beide müssen true sein
if ($istAdmin && $istEingeloggt) {
    echo "Zugang erlaubt";
}

// ODER: mindestens eine muss true sein
if ($istAdmin || $istEingeloggt) {
    echo "Teilzugang";
}

// NICHT
if (!$istEingeloggt) {
    echo "Bitte einloggen";
}
```

## Früh aussteigen mit exit()

Ein häufiges Muster in API-Dateien: Fehlerbedingung prüfen, Fehler zurückgeben, dann sofort aufhören.

```php
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Nicht eingeloggt']);
    exit(); // Code danach wird nicht mehr ausgeführt
}

// Ab hier: User ist eingeloggt
echo json_encode(['status' => 'ok']);
```

### Quellen
- [if/else](https://www.php.net/manual/de/control-structures.if.php)
- [Vergleichsoperatoren](https://www.php.net/manual/de/language.operators.comparison.php)
- [isset()](https://www.php.net/manual/de/function.isset.php)

---

← Zurück: [02 Arrays](./02-arrays.md) · → Weiter: [04 POST & GET](./04-post-get.md)
