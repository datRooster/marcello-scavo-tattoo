# Sistema Dinamico delle Stanze - Gallery 2D

## 📋 Overview
Il sistema delle stanze è ora completamente dinamico e si basa sui file presenti nella cartella `assets/images/rooms/`.

## 🎯 Come Funziona

### 1. **Scansione Automatica** (PHP)
Il file `functions.php` legge automaticamente tutti i file immagine dalla cartella:
```php
/wp-content/themes/marcello-scavo-tattoo/assets/images/rooms/
```

### 2. **Formati Supportati**
- `.jpg` / `.jpeg`
- `.png`
- `.webp`
- `.gif`

### 3. **Generazione Automatica Stanze**
Per ogni immagine trovata:
- **ID stanza**: nome file sanitizzato (es: `galleria-2` da `Galleria_2.webp`)
- **Nome stanza**: nome leggibile (es: "Galleria 2" da `Galleria_2.webp`)
- **URL immagine**: path completo all'immagine

### 4. **Cornici di Default**
Ogni stanza usa 3 cornici equidistanti con coordinate predefinite:
- **Cornice sinistra**: `x: 12.5%, y: 24%, width: 11.5%, height: 32%`
- **Cornice centrale**: `x: 43.5%, y: 28%, width: 13.5%, height: 22%`
- **Cornice destra**: `x: 77%, y: 24%, width: 13%, height: 32%`

## 🚀 Come Aggiungere Nuove Stanze

### Metodo Semplice:
1. Crea un'immagine di una stanza con cornici visibili
2. Salva il file in formato WebP (consigliato) o JPG/PNG
3. Carica il file in: `assets/images/rooms/`
4. Ricarica la pagina - la stanza apparirà automaticamente!

### Esempio:
```
assets/images/rooms/
├── Galleria.webp          → Stanza "Galleria"
├── Galleria_2.webp        → Stanza "Galleria 2"
├── Studio_Artistico.webp  → Stanza "Studio Artistico"
└── Sala_Moderna.webp      → Stanza "Sala Moderna"
```

## 📝 Convenzioni di Naming

### Nomi File Consigliati:
- Usa underscore `_` o trattini `-` per separare le parole
- Il sistema li convertirà automaticamente in spazi
- Usa PascalCase o kebab-case

### Esempi:
| Nome File | Nome Visualizzato |
|-----------|-------------------|
| `Galleria.webp` | "Galleria" |
| `Galleria_Moderna.webp` | "Galleria Moderna" |
| `studio-artistico.webp` | "Studio Artistico" |
| `Sala_VIP_2024.webp` | "Sala VIP 2024" |

## 🎨 Badge "Popolare"
- La **prima stanza** in ordine alfabetico riceve automaticamente il badge "Popolare"
- Puoi ordinare le stanze nominando i file strategicamente (es: `01_Galleria.webp`)

## 🔧 Personalizzazione Avanzata

### Coordinate Cornici Personalizzate (Futuro)
Per personalizzare le posizioni delle cornici per stanze specifiche, puoi creare un file JSON:
```
assets/images/rooms/config.json
```

Esempio struttura:
```json
{
  "galleria-moderna": {
    "frames": [
      { "x": 10, "y": 20, "width": 15, "height": 35 },
      { "x": 40, "y": 25, "width": 20, "height": 30 },
      { "x": 75, "y": 20, "width": 15, "height": 35 }
    ]
  }
}
```

## ⚡ Performance

### Ottimizzazioni:
- Usa formato **WebP** per immagini più leggere (60-80% più piccole)
- Risoluzione consigliata: **1920x1080px** o **1600x900px**
- Qualità: 80-85% per WebP
- Usa `loading="lazy"` (già implementato) per lazy loading

### Compressione Consigliata:
```bash
# Converti JPG in WebP con qualità 85%
cwebp -q 85 input.jpg -o output.webp

# Batch conversion
for file in *.jpg; do cwebp -q 85 "$file" -o "${file%.jpg}.webp"; done
```

## 🐛 Troubleshooting

### Problema: Stanza non appare
**Soluzione:**
1. Verifica che il file sia in `assets/images/rooms/`
2. Controlla che l'estensione sia supportata (jpg, png, webp, gif)
3. Ricarica la pagina (Cmd+R o Ctrl+R)
4. Controlla la console browser per errori

### Problema: Nome stanza non leggibile
**Soluzione:**
Rinomina il file usando underscore o trattini:
- ❌ `galleriamoderna.webp` → "Galleriamoderna"
- ✅ `galleria_moderna.webp` → "Galleria Moderna"

### Problema: Immagine non si carica
**Soluzione:**
1. Verifica i permessi del file (644 o 755)
2. Controlla che il path sia accessibile via browser
3. Controlla la console per errori 404

## 📊 Dati Passati a JavaScript

### Variabile Globale: `roomsData`
```javascript
{
  available_rooms: [
    {
      id: "galleria",
      name: "Galleria",
      filename: "Galleria.webp",
      url: "http://localhost/wp-content/themes/.../Galleria.webp"
    },
    ...
  ],
  theme_path: "http://localhost/wp-content/themes/marcello-scavo-tattoo"
}
```

## 🔐 Sicurezza

### Validazione File:
- Solo estensioni immagine consentite
- Nomi file sanitizzati con `sanitize_title()`
- Path relativi, non assoluti
- Nessun caricamento dinamico da input utente

## 📈 Scalabilità

### Limiti:
- **Nessun limite** al numero di stanze
- Layout responsive: griglia auto-adattiva
- Lazy loading automatico delle immagini

### Performance con Molte Stanze:
- 10 stanze: ⚡ Eccellente
- 20 stanze: ✅ Ottima
- 50+ stanze: ⚠️ Considera paginazione

## 🎯 Roadmap Future

### Funzionalità Pianificate:
- [ ] File JSON per configurazioni cornici personalizzate
- [ ] Upload stanze da admin WordPress
- [ ] Rilevamento automatico cornici con AI
- [ ] Drag & drop per riordinare stanze
- [ ] Categorie di stanze (Modern, Classic, Minimal, ecc.)
- [ ] Ricerca e filtri per stanze

## 📝 Note Tecniche

### File Modificati:
1. **functions.php**: Scansione cartella e `wp_localize_script`
2. **3d-gallery.js**: 
   - `getAvailableRooms()` - Recupera stanze da PHP
   - `generateRoomOptions()` - Genera HTML dinamico
   - `loadRoomMockup()` - Carica stanza selezionata
   - `generateDefaultFrames()` - Coordinate default

### Dipendenze:
- PHP 7.4+
- WordPress 5.0+
- Modern browsers (ES6+)

## 🎨 Esempio Completo

### Step-by-step:
1. Crea immagine stanza: `Sala_Moderna.webp` (1920x1080px)
2. Carica in: `wp-content/themes/marcello-scavo-tattoo/assets/images/rooms/`
3. Ricarica pagina gallery
4. ✅ Nuova card "Sala Moderna" appare automaticamente
5. Click "Usa questa stanza"
6. ✅ Background stanza caricato
7. ✅ 3 cornici pre-popolate con artwork

---

**Data Implementazione**: 4 Novembre 2025  
**Versione**: 2.0 - Sistema Dinamico  
**Autore**: AI Assistant + datRooster
