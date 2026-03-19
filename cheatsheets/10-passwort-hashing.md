# Cheatsheet `Passwort-Hashing`

Passwörter dürfen **niemals im Klartext** in der Datenbank gespeichert werden. PHP bietet dafür die Funktionen `password_hash()` und `password_verify()`.

## Warum Hashing?

```
Klartext-Passwort:  "geheim123"
Hash:               "$2y$10$Xk3c8r2jF9..."  (60+ Zeichen, nicht umkehrbar)
```

- Wenn die Datenbank gehackt wird, können die Passwörter nicht gelesen werden
- Hashing ist eine **Einwegverschlüsselung** – aus dem Hash kann man nicht zurück zum Passwort

## password_hash() – beim Registrieren

```php
<?php
$passwort = $_POST['password']; // Klartext-Passwort aus dem Formular

// ✅ Passwort hashen bevor es in die DB gespeichert wird
$hash = password_hash($passwort, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (:email, :pass)");
$stmt->execute([':email' => $email, ':pass' => $hash]);
```

## password_verify() – beim Login

```php
<?php
$passwortEingabe = $_POST['password']; // Klartext aus dem Formular

// User aus der DB laden (mit dem gespeicherten Hash)
$stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = :email");
$stmt->execute([':email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($passwortEingabe, $user['password'])) {
    // ✅ Passwort stimmt
    session_start();
    $_SESSION['user_id'] = $user['id'];
    echo json_encode(['status' => 'success']);
} else {
    // ❌ User nicht gefunden oder Passwort falsch
    echo json_encode(['error' => 'Ungültige Zugangsdaten']);
}
```

> ⚠️ Gib bei einer falschen Anmeldung **nie** preis, ob die Email oder das Passwort falsch war. Die generische Meldung "Ungültige Zugangsdaten" schützt vor Enumeration-Angriffen.

## Was macht PASSWORD_DEFAULT?

`PASSWORD_DEFAULT` verwendet den aktuell sichersten Algorithmus (heute: bcrypt). Der Hash enthält auch automatisch einen zufälligen **Salt**, sodass gleiche Passwörter unterschiedliche Hashes erzeugen.

```php
<?php
echo password_hash("gleich", PASSWORD_DEFAULT);
// → $2y$10$abc...xyz  (anderer Hash)
echo password_hash("gleich", PASSWORD_DEFAULT);
// → $2y$10$def...uvw  (wieder anderer Hash!)

// password_verify() weiss damit umzugehen ✅
```

### Quellen
- [password_hash()](https://www.php.net/manual/de/function.password-hash.php)
- [password_verify()](https://www.php.net/manual/de/function.password-verify.php)
- [Passwortsicherheit](https://cheatsheetseries.owasp.org/cheatsheets/Password_Storage_Cheat_Sheet.html)
