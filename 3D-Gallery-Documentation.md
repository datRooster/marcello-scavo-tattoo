# 🎨 Galleria 3D Hero - Documentazione

## Panoramica
Il sistema **3D Gallery Hero** crea un'esperienza immersiva per le categorie portfolio "Illustrazioni", "Disegni", "Quadri" e "Arte", simulando una vera galleria d'arte virtuale con rendering 3D e effetti parallax.

## 🎯 Caratteristiche Principali

### ✨ Rendering 3D
- **Three.js integration** per rendering WebGL
- **Stanza 3D virtuale** con pareti, pavimento e illuminazione
- **Quadri posizionati dinamicamente** alle pareti
- **Controlli orbita** per navigazione 360°
- **Illuminazione realistica** con spotlight per ogni opera

### 🎪 Effetti Visivi
- **Parallax interattivo** basato su movimento mouse
- **4 stili di stanza**: Moderno, Classico, Industriale, Artistico
- **Cornici dorate** con effetto vetro
- **Animazioni fluide** e transizioni eleganti
- **Effetti di profondità** e atmosphere lighting

### 🎮 Interattività
- **Click su quadri** per visualizzare dettagli
- **Tour guidato automatico** delle opere
- **Controlli audio** per suoni ambientali
- **Zoom e rotazione** della camera
- **Responsive design** per mobile

## 📁 Struttura File

```
wp-content/themes/marcello-scavo-tattoo/
├── inc/
│   └── 3d-gallery-widget.php          # Widget personalizzato
├── assets/
│   ├── css/
│   │   └── 3d-gallery.css             # Stili CSS per 3D e parallax
│   └── js/
│       └── 3d-gallery.js              # Logica Three.js e interazioni
└── taxonomy-portfolio_category.php     # Template modificato
```

## 🛠 Installazione e Configurazione

### 1. Attivazione Automatica
Il sistema si attiva automaticamente per le categorie:
- `illustrazioni`
- `disegni` 
- `quadri`
- `arte`
- `paintings`
- `drawings`

### 2. Configurazione Widget

1. **Vai a**: `Aspetto > Widget` o `Aspetto > Personalizza > Widget`
2. **Trova**: `🎨 3D Gallery Hero (Categoria Portfolio)`
3. **Aggiungi**: Il widget `🎨 3D Gallery Hero`
4. **Configura**: Le opzioni disponibili

### 3. Opzioni Widget

#### 📝 Contenuto
- **Titolo**: Personalizzato o automatico dalla categoria
- **Sottotitolo**: Personalizzato o automatico dalla descrizione
- **Descrizione**: Testo aggiuntivo per l'esperienza

#### 🎨 Stile Stanza
- **Moderno**: Pareti bianche minimaliste
- **Classico**: Legno tradizionale con texture
- **Industriale**: Mattoni e metallo scuro
- **Artistico**: Colori vibranti e creativi

#### ⚙️ Funzionalità
- **✅ Abilita rendering 3D**: Three.js WebGL (default: ON)
- **✅ Abilita effetti parallax**: Profondità CSS (default: ON)
- **📱 Fallback automatico**: Se 3D non supportato

## 🎮 Controlli Utente

### 🖱 Desktop
- **Trascina**: Ruota camera intorno alla stanza
- **Scroll**: Zoom in/out
- **Click**: Visualizza dettagli opera
- **Tasti**: Navigazione opzionale

### 📱 Mobile
- **Touch & Drag**: Rotazione camera
- **Pinch**: Zoom
- **Tap**: Interazione opere
- **Interfaccia semplificata**: Controlli ottimizzati

## 💡 Funzionalità Avanzate

### 🎪 Tour Guidato
```javascript
// Avvio automatico del tour
document.getElementById('start-gallery-tour').click();
```

### 🔊 Audio Ambientale
- **Suoni di sottofondo**: Ambiente galleria
- **Controlli on/off**: Toggle utente
- **Volume automatico**: Responsive al contesto

### 📊 Analytics Integration
Il sistema traccia automaticamente:
- **Tempo visualizzazione**: Durata sessione 3D
- **Interazioni**: Click su opere
- **Tour completati**: Analytics tour guidato

## 🔧 Personalizzazione Avanzata

### 🎨 Stili CSS Personalizzati
```css
/* Personalizza colori stanza */
[data-room-style="custom"] .room-back-wall {
    background: linear-gradient(180deg, #your-color 0%, #your-color2 100%);
}

/* Personalizza cornici */
.frame-border {
    background: linear-gradient(45deg, #custom-gold, #custom-bronze);
}
```

### ⚡ Performance Optimization
```javascript
// Configura qualità rendering
const gallerySettings = {
    pixelRatio: Math.min(window.devicePixelRatio, 2),
    antialias: true,
    shadowMapSize: 1024 // Riduci per performance migliori
};
```

## 🐛 Risoluzione Problemi

### ❌ 3D Non Si Carica
1. **Verifica**: Browser supporta WebGL
2. **Controlla**: Console JavaScript per errori
3. **Fallback**: Dovrebbe attivare modalità parallax
4. **CDN**: Verifica connessione Three.js

### 🐌 Performance Lente
1. **Riduci**: Numero opere visualizzate (max 8)
2. **Ottimizza**: Dimensioni immagini (max 800x600)
3. **Disabilita**: Anti-aliasing su dispositivi lenti
4. **Cache**: Verifica cache delle texture

### 📱 Mobile Issues
1. **Touch**: Verifica eventi touch registrati
2. **Memoria**: Riduci qualità texture
3. **Fallback**: Attiva solo parallax su mobile
4. **Performance**: Limita frame rate

## 📈 Metriche e Performance

### ⚡ Benchmark Target
- **Caricamento**: < 3 secondi
- **FPS**: 60fps desktop, 30fps mobile
- **Memoria**: < 100MB texture cache
- **Bundle Size**: ~200KB (escluso Three.js)

### 📊 Analytics Disponibili
- **Engagement Rate**: Tempo medio visualizzazione
- **Interaction Rate**: % utenti che cliccano opere
- **Tour Completion**: % tour completati
- **Device Performance**: FPS medio per device

## 🔗 Dipendenze

### 📚 Librerie Esterne
- **Three.js**: r128+ (caricato da CDN)
- **OrbitControls**: Incluso con Three.js
- **FontAwesome**: 6.4.0+ (già presente nel tema)

### 🏗 Requisiti Browser
- **WebGL**: Supporto richiesto per 3D
- **ES6**: Supporto JavaScript moderno
- **CSS3**: Transform e animation support
- **Touch Events**: Per mobile interaction

## 🎨 Esempi di Utilizzo

### Galleria Illustrazioni
Perfetto per:
- Portfolio artistico digitale
- Concept art e design
- Illustrazioni commerciali
- Arte digitale e tradizionale

### Galleria Disegni
Ideale per:
- Sketches e bozzetti
- Disegni tecnici
- Arte a matita/carboncino
- Studi preparatori

## 📞 Supporto

Per assistenza tecnica o personalizzazioni:
1. **Documentazione**: Consulta questa guida
2. **Console Browser**: Verifica errori JavaScript
3. **Widget Settings**: Controlla configurazione
4. **Community**: Forum WordPress tema

---

*Versione: 1.0.0 | Compatibilità: WordPress 5.0+ | Browser: Modern browsers con WebGL*