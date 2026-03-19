# Cheatsheet `Variablen & Datentypen`

In PHP beginnen alle Variablen mit einem `$`-Zeichen.

## Variablen deklarieren

```php
<?php
$name = "Anna";          // String (Text)
$alter = 25;             // Integer (Ganzzahl)
$groesse = 1.72;         // Float (Kommazahl)
$istAngemeldet = true;   // Boolean (true / false)
$nichts = null;          // Null (kein Wert)
```

## Datentypen

| Typ       | Beispiel              | Beschreibung             |
| --------- | --------------------- | ------------------------ |
| `string`  | `"Hallo"`             | Text                     |
| `int`     | `42`                  | Ganzzahl                 |
| `float`   | `3.14`                | Kommazahl                |
| `bool`    | `true` / `false`      | Wahrheitswert            |
| `null`    | `null`                | Kein Wert / nicht gesetzt|
| `array`   | `[1, 2, 3]`           | Liste von Werten         |

## Strings zusammenfügen

```php
<?php
$vorname = "Anna";
$nachname = "Müller";

// Mit Punkt zusammenfügen
$vollname = $vorname . " " . $nachname;

// In doppelten Anführungszeichen direkt einbetten
$begruessung = "Hallo $vorname!";

echo $vollname;     // gibt "Anna Müller" aus
echo $begruessung;  // gibt "Hallo Anna!" aus
```

## Typ prüfen & umwandeln

```php
<?php
$wert = "42";

// Typ prüfen
echo gettype($wert);   // gibt "string" aus

// Typ umwandeln (casten)
$zahl = (int) $wert;   // $zahl ist jetzt die Zahl 42
$text = (string) 100;  // $text ist jetzt der String "100"
```

### Quellen
- [PHP Variablen](https://www.php.net/manual/de/language.variables.basics.php)
- [PHP Datentypen](https://www.php.net/manual/de/language.types.php)
