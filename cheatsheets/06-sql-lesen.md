# Cheatsheet `SQL – Daten lesen (SELECT)`

## Alle Zeilen einer Tabelle laden

```php
<?php
$stmt = $pdo->prepare("SELECT * FROM tracks");
$stmt->execute();
$tracks = $stmt->fetchAll(PDO::FETCH_ASSOC); // gibt Array von assoziativen Arrays zurück

echo json_encode($tracks);
// → [{"id":"1","title":"Bohemian Rhapsody"}, ...]
```

## Nur bestimmte Spalten laden

```php
<?php
// ✅ Nur benötigte Spalten laden – besser als SELECT *
$stmt = $pdo->prepare("SELECT id, title FROM tracks");
$stmt->execute();
$tracks = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

## Mit WHERE-Bedingung filtern

```php
<?php
$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT id, email, name FROM users WHERE id = :id");
$stmt->execute([':id' => $userId]);

// fetchAll() → Array (auch wenn nur eine Zeile)
// fetch()    → eine einzelne Zeile als assoziatives Array
$user = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($user);
// → {"id":"42","email":"anna@test.ch","name":"Anna"}
```

## Mit JOIN zwei Tabellen verbinden

```php
<?php
// Alle Tracks eines Geräts laden (über Junction-Tabelle device_tracks)
$stmt = $pdo->prepare("
    SELECT t.id, t.title,
           IF(dt.track_id IS NOT NULL, 1, 0) AS selected
    FROM tracks t
    LEFT JOIN device_tracks dt
           ON dt.track_id = t.id AND dt.device_id = :device_id
    ORDER BY t.title
");
$stmt->execute([':device_id' => $deviceId]);
$tracks = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

## Anzahl Zeilen prüfen

```php
<?php
// Prüfen ob eine Email schon existiert
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
$stmt->execute([':email' => $email]);
$count = (int) $stmt->fetchColumn(); // gibt eine einzelne Zahl zurück

if ($count > 0) {
    echo json_encode(['error' => 'Email bereits vergeben']);
    exit();
}
```

## Sortieren & Limitieren

```sql
-- Neueste Einträge zuerst
SELECT * FROM sensordata ORDER BY starttime DESC

-- Nur die 10 neuesten
SELECT * FROM sensordata ORDER BY starttime DESC LIMIT 10
```

### Quellen
- [SELECT Statement](https://dev.mysql.com/doc/refman/8.0/en/select.html)
- [PDO::fetch](https://www.php.net/manual/de/pdostatement.fetch.php)
- [PDO::fetchAll](https://www.php.net/manual/de/pdostatement.fetchall.php)
- [JOIN erklärt](https://dev.mysql.com/doc/refman/8.0/en/join.html)
