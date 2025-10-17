# Marcello Scavo Tattoo Theme - Documentazione Completa

Tema WordPress personalizzato per l'artista del tatuaggio Marcello Scavo.

## 📋 Indice
1. [Caratteristiche del Tema](#caratteristiche-del-tema)
2. [Installazione e Setup](#installazione-e-setup)
3. [Configurazione Bookly](#configurazione-bookly)
4. [Widget Instagram](#widget-instagram)
5. [Google Reviews](#google-reviews)
6. [Gallery Showcase](#gallery-showcase)
7. [Customizzazioni Avanzate](#customizzazioni-avanzate)
8. [Risoluzione Problemi](#risoluzione-problemi)
9. [Supporto e Manutenzione](#supporto-e-manutenzione)

---

## Caratteristiche del Tema

### 🎨 Design
- **Stile**: Artistico-minimalista con elementi ispirati all'arte Maori
- **Colori**: Palette blu e dorata (#1e3a8a, #d97706)
- **Typography**: Google Fonts (Inter, Playfair Display, Crimson Text)
- **Responsive**: Completamente responsive per tutti i dispositivi
- **Performance**: Ottimizzato per velocità e SEO

### ⚡ Funzionalità Principali

#### 1. Portfolio Personalizzato
- Custom Post Type "Portfolio" per mostrare i lavori
- Categorie e tag personalizzati
- Filtri avanzati per tipo, luogo, categoria
- Layout grid responsivo con masonry
- Lightbox per le immagini ad alta risoluzione
- Meta fields personalizzati (cliente, data, luogo, durata)

#### 2. Sistema di Prenotazioni con Bookly
- Integrazione completa con plugin Bookly
- Stili personalizzati per coerenza design
- Form di prenotazione con calendario interattivo
- Sistema di notifiche automatiche
- Analytics tracking per conversioni
- Metodi di contatto alternativi

#### 3. Instagram Feed Automatico
- Sistema a cascata con fallback intelligenti
- Cache ottimizzato per performance
- Supporto per multiple modalità (API/Semplice)
- Layout responsivo customizzabile

#### 4. Shop Online
- Custom Post Type "Shop Product"
- Gestione stock e prezzi
- Integrazione e-commerce ready
- Supporto WooCommerce

#### 5. Blog/News
- Template personalizzati per articoli
- Social sharing integrato
- Reading time calculator
- Articoli correlati
- SEO ottimizzato

#### 6. Google Reviews Integration
- Widget per recensioni automatiche
- Stelle rating visuali
- Cache per performance
- Fallback per offline

---

## 🚀 Installazione e Setup

### Requisiti Sistema
- **WordPress**: 5.0+ (raccomandato 6.0+)
- **PHP**: 7.4+ (raccomandato 8.0+)
- **MySQL**: 5.7+ o MariaDB 10.3+
- **Memory Limit**: 128MB+ (raccomandato 256MB)

### Installazione
1. **Upload tema**: Carica in `/wp-content/themes/marcello-scavo-tattoo/`
2. **Attivazione**: Vai su Aspetto > Temi e attiva
3. **Permalink**: Imposta Permalink su "Nome articolo"
4. **Menu**: Crea e assegna menu principale
5. **Widget**: Configura widget areas
6. **Customizer**: Personalizza opzioni tema

### Plugin Raccomandati
- **Bookly Booking Plugin** (richiesto per prenotazioni)
- **Polylang** (multilingua EN/IT)
- **Yoast SEO** (ottimizzazione SEO)
- **Contact Form 7** (form contatti)
- **WP Optimize** (performance)

---

## 📅 Configurazione Bookly

### Setup Iniziale Bookly

#### Step 1: Installazione
```
1. Plugin > Aggiungi nuovo
2. Cerca "Bookly"
3. Installa "Bookly Booking Plugin"
4. Attiva plugin
```

#### Step 2: Configurazione Aziendale
```
Bookly > Impostazioni > Azienda:
- Nome: Marcello Scavo Tattoos
- Indirizzo Milano: Via Asiago, 59, Milano
- Indirizzo Messina: [Inserire indirizzo]
- Telefono: 347 627 0570
- Email: info@marcelloscavo.com
- Sito web: https://marcelloscavo.com
```

#### Step 3: Creazione Servizi
```
Bookly > Servizi > Nuovo Servizio:

1. Tatuaggio Personalizzato
   - Durata: 120 minuti
   - Prezzo: €200-400 (personalizzabile)
   - Categoria: Tatuaggi
   - Descrizione: "Sessione tatuaggio con consulenza design inclusa"

2. Consulenza Design (opzionale)
   - Durata: 30 minuti
   - Prezzo: €50
   - Categoria: Consulenze
   - Descrizione: "Consulenza per design personalizzato"

3. Ritocco/Touch-up
   - Durata: 60 minuti
   - Prezzo: €100
   - Categoria: Ritocchi
   - Descrizione: "Ritocco tatuaggio esistente"

4. Cover-up
   - Durata: 180 minuti
   - Prezzo: €300-500
   - Categoria: Tatuaggi
   - Descrizione: "Copertura tatuaggio esistente"
```

#### Step 4: Configurazione Staff
```
Bookly > Staff > Aggiungi Staff:
- Nome: Marcello Scavo
- Email: marcello@marcelloscavo.com
- Telefono: 347 627 0570
- Servizi: Tutti i servizi tatuaggio
- Sedi: Milano e Messina
```

#### Step 5: Orari di Lavoro
```
Milano:
- Lunedì-Venerdì: 10:00-19:00
- Sabato: 10:00-17:00
- Domenica: Su appuntamento

Messina:
- Personalizzabile secondo disponibilità
- Generalmente: Lun-Ven 15:00-20:00, Sab 10:00-18:00
```

#### Step 6: Notifiche Email
```
Bookly > Notifiche:
- Conferma prenotazione cliente: ✅ Attiva
- Promemoria 24h prima: ✅ Attiva
- Notifica a staff: ✅ Attiva
- Cancellazione prenotazione: ✅ Attiva
```

### Widget Bookly nel Tema

#### Shortcode Base
```
[bookly-form]
```

#### Shortcode Avanzati
```
[bookly-form service_id="1"]              # Servizio specifico
[bookly-form staff_member_id="1"]         # Staff specifico
[bookly-form hide="staff,service"]        # Nascondi campi
[bookly-form category_id="1"]             # Categoria specifica
```

#### Configurazione Widget
```
1. Aspetto > Widget
2. Area "Prenotazione CTA"
3. Aggiungi "Widget Bookly Prenotazioni"
4. Compila:
   - Titolo: "Prenota il tuo tatuaggio oggi!"
   - Sottotitolo: "Trasforma la tua pelle in arte"
   - Shortcode: [bookly-form]
   - Features attive: ✅
```

---

## 📸 Widget Instagram

### 🎯 Sistema Automatico
Il tema include un sistema Instagram automatico con fallback intelligenti:

1. **API RSS**: Preleva post da feed pubblici Instagram
2. **Fallback immagini reali**: Usa immagini da `/assets/images/`
3. **Fallback placeholder**: Immagini di backup
4. **Cache**: 30 minuti per ottime performance

### Configurazione Widget Instagram

#### Setup Base
```
1. Aspetto > Widget
2. Cerca "Instagram Feed - Marcello Scavo"
3. Trascina nell'area desiderata
4. Configura:
   - Numero post: 3, 6, 9 o 12
   - Layout: Griglia, Carosello, Masonry
   - Mostra caption: Sì/No
   - Mostra data: Sì/No
```

#### Modalità API (Avanzata)
```
Se hai accesso all'API Instagram:
1. Registra app su Facebook Developers
2. Ottieni Access Token
3. Nel widget seleziona "API Mode"
4. Inserisci token nel campo apposito
```

#### Modalità Semplice (Consigliata)
```
1. Widget Instagram > Modalità: "Simple Mode"
2. Username: marcelloscavo_art
3. Il sistema userà automaticamente fallback
4. Funziona sempre, nessuna configurazione richiesta
```

### Immagini Fallback
```
Posizione: /assets/images/
File supportati:
- IMG_4800.jpg
- IMG_4854.JPG
- DSC03105.JPEG
- 54B3F245-E22C-4DDC-B5D2-885750AD64E6.JPG

Per aggiungere nuove immagini:
1. Carica in /assets/images/
2. Il widget le rileverà automaticamente
```

---

## ⭐ Google Reviews

### Setup Widget Reviews

#### Configurazione Base
```
1. Aspetto > Widget
2. Aggiungi "Google Reviews"
3. Configura:
   - Business Name: Marcello Scavo Tattoos
   - Place ID: [Ottieni da Google My Business]
   - Numero recensioni: 5
   - Mostra stelle: ✅
   - Cache durata: 24 ore
```

#### Ottenere Google Place ID
```
1. Vai su Google My Business
2. Trova la tua attività
3. Copia Place ID dall'URL
4. Alternative: Usa tool online "Place ID Finder"
```

#### Layout Recensioni
```
Layout disponibili:
- Lista verticale
- Griglia 2 colonne
- Carosello orizzontale
- Cards minimali
```

### Configurazione Google My Business

#### Ottimizzazione Profilo
```
1. Completa tutte le informazioni:
   - Nome: Marcello Scavo Tattoos
   - Categoria: Tatuatore
   - Indirizzo: Via Asiago, 59, Milano
   - Telefono: 347 627 0570
   - Sito: https://marcelloscavo.com
   - Orari: Come configurato in Bookly

2. Aggiungi foto di qualità:
   - Logo
   - Interno studio
   - Lavori completati
   - Staff al lavoro

3. Raccogli recensioni:
   - Chiedi ai clienti soddisfatti
   - Invia link diretto per recensione
   - Rispondi sempre alle recensioni
```

---

## 🖼️ Gallery Showcase

### Configurazione Portfolio

#### Custom Post Type Portfolio
```
- Slug: portfolio
- Archive: /portfolio/
- Single: /portfolio/[slug]/
- Supporta: Title, Editor, Featured Image, Custom Fields
```

#### Meta Fields Portfolio
```
- Cliente: Nome del cliente (opzionale)
- Data realizzazione: Data completamento
- Luogo: Milano/Messina
- Durata sessione: Ore impiegate
- Categoria: Tipo di tatuaggio
- Note progetto: Dettagli tecnici
```

#### Categorie Portfolio
```
Categorie principali:
- Traditional
- Maori/Tribali
- Realistici
- Blackwork
- Color Work
- Cover-up
- Piccoli/Delicati
```

### Setup Gallery Showcase

#### Configurazione Layout
```
1. Aspetto > Personalizza > Portfolio Settings
2. Layout griglia:
   - Colonne desktop: 3
   - Colonne tablet: 2
   - Colonne mobile: 1
   - Spaziatura: 20px

3. Filtri:
   - Mostra filtri categoria: ✅
   - Filtro per luogo: ✅
   - Filtro per anno: ✅
   - Animazione filtri: Fade/Slide
```

#### Lightbox Gallery
```
Funzionalità lightbox:
- Zoom alta risoluzione
- Navigazione keyboard
- Swipe mobile
- Caption con dettagli
- Condivisione social
- Download immagine (opzionale)
```

#### Ottimizzazione Immagini
```
Dimensioni raccomandate:
- Thumbnail: 400x400px
- Featured: 800x600px
- Lightbox: 1200x900px
- Formato: JPG/WebP

Compressione:
- Thumbnail: 85% qualità
- Featured: 90% qualità
- Lightbox: 95% qualità
```

---

## 🎨 Customizzazioni Avanzate

### Personalizzazione Colori

#### Variabili CSS Principali
```css
:root {
    /* Colori Brand */
    --primary-blue: #1e3a8a;
    --secondary-blue: #3b82f6;
    --primary-gold: #d97706;
    --secondary-gold: #fbbf24;
    
    /* Colori UI */
    --background: #ffffff;
    --surface: #f8fafc;
    --text-primary: #1f2937;
    --text-secondary: #6b7280;
    
    /* Colori Stato */
    --success: #10b981;
    --warning: #f59e0b;
    --error: #ef4444;
    --info: #3b82f6;
}
```

#### Personalizzazione Palette
```css
/* Per cambiare colore primario */
.custom-primary {
    --primary-blue: #your-color;
    --primary-gold: #your-accent;
}

/* Applicare al body */
body.custom-primary {
    /* Tutti i colori si aggiorneranno automaticamente */
}
```

### Typography Customization

#### Font Loading
```css
/* Google Fonts Preload */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&family=Crimson+Text:ital,wght@0,400;0,600;1,400&display=swap');

/* Variabili Font */
:root {
    --font-primary: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    --font-secondary: 'Playfair Display', Georgia, serif;
    --font-accent: 'Crimson Text', 'Times New Roman', serif;
}
```

#### Responsive Typography
```css
/* Scale Typography */
:root {
    --text-xs: 0.75rem;    /* 12px */
    --text-sm: 0.875rem;   /* 14px */
    --text-base: 1rem;     /* 16px */
    --text-lg: 1.125rem;   /* 18px */
    --text-xl: 1.25rem;    /* 20px */
    --text-2xl: 1.5rem;    /* 24px */
    --text-3xl: 1.875rem;  /* 30px */
    --text-4xl: 2.25rem;   /* 36px */
    --text-5xl: 3rem;      /* 48px */
}

/* Mobile Scale */
@media (max-width: 768px) {
    :root {
        --text-3xl: 1.5rem;
        --text-4xl: 1.875rem;
        --text-5xl: 2.25rem;
    }
}
```

### Layout Customization

#### Grid System
```css
/* Container Sizes */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

.container-wide {
    max-width: 1400px;
}

.container-narrow {
    max-width: 800px;
}

/* Grid Utilities */
.grid {
    display: grid;
    gap: 1.5rem;
}

.grid-2 { grid-template-columns: repeat(2, 1fr); }
.grid-3 { grid-template-columns: repeat(3, 1fr); }
.grid-4 { grid-template-columns: repeat(4, 1fr); }

/* Responsive Grids */
@media (max-width: 768px) {
    .grid-2,
    .grid-3,
    .grid-4 {
        grid-template-columns: 1fr;
    }
}
```

### Performance Optimization

#### CSS Optimization
```css
/* Critical CSS Inline */
/* Styles above-the-fold vanno inline nell'head */

/* Non-critical CSS */
/* Carica via loadCSS o defer */

/* CSS Purging */
/* Rimuovi CSS non utilizzato in produzione */
```

#### JavaScript Optimization
```javascript
// Lazy Loading
const lazyImages = document.querySelectorAll('[data-lazy]');
const imageObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const img = entry.target;
            img.src = img.dataset.lazy;
            img.classList.remove('lazy');
            observer.unobserve(img);
        }
    });
});

lazyImages.forEach(img => imageObserver.observe(img));
```

---

## 🔧 Risoluzione Problemi

### Problemi Comuni e Soluzioni

#### 1. Portfolio non visibile
```
Problema: Custom post type non appare
Soluzione:
1. Vai in Impostazioni > Permalink
2. Salva nuovamente (flush rewrite rules)
3. Verifica functions.php per registrazione CPT
```

#### 2. Bookly non si carica
```
Problema: Widget Bookly non funziona
Soluzioni:
1. Verifica plugin Bookly attivo
2. Controlla shortcode corretto
3. Verifica JavaScript console errori
4. Prova modalità debug WP
```

#### 3. Instagram feed vuoto
```
Problema: Nessuna immagine Instagram
Soluzioni:
1. Modalità Simple: Verifica immagini in /assets/images/
2. Modalità API: Controlla Access Token
3. Controlla cache (svuota se necessario)
4. Verifica username Instagram corretto
```

#### 4. Performance lente
```
Problema: Sito lento
Soluzioni:
1. Installa plugin caching (WP Rocket)
2. Ottimizza immagini (WebP, compressione)
3. Minifica CSS/JS
4. Abilita CDN
5. Controlla plugin pesanti
```

#### 5. Responsive issues
```
Problema: Layout rotto su mobile
Soluzioni:
1. Verifica CSS media queries
2. Testa su dispositivi reali
3. Usa Chrome DevTools
4. Controlla viewport meta tag
```

### Debug e Logging

#### Abilita Debug WordPress
```php
// wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', true);
```

#### Log Personalizzati
```php
// functions.php
function tema_log($message) {
    if (WP_DEBUG === true) {
        error_log('[TEMA] ' . $message);
    }
}

// Uso
tema_log('Instagram widget: Avvio caricamento feed');
```

### Backup e Sicurezza

#### Backup Automatici
```
Plugin raccomandati:
- UpdraftPlus: Backup completi automatici
- WP Clone: Clonazione staging
- All-in-One WP Migration: Migrazione facile
```

#### Sicurezza
```
Plugin raccomandati:
- Wordfence: Firewall e malware scan
- Sucuri: Protezione avanzata
- iThemes Security: Hardening WordPress
```

---

## 📞 Supporto e Manutenzione

### Contatti Supporto
- **Email Tecnico**: support@your-domain.com
- **GitHub Issues**: [Link repository]
- **Documentazione**: [Link docs]

### Aggiornamenti Tema

#### Controllo Versione
```php
// Versione corrente
$tema_version = wp_get_theme()->get('Version');
echo 'Versione tema: ' . $tema_version;
```

#### Log Modifiche
```
v1.0.0 (2025-09-02)
- Release iniziale
- Portfolio system completo
- Integrazione Bookly
- Instagram feed automatico
- Google Reviews widget
- Responsive design
- SEO ottimizzato
```

### Manutenzione Periodica

#### Checklist Mensile
- [ ] Backup completo sito
- [ ] Aggiornamento plugin
- [ ] Controllo performance PageSpeed
- [ ] Verifica funzionamento form
- [ ] Test prenotazioni Bookly
- [ ] Controllo feed Instagram
- [ ] Review Google My Business
- [ ] Analisi Google Analytics

#### Checklist Trimestrale
- [ ] Ottimizzazione database
- [ ] Pulizia cache
- [ ] Audit sicurezza
- [ ] Test cross-browser
- [ ] Aggiornamento contenuti
- [ ] Review SEO
- [ ] Backup off-site

### Best Practices

#### Sviluppo
1. **Sempre staging**: Test modifiche su ambiente di sviluppo
2. **Version control**: Usa Git per tracking modifiche
3. **Backup pre-modifica**: Backup prima di ogni modifica
4. **Test multibrowser**: Verifica compatibilità
5. **Performance first**: Ottimizza sempre per velocità

#### Contenuti
1. **SEO first**: Ottimizza sempre per motori ricerca
2. **Mobile first**: Progetta prima per mobile
3. **User experience**: Priorità alla facilità d'uso
4. **Accessibilità**: Rispetta standard WCAG
5. **Performance**: Immagini ottimizzate, contenuti snelli

---

## 📋 Checklist Pre-Launch

### Technical
- [ ] SSL certificato installato
- [ ] Redirect HTTP → HTTPS
- [ ] Google Analytics configurato
- [ ] Google Search Console verificato
- [ ] Sitemap XML generata
- [ ] Robots.txt configurato
- [ ] Plugin caching attivo
- [ ] Backup automatici configurati

### Content
- [ ] Portfolio popolato (min 10 progetti)
- [ ] Blog con almeno 3 articoli
- [ ] Pagina About completata
- [ ] Pagina Servizi dettagliata
- [ ] Pagina Contatti con info corrette
- [ ] Menu navigazione completo
- [ ] Footer con link legali

### Bookly
- [ ] Servizi configurati
- [ ] Orari impostati
- [ ] Notifiche email test
- [ ] Pagamenti configurati (se richiesto)
- [ ] Staff configurato
- [ ] Test prenotazione completo

### Social & Reviews
- [ ] Instagram widget funzionante
- [ ] Google My Business ottimizzato
- [ ] Widget recensioni attivo
- [ ] Social media links corretti
- [ ] Pixel Facebook/Meta (se richiesto)

---

## 💡 Tips e Trucchi

### Performance
```php
// Preload delle risorse critiche
function tema_preload_resources() {
    echo '<link rel="preload" href="' . get_template_directory_uri() . '/assets/css/critical.css" as="style">';
    echo '<link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" as="style">';
}
add_action('wp_head', 'tema_preload_resources', 5);
```

### SEO Enhancement
```php
// Schema.org per Business
function tema_business_schema() {
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'TattooArtist',
        'name' => 'Marcello Scavo',
        'url' => home_url(),
        'image' => get_template_directory_uri() . '/assets/images/logo.png',
        'telephone' => '347 627 0570',
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => 'Via Asiago, 59',
            'addressLocality' => 'Milano',
            'addressCountry' => 'IT'
        ]
    ];
    
    echo '<script type="application/ld+json">' . json_encode($schema) . '</script>';
}
add_action('wp_head', 'tema_business_schema');
```

### User Experience
```javascript
// Smooth scroll per anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});
```

---

**Fine Documentazione** 

*Ultima modifica: 2 Settembre 2025*  
*Versione: 1.0.0*
