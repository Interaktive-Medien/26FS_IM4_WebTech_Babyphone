# 🧹 Homely Beispiel-Projekt

![Static Badge](https://img.shields.io/badge/Sprache-PHP-%23f7df1e)
![Static Badge](https://img.shields.io/badge/Kurs-MMP_IM4-blue)
![Last Changed](https://img.shields.io/endpoint?url=https://badges.crazy-internet.ch/im4_homely.php)

Dieses Repository beinhaltet das Beispiel-Projekt namens Homely für die Interaktiven Medien IV.

# 🏁 Live - Version

Du kannst Homely unter folgendem Link testen:

[https://homely.crazy-internet.ch](https://homely.crazy-internet.ch)

> 💌 füge `nick@homely.ch` oder `blub@blub.ch` zu Testzwecken deiner Freundesliste hinzu.

## ⚙️ Installation auf eigenem Web-Server

Um Homely auf dem eigenen Web-Server zu installieren, führe folgende Schritte aus:

### 1. Download

- [Klone das Repository](https://docs.github.com/en/repositories/creating-and-managing-repositories/cloning-a-repository) über GitHub oder [downloade das Repository als ZIP Datei](https://docs.github.com/en/repositories/working-with-files/using-files/downloading-source-code-archives) auf deinen eigenen Computer.

### 2. Datenbank

- Erstelle eine neue Datenbank bei deinem Hoster (z.B. [Infomaniak](https://www.infomaniak.com/de/support/faq/1981/mysqlmariadb-benutzer-und-datenbanken-verwalten)).

- Importiere alle SQL Dateien im Ordner `system/` in die neue Datenbank, um die benötigten Tabellen zu erstellen.

### 3. Code

- Benenne die Datei `system/config.php.blank` in `system/config.php` um.

- Passe die Datenbankverbindungsdaten in der Datei `system/config.php` an.

### 4. FTP Connect

- Erstelle eine neue FTP Verbindung mit dem SFTP Plugin gemäss [Anleitung im MMP 101](https://github.com/Interaktive-Medien/101-MMP/blob/main/resources/sftp.md).

# 🐛 Troubleshooting

- Wenn du PHP-Dateien verschiebst oder umbenennst, musst du deinen Cache löschen, damit das Login weiterhin funktioniert. Im Zweifelsfall in privatem Tab testen.
