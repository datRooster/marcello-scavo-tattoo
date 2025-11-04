# Sistema Free Drop & Resize - Gallery 2D

## 📋 Overview
Il nuovo sistema permette di posizionare liberamente le artwork sulla stanza senza vincoli di cornici predefinite, con possibilità di ridimensionamento tramite handle visivi.

## 🎯 Funzionalità Principali

### 1. **Free Drop Canvas**
- ✅ Tutta la stanza è una grande drop zone
- ✅ Nessuna cornice predefinita
- ✅ Posizionamento libero ovunque
- ✅ Drag & drop dalla sidebar destra

### 2. **Drag & Drop Artwork**
- Trascina un'artwork dalla sidebar
- Rilascia ovunque sulla stanza
- L'artwork appare centrata sul punto di drop
- Dimensioni iniziali: 15% larghezza canvas

### 3. **Movimento Artwork**
- Click e trascina l'artwork per spostarla
- Funziona in modalità "Selezione" o "Sposta"
- Posizione salvata in percentuali (responsive)

### 4. **Ridimensionamento Intelligente**
- Click sul bottone "Ridimensiona" nella toolbar
- Click sull'artwork da ridimensionare
- Compaiono 8 handle dorati:
  - **4 angoli**: ridimensionamento diagonale
  - **4 lati**: ridimensionamento orizzontale/verticale
- Trascina gli handle per modificare le dimensioni
- Limiti minimi: 5% del canvas

## 🛠️ Toolbar Design

### Bottoni Disponibili:
1. **Selezione** (default)
   - Permette di selezionare e spostare artwork
   - Cursor: default

2. **Sposta**
   - Modalità movimento dedicata
   - Cursor: grab

3. **Ridimensiona** ⭐ NUOVO
   - Attiva modalità resize
   - Click su artwork per vedere handle
   - 8 punti di ridimensionamento

4. **Salva Design**
   - Salva configurazione corrente
   - Export JSON con posizioni e dimensioni

## 💻 Implementazione Tecnica

### JavaScript - Metodi Principali

#### `initializeFreeDropSystem()`
Inizializza il canvas libero con event listeners per drag & drop.

```javascript
this.placedArtworks = [];      // Array artwork posizionate
this.selectedArtwork = null;   // Artwork selezionata
this.resizeMode = false;       // Stato modalità resize
```

#### `handleFreeCanvasDrop(e)`
Gestisce il drop di un'artwork sul canvas:
- Calcola posizione relativa in percentuali
- Crea elemento artwork con dimensioni iniziali
- Aggiunge event listeners per movimento e selezione

#### `placeArtworkOnCanvas(artworkData, xPercent, yPercent)`
Posiziona fisicamente l'artwork:
- Crea elemento `div.placed-artwork-free`
- Applica stili con posizione assoluta in %
- Dimensioni iniziali: 15% x 20%
- Aggiunge shadow e transizioni

#### `selectArtworkForResize(artworkElement)`
Seleziona artwork per ridimensionamento:
- Verifica che `resizeMode` sia attivo
- Aggiunge classe `.selected-for-resize`
- Chiama `addResizeHandles()`

#### `addResizeHandles(artworkElement)`
Crea 8 handle di resize:
```javascript
const handles = ['nw', 'n', 'ne', 'e', 'se', 's', 'sw', 'w'];
```
Ogni handle ha:
- Posizione specifica (angolo/lato)
- Cursor appropriato (nw-resize, e-resize, etc.)
- Event listener per `mousedown`

#### `startResize(e, artworkElement, position)`
Gestisce il ridimensionamento:
- Traccia posizione iniziale e dimensioni
- Calcola delta mouse movement
- Aggiorna width/height in tempo reale
- Mantiene limiti minimi (5%)
- Usa percentuali per responsive design

#### `startDragArtwork(e, artworkElement)`
Gestisce lo spostamento:
- Blocca il drag se in modalità resize sui handle
- Traccia offset mouse-elemento
- Aggiorna posizione in tempo reale
- Usa percentuali per responsive design

#### `toggleResizeMode(enabled)`
Attiva/disattiva modalità resize:
- `true`: cursor default, abilita selezione per resize
- `false`: cursor crosshair, rimuove handle, deseleziona

### CSS - Classi Principali

#### `.placed-artwork-free`
```css
position: absolute;
box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
transition: box-shadow 0.2s ease;
user-select: none;
```

#### `.selected-for-resize`
```css
box-shadow: 0 0 0 3px var(--gold-color), 0 8px 24px rgba(0, 0, 0, 0.5);
z-index: 100 !important;
```

#### `.resize-handle`
```css
position: absolute;
width: 12px;
height: 12px;
background: var(--gold-color);
border: 2px solid white;
border-radius: 50%;
opacity: 0; /* visibili solo su selected */
```

#### Handle Positions
Ogni handle ha posizione specifica:
- `.resize-handle-nw`: top-left corner
- `.resize-handle-n`: top center
- `.resize-handle-ne`: top-right corner
- `.resize-handle-e`: right center
- `.resize-handle-se`: bottom-right corner
- `.resize-handle-s`: bottom center
- `.resize-handle-sw`: bottom-left corner
- `.resize-handle-w`: left center

## 🎨 User Experience

### Flusso Utente Tipico:

1. **Selezione Stanza**
   - Click su card stanza
   - Canvas caricato con background

2. **Aggiunta Artwork**
   - Drag artwork dalla sidebar
   - Drop ovunque sulla stanza
   - Artwork appare centrata sul punto

3. **Posizionamento Fine**
   - Click e drag per spostare
   - Posizionamento pixel-perfect

4. **Ridimensionamento**
   - Click bottone "Ridimensiona"
   - Click sull'artwork
   - Compaiono 8 handle dorati
   - Drag handle per modificare dimensioni

5. **Finalizzazione**
   - Click bottone "Selezione" per uscire da resize mode
   - Continua ad aggiungere/modificare artwork
   - Click "Salva Design" quando pronto

## 📊 Dati Salvati

### Struttura Artwork Posizionata:
```javascript
{
  id: "artwork-1699123456789-abc123",
  element: HTMLElement,
  data: {
    id: "12",
    title: "Opera d'Arte",
    url: "http://localhost/.../image.jpg"
  }
}
```

### Struttura Export Design:
```javascript
{
  roomId: "galleria",
  artworks: [
    {
      artworkId: "artwork-123",
      originalId: "12",
      position: {
        left: "35.5%",
        top: "42.3%",
        width: "18.2%",
        height: "24.1%"
      }
    }
  ],
  timestamp: "2025-11-04T10:30:00.000Z"
}
```

## 🔧 Personalizzazione

### Modificare Dimensioni Iniziali:
```javascript
// In placeArtworkOnCanvas()
const initialWidth = 15;  // % larghezza canvas
const initialHeight = 20; // % altezza canvas
```

### Modificare Limiti Resize:
```javascript
// In startResize(), condizione minima
if (widthPercent > 5 && heightPercent > 5) {
  // Cambia 5 per modificare limite minimo
}
```

### Modificare Colore Handle:
```css
.resize-handle {
  background: var(--gold-color); /* Cambia qui */
  border: 2px solid white;
}
```

### Modificare Stile Selezione:
```css
.selected-for-resize {
  box-shadow: 0 0 0 3px var(--gold-color), /* Bordo */
              0 8px 24px rgba(0, 0, 0, 0.5); /* Shadow */
}
```

## ⚡ Performance

### Ottimizzazioni Implementate:
- ✅ Posizioni in percentuali (no ricalcoli)
- ✅ Event delegation dove possibile
- ✅ Transizioni CSS (hardware accelerated)
- ✅ `pointer-events: none` su immagini
- ✅ `user-select: none` per evitare selezioni accidentali
- ✅ Debounce implicito tramite mousemove

### Limiti Consigliati:
- Max artwork per canvas: **50-100**
- Dimensione minima artwork: **5% canvas**
- Resize handle size: **12px** (mobile-friendly)

## 🐛 Troubleshooting

### Problema: Handle non visibili
**Causa**: Artwork non selezionata o resize mode non attivo
**Soluzione**: 
1. Click bottone "Ridimensiona"
2. Click sull'artwork
3. Verifica `this.resizeMode === true`

### Problema: Artwork non si sposta
**Causa**: In modalità resize con handle attivi
**Soluzione**: Click bottone "Selezione" o "Sposta"

### Problema: Drop non funziona
**Causa**: Event listener non inizializzati
**Soluzione**: Verifica che `initializeFreeDropSystem()` sia chiamato

### Problema: Dimensioni non responsive
**Causa**: Stili hardcoded in px invece di %
**Soluzione**: Verifica che tutti gli stili usino percentuali

## 🎯 Roadmap Future

### Funzionalità Pianificate:
- [ ] Rotazione artwork con handle dedicato
- [ ] Snap to grid opzionale
- [ ] Multi-selezione (Shift+Click)
- [ ] Copy/Paste artwork (Ctrl+C/V)
- [ ] Undo/Redo stack
- [ ] Layers panel (z-index management)
- [ ] Alignment tools (align left, center, right)
- [ ] Distribution tools (distribute evenly)
- [ ] Aspect ratio lock durante resize
- [ ] Smart guides (allineamento automatico)

### Miglioramenti UX:
- [ ] Minimap per navigazione canvas grande
- [ ] Zoom canvas (pinch-to-zoom)
- [ ] Context menu (right-click)
- [ ] Keyboard shortcuts
- [ ] Touch gestures per mobile
- [ ] Preview prima del drop
- [ ] Animazioni smooth per posizionamento

## 📝 Note Tecniche

### Browser Support:
- ✅ Chrome/Edge 90+
- ✅ Firefox 88+
- ✅ Safari 14+
- ⚠️ Mobile: iOS 14+, Android 10+

### Dependencies:
- JavaScript ES6+
- CSS3 (transforms, transitions)
- Drag & Drop API
- Mouse Events API

### Compatibilità:
Sistema completamente standalone, nessuna dipendenza esterna richiesta oltre a WordPress e Theme base.

---

**Data Implementazione**: 4 Novembre 2025  
**Versione**: 3.0 - Free Drop & Resize System  
**Autore**: AI Assistant + datRooster
