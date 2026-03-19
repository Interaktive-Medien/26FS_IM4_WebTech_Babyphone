# Nestfunk - Babyphone

![Static Badge](https://img.shields.io/badge/Kurs-MMP_IM4-blue)

**Live:** [nestfunk.hausmaenner.ch](https://nestfunk.hausmaenner.ch)

Dieses Repository ist ein Beispielprojekt für Interaktive Medien IV. **Nestfunk** ist ein Babyphone-System bestehend aus einer Web-App und einem physischen Gerät (ESP32), die miteinander kommunizieren.

---

## Web App

![Static Badge](https://img.shields.io/badge/Sprache-PHP-%23f7df1e)

Die Web-App ermöglicht es Eltern, ihr Babyphone zu verwalten, eine Playlist zu konfigurieren und die Statistik einzusehen, wann ihr Baby geweint hat.

Als **Lernprojekt** werden folgende Konzepte vermittelt:

- Wie **Authentication** (Login / Registrierung / Session) funktioniert
- Warum man **Frontend und Backend trennt** (API-basierter Ansatz)
- Wie eine **REST-ähnliche API** mit PHP aufgebaut wird

→ **[Dokumentation Web App](readme_webapp.md)**

---

## Physical Computing

![Static Badge](https://img.shields.io/badge/Sprache-C%2B%2B-blue)

Das physische Gerät basiert auf einem ESP32-Mikrocontroller. Es erkennt, wann das Baby weint, spielt Musik ab und kommuniziert mit der Web-App über die REST-API.

→ **[Dokumentation Physical Computing](readme_physical_computing.md)**
