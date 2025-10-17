# Marcello Scavo Tattoo Theme

Tema WordPress personalizzato per l'artista del tatuaggio Marcello Scavo.

## Caratteristiche

### Design
- **Stile**: Artistico-minimalista con elementi ispirati all'arte Maori
- **Colori**: Palette blu e dorata
- **Typography**: Google Fonts (Inter, Playfair Display, Crimson Text)
- **Responsive**: Completamente responsive per tutti i dispositivi
- **Performance**: Ottimizzato per velocità e SEO

### Funzionalità Principali

#### 1. Portfolio Personalizzato
- Custom Post Type "Portfolio" per mostrare i lavori
- Categorie e tag personalizzati
- Filtri avanzati per tipo, luogo, categoria
- Layout grid responsivo
- Lightbox per le immagini
- Meta fields personalizzati (cliente, data, luogo, durata)

#### 2. Sistema di Prenotazioni con Bookly
- Integrazione completa con plugin Bookly per gestione professionale appuntamenti
- Stili personalizzati per mantenere coerenza del design del tema
- Form di prenotazione con calendario interattivo e selezione orari
- Sistema di notifiche e conferme automatiche via Bookly
- Analytics tracking per il flusso di prenotazione
- Metodi di contatto alternativi (telefono, email) per flessibilità

#### 3. Shop Online
- Custom Post Type "Shop Product" per merchandising
- Supporto per flash tattoo, merchandise, stampe
- Gestione stock e prezzi
- Integrazione e-commerce pronta

#### 4. Blog/News
- Template personalizzati per articoli
- Social sharing integrato
- Reading time calculator
- Articoli correlati
- Navigazione tra articoli
- Bio autore

#### 5. Multilingual Ready
- Text domain configurato
- Supporto per plugin multilingua (Polylang, WPML)
- String tradotte in italiano
- Struttura preparata per traduzioni

#### 6. SEO Ottimizzato
- Structured data
- Meta tags OpenGraph e Twitter
- Performance monitoring
- Lazy loading immagini
- CSS e JS ottimizzati

### Custom Post Types

#### Portfolio
- **Slug**: portfolio
- **Archive**: portfolio/
- **Single**: portfolio/[slug]
- **Meta Fields**:
  - Cliente
  - Data realizzazione
  - Luogo (Milano/Messina)
  - Durata sessione
  - Note del progetto

#### Prodotti Shop
- **Slug**: shop
- **Archive**: shop/
- **Single**: shop/[slug]
- **Meta Fields**:
  - Prezzo
  - Tipo prodotto
  - Quantità stock
  - Codice prodotto (SKU)

### Tassonomie Personalizzate

#### Portfolio Category
- **Slug**: portfolio-category
- **Hierarchical**: Sì
- **For**: Portfolio

#### Portfolio Tag
- **Slug**: portfolio-tag
- **Hierarchical**: No
- **For**: Portfolio

### Locations/Sedi

Il tema supporta due sedi principali:

#### Milano
- Studio principale
- Orari: Lun-Ven 10:00-19:00, Sab 10:00-18:00, Dom su appuntamento

#### Messina
- Studio secondario  
- Orari: Lun-Ven 15:00-20:00, Sab 10:00-18:00, Dom su appuntamento

### Customizer Options

#### Hero Section
- Titolo hero personalizzabile
- Sottotitolo hero personalizzabile

#### Informazioni Contatto
- Indirizzo Milano
- Indirizzo Messina
- Telefono
- Email

### Menu Locations

#### Primary Menu
- **Location**: Header principale
- **Default Items**:
  - Home
  - About
  - Portfolio
  - Servizi
  - Shop
  - Blog
  - Contatti

#### Footer Menu
- **Location**: Footer
- **Default Items**: Come primary menu

#### Social Menu
- **Location**: Header e Footer
- **Items**:
  - Instagram
  - Facebook
  - TikTok
  - YouTube

### Widget Areas

#### Primary Sidebar
- Sidebar principale per pagine e blog

#### Footer Columns (3)
- Footer Column 1: Informazioni azienda
- Footer Column 2: Link rapidi
- Footer Column 3: Newsletter e contatti

### JavaScript Features

#### Core Functionality
- Mobile menu responsive
- Smooth scrolling per anchor links
- Portfolio filter con AJAX
- Form booking con validazione
- Newsletter subscription
- Loading states e animazioni
- Back to top button

#### Performance Features
- Lazy loading immagini
- Intersection Observer per animazioni
- Performance monitoring
- Cookie consent GDPR

#### User Experience
- Lightbox per gallery
- Social sharing
- Copy to clipboard
- Notification system
- Search functionality

### Sicurezza e Performance

#### Sicurezza
- Nonce verification per AJAX
- Sanitizzazione input
- Escape output
- Capability checks

#### Performance
- CSS e JS minificati in produzione
- Image optimization ready
- Caching headers support
- CDN ready

### Browser Support

- **Modern Browsers**: Chrome 80+, Firefox 75+, Safari 13+, Edge 80+
- **Mobile**: iOS Safari 13+, Chrome Mobile 80+
- **Progressive Enhancement**: Fallback per browser più vecchi

### Requisiti

#### WordPress
- **Versione minima**: WordPress 5.0+
- **PHP**: 7.4+ (raccomandato 8.0+)
- **MySQL**: 5.7+ o MariaDB 10.3+

#### Plugin Raccomandati
- **SEO**: Yoast SEO o RankMath
- **Multilingual**: Polylang o WPML
- **Caching**: WP Rocket o W3 Total Cache
- **Security**: Wordfence o Sucuri
- **Backup**: UpdraftPlus
- **Forms**: Contact Form 7 (alternativa ai form custom)

#### Plugin Opzionali
- **E-commerce**: WooCommerce (per shop avanzato)
- **Analytics**: Google Analytics Dashboard
- **Social**: Social Media Share Buttons

### Struttura File

```
marcello-scavo-tattoo/
├── style.css                 # CSS principale
├── functions.php            # Funzioni tema
├── index.php               # Homepage
├── header.php              # Header template
├── footer.php              # Footer template
├── single.php              # Single blog post
├── single-portfolio.php    # Single portfolio item
├── archive-portfolio.php   # Portfolio archive
├── screenshot.png          # Screenshot tema
├── README.md              # Questo file
├── assets/
│   ├── css/
│   │   └── components.css  # CSS componenti
│   ├── js/
│   │   └── main.js        # JavaScript principale
│   └── images/            # Immagini tema
└── languages/             # File traduzioni
```

## Plugin Integrati e Raccomandati

### Plugin Richiesti
- **Bookly** (versione gratuita o Pro): Sistema di prenotazione professionale per appuntamenti
  - URL: https://wordpress.org/plugins/bookly-responsive-appointment-booking-tool/
  - Funzionalità: Gestione calendario, prenotazioni online, notifiche automatiche

### Plugin Raccomandati
- **Polylang**: Supporto multilingua (EN/IT)
  - URL: https://wordpress.org/plugins/polylang/
  - Alternativa: WPML
- **Contact Form 7**: Moduli di contatto aggiuntivi
- **Yoast SEO**: Ottimizzazione SEO avanzata
- **WP Optimize**: Ottimizzazione prestazioni

### Configurazione Bookly
1. Installa e attiva il plugin Bookly
2. Configura i servizi (es. "Consultation", "Tattoo Session", "Touch-up")
3. Imposta gli orari di lavoro per Milano e Messina
4. Configura le notifiche email
5. Il tema applicherà automaticamente stili personalizzati coerenti

### Installazione

1. **Upload**: Carica la cartella del tema in `/wp-content/themes/`
2. **Attivazione**: Attiva il tema da Aspetto > Temi
3. **Menu**: Configura i menu in Aspetto > Menu
4. **Customizer**: Personalizza le opzioni in Aspetto > Personalizza
5. **Content**: Aggiungi contenuti portfolio, blog e prodotti shop

### Configurazione Iniziale

#### 1. Impostazioni Permalink
- Vai in Impostazioni > Permalink
- Seleziona "Nome articolo" o struttura personalizzata
- Salva per attivare i custom post types

#### 2. Menu
- Crea menu principale con le voci necessarie
- Assegna al location "Primary Menu"
- Crea menu footer (opzionale)

#### 3. Widget
- Configura widget sidebar e footer
- Aggiungi widget social media

#### 4. Customizer
- Configura Hero section
- Inserisci informazioni contatto
- Carica logo personalizzato

### Personalizzazione

#### Colori
Per modificare i colori del tema, edita le variabili CSS in `style.css`:

```css
:root {
    --primary-blue: #1e3a8a;
    --secondary-blue: #3b82f6;
    --primary-gold: #d97706;
    --secondary-gold: #fbbf24;
    /* ... altre variabili */
}
```

#### Typography
Le font si possono modificare nelle variabili CSS:

```css
:root {
    --font-primary: 'Inter', sans-serif;
    --font-secondary: 'Playfair Display', serif;
    --font-accent: 'Crimson Text', serif;
}
```

#### Layout
I breakpoint responsive si possono modificare nei media queries del CSS.

### Supporto

Per supporto tecnico o personalizzazioni:
- Email: [tuo-email]
- Documentazione: [link-documentazione]
- GitHub: [link-repository]

### Licenza

Questo tema è sviluppato specificamente per Marcello Scavo.
Tutti i diritti riservati.

### Credits

- **Sviluppo**: GitHub Copilot
- **Design**: Ispirato all'arte Maori e al mondo del tatuaggio
- **Icons**: Font Awesome
- **Fonts**: Google Fonts
- **Framework**: Custom CSS Grid e Flexbox

### Changelog

#### v1.0.0 (2025-08-31)
- Release iniziale
- Portfolio system completo
- Integrazione Bookly per prenotazioni professionali
- Shop online
- Blog responsive
- Multilingual ready (Polylang/WPML)
- SEO ottimizzato
