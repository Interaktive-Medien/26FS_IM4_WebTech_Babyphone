# Cheatsheet `Arrays`

Arrays speichern mehrere Werte in einer einzigen Variable.

## Einfaches Array (Liste)

```php
<?php
$farben = ["rot", "grün", "blau"];

echo $farben[0]; // gibt "rot" aus (Index beginnt bei 0)
echo $farben[2]; // gibt "blau" aus
```

## Assoziatives Array (Key → Value)

Das assoziative Array ist in PHP besonders wichtig, da Datenbankzeilen immer als assoziatives Array zurückgegeben werden.

```php
<?php
$user = [
    "name"  => "Anna",
    "email" => "anna@example.com",
    "alter" => 25
];

echo $user["name"];  // gibt "Anna" aus
echo $user["email"]; // gibt "anna@example.com" aus
```

## Array-Einträge hinzufügen & entfernen

```php
<?php
$liste = ["Apfel", "Banane"];

// Eintrag hinzufügen
$liste[] = "Kirsche";           // am Ende anhängen
array_push($liste, "Mango");    // ebenfalls am Ende

// Eintrag entfernen
array_pop($liste);              // letzten Eintrag entfernen
```

## Über ein Array loopen

```php
<?php
$tracks = ["Bohemian Rhapsody", "Creep", "Wonderwall"];

foreach ($tracks as $track) {
    echo $track; // gibt jeden Track aus
}

// Mit Index
foreach ($tracks as $index => $track) {
    echo "$index: $track"; // gibt "0: Bohemian Rhapsody" etc. aus
}
```

## Array in JSON umwandeln

Das ist das Muster, das in fast jeder API-Datei verwendet wird:

```php
<?php
$daten = [
    ["id" => 1, "title" => "Creep"],
    ["id" => 2, "title" => "Wonderwall"],
];

header('Content-Type: application/json');
echo json_encode($daten);
// gibt [{"id":1,"title":"Creep"},{"id":2,"title":"Wonderwall"}] aus
```

### Quellen
- [PHP Arrays](https://www.php.net/manual/de/language.types.array.php)
- [foreach](https://www.php.net/manual/de/control-structures.foreach.php)
- [json_encode](https://www.php.net/manual/de/function.json-encode.php)

---

← Zurück: [01 Variablen & Datentypen](./01-variablen.md) · → Weiter: [03 Bedingungen](./03-bedingungen.md)
