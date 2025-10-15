# Portfolio Template - Guida Utilizzo

## Panoramica
Il template Portfolio è stato completamente rinnovato per offrire un'esperienza visiva ricca e professionale, fedele al design desiderato. Include sezioni moderne, slider interattivo e layout responsive.

## Nuove Funzionalità

### 1. Hero Section Migliorata
- Background sfumato personalizzabile
- Animazioni di entrata
- Indicatore di scroll
- Call-to-action prominente

### 2. Slider Gallery
È stato aggiunto un nuovo widget slider come alternativa alla galleria tradizionale:
- **Widget Name**: "Portfolio Slider"
- **Area Widget**: "Portfolio - Slider Galleria"

#### Configurazione Slider:
- Titolo personalizzabile
- Selezione categoria portfolio
- Numero di slide configurabile
- Autoplay con velocità personalizzabile
- Controlli touch/swipe per mobile

### 3. Sezioni Migliorate

#### Galleria d'Arte
- Layout griglia responsive
- Effetti hover eleganti
- Fallback automatico se non ci sono widget

#### Tatuaggi Recenti
- Grid layout moderno
- Badge categoria
- Overlay informativi
- Meta informazioni (data, categorie)

#### Testimonianze
- Design a carte
- Rating con stelle
- Avatar clienti
- Quote styling professionale

#### Call-to-Action
- Background sfumato
- Multiple CTA buttons
- Features icons
- Design coinvolgente

## Widget Areas Disponibili

1. **Portfolio - Galleria** (`portfolio-gallery`)
2. **Portfolio - Slider Galleria** (`portfolio-gallery-slider`) ⭐ NUOVO
3. **Portfolio - Tatuaggi Recenti** (`portfolio-latest`)
4. **Portfolio - Testimonianze** (`portfolio-testimonials`)
5. **Portfolio - Prenotazione** (`portfolio-cta`)

## Personalizzazione Customizer

Le seguenti opzioni sono personalizzabili dal Customizer WordPress:

- `portfolio_hero_title`: Titolo della hero section
- `portfolio_hero_desc`: Descrizione della hero section
- `portfolio_gallery_desc`: Descrizione della sezione galleria
- `portfolio_latest_desc`: Descrizione dei tatuaggi recenti
- `portfolio_cta_title`: Titolo della CTA section
- `portfolio_cta_desc`: Descrizione della CTA section

## Come Usare lo Slider

### Opzione 1: Widget Slider (Raccomandato)
1. Vai in **Aspetto > Widget**
2. Trova l'area "Portfolio - Slider Galleria"
3. Aggiungi il widget "Portfolio Slider"
4. Configura le opzioni:
   - Titolo
   - Categoria (opzionale)
   - Numero di slide
   - Autoplay on/off
   - Velocità autoplay

### Opzione 2: Widget Gallery Tradizionale
1. Usa l'area "Portfolio - Galleria"
2. Aggiungi widget di gallery standard

## Responsive Design

Il template è completamente responsive con breakpoints:
- Desktop: > 768px
- Tablet: 768px - 480px
- Mobile: < 480px

## Accessibilità

- Focus states per tutti gli elementi interattivi
- ARIA labels per controlli slider
- Contrast ratio ottimizzato
- Navigazione da tastiera supportata

## Performance

- Lazy loading per immagini
- CSS ottimizzato
- JavaScript modulare
- Transizioni hardware-accelerated

## Browser Support

- Chrome 60+
- Firefox 60+
- Safari 12+
- Edge 79+

## Fallback Content

Se non vengono configurati widget, il template mostra automaticamente:
- Portfolio items dal custom post type 'portfolio'
- Placeholder eleganti
- Testimonianze di esempio

## File Modificati

- `page-portfolio.php` - Template principale
- `functions.php` - Widget slider e area registrations
- `style.css` - Stili avanzati
- `assets/js/portfolio-slider.js` - Funzionalità slider

## Troubleshooting

### Lo slider non funziona
- Verifica che il JavaScript sia caricato
- Controlla la console per errori
- Assicurati che ci siano portfolio items con immagini

### Layout rotto su mobile
- Verifica che il viewport meta tag sia presente
- Controlla che il CSS responsive sia caricato
- Testa su diversi dispositivi

### Performance lenta
- Ottimizza le immagini del portfolio
- Usa formati moderni (WebP)
- Abilita caching

## Aggiornamenti Futuri

Il template è pronto per:
- Integrazione con page builders
- Personalizzazioni colore avanzate
- Effetti parallax
- Integrazione social media avanzata

---

*Ultimo aggiornamento: Ottobre 2025*
*Versione: 2.0.0*