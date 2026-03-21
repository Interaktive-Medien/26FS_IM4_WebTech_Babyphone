# Cheatsheet `Was ist PHP`

- PHP ist eine serverseitige Programmiersprache – der Code läuft auf dem **Server**, nicht im Browser
- PHP-Dateien enden mit `.php`
- PHP-Code wird zwischen `<?php` und `?>` geschrieben
- Der Server führt den PHP-Code aus und schickt das Ergebnis (z.B. JSON oder HTML) an den Browser

```
Browser  ──── HTTP Request ────►  Server (PHP läuft hier)
         ◄─── JSON / HTML ──────  Server gibt Ergebnis zurück
```

## PHP in einer Datei einbinden

```php
<?php
// Alles zwischen <?php und ?> wird vom Server ausgeführt
echo "Hallo Welt";
?>
```

## Unterschied zu JavaScript

| Eigenschaft        | JavaScript          | PHP                     |
| ------------------ | ------------------- | ----------------------- |
| Wo läuft es?       | Im Browser (Client) | Auf dem Server          |
| Zugriff auf DB?    | Nein (direkt)       | Ja                      |
| Sichtbar für User? | Ja (Quellcode)      | Nein (nur das Ergebnis) |
| Dateiendung        | `.js`               | `.php`                  |

### Quellen

- [PHP Grundlagen](https://developer.mozilla.org/en-US/docs/Glossary/PHP)

---

→ Weiter: [01 Variablen & Datentypen](./01-variablen.md)
