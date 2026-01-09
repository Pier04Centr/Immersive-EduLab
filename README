# Immersive EduLab 🎓🚀

![Project Status](https://img.shields.io/badge/status-active-success)
![License](https://img.shields.io/badge/license-AGPLv3-blue)
![Docker](https://img.shields.io/badge/docker-supported-2496ED?logo=docker&logoColor=white)
![Tech](https://img.shields.io/badge/stack-LAMP-orange)

> **Piattaforma Web per la Didattica Immersiva e la Realtà Aumentata (AR)**

**Immersive EduLab** è una Web Application progettata per supportare la didattica nelle facoltà STEM, superando i limiti dei materiali di studio tradizionali (slide, libri). La piattaforma funge da repository centralizzato per modelli 3D interattivi, permettendo la manipolazione spaziale, temporale (animazioni) e la visualizzazione in Realtà Aumentata direttamente dal browser.

---

## 📑 Indice
- [Funzionalità Principali](#-funzionalità-principali)
- [Architettura e Tecnologie](#-architettura-e-tecnologie)
- [Anteprima](#-anteprima)
- [Installazione e Avvio](#-installazione-e-avvio)
- [Struttura del Progetto](#-struttura-del-progetto)
- [Autori](#-autori)
- [Licenza](#-licenza)

---

## ✨ Funzionalità Principali

### 🔭 Visualizzazione e Interazione 3D
- **Rendering Nativo:** Visualizzazione fluida di modelli `.glb` (glTF) tramite `<model-viewer>`.
- **Manipolazione Ibrida:**
  - *Spaziale:* Orbit controls per rotazione e zoom a 360°.
  - *Temporale:* Slider interattivo per navigare la timeline delle animazioni (es. cicli meccanici o biologici).
- **WebXR & AR:** Realtà Aumentata markerless accessibile da mobile (Android/iOS) senza app esterne.

### 👥 Social Learning & User Experience
- **Chat Contestuale:** Sistema di messaggistica asincrona (AJAX) specifico per ogni progetto/modello.
- **Onboarding Immersivo:** Sequenza di avvio cinematica "System Boot" in stile Sci-Fi/Cyberpunk.
- **Profilo Utente 3D:** Dashboard personale con avatar interattivo basato su **Three.js** (mouse tracking e luci dinamiche).
- **Temi:** Supporto nativo Dark Mode / Light Mode con persistenza.

### 🛡️ Sicurezza e Backend
- **Autenticazione Sicura:** Hashing delle password con SHA-512 e protezione SQL Injection.
- **Gestione Ruoli:**
  - *Ospite:* Login/Registrazione.
  - *Utente:* Upload modelli, commenti, gestione profilo.
  - *Admin:* Console di gestione, eliminazione utenti e progetti (Rollback logico/fisico).

---

## 🛠 Architettura e Tecnologie

Il progetto segue un'architettura **3-Tier** containerizzata su Docker.

| Ambito | Tecnologie Utilizzate |
| :--- | :--- |
| **Frontend** | HTML5, CSS3, JavaScript (Vanilla), **WebXR API** |
| **3D Engines** | **Google <model-viewer>** (Galleria), **Three.js** (Profilo) |
| **Backend** | PHP 8.1 (Pattern MVC semplificato) |
| **Database** | MySQL 8.0 (Doppio DB: `DB_Utenti`, `DB_Gallary`) |
| **Infrastructure** | Docker, Docker Compose, Apache |

---

## 🚀 Installazione e Avvio

### Prerequisiti
- Docker Desktop installato e attivo.
- Git.

### Setup Rapido
1. **Clona la repository:**
```bash
git clone https://github.com/Pier04Centr/Immersive-EduLab/
cd Immersive-EduLab
```
2. **Avvia i container:**
```bash
docker-compose up -d

```


Il comando scaricherà automaticamente le immagini di PHP, Apache e MySQL e configurerà i volumi.


3. **Accedi alla piattaforma:**
Apri il browser e visita:


`http://localhost:8080`.


4. **Credenziali Admin**
*`test@mail.com` / `P@ssword1`.*

---

## 👥 Autori

Centrone Pierpaolo
Dagostino Davide

Progetto sviluppato per il corso di **Ingegneria del Software**.

---

## 📄 Licenza

Distribuito sotto la licenza **GNU AGPLv3**. Vedi il file `LICENSE` per maggiori informazioni.
