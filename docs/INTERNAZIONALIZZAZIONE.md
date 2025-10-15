# Sistema di Internazionalizzazione - Marcello Scavo Tattoo Theme

## Panoramica

Il tema Marcello Scavo Tattoo include un sistema completo di internazionalizzazione che supporta tre lingue:
- **Italiano (IT)** - Lingua predefinita
- **Inglese (EN)** - English  
- **Spagnolo (ES)** - Español

## 🎯 Caratteristiche

### ✅ Completato
1. **File di traduzione (.po/.mo)** creati per tutte e tre le lingue
2. **Tutte le stringhe del tema** marcate per la traduzione con `__()` e `_e()`
3. **Switch di lingua automatico** nel header
4. **Supporto per plugin multilingual** (Polylang, WPML)
5. **Sistema di fallback manuale** quando nessun plugin è attivo
6. **Shortcode per language switcher** `[language_switcher]`
7. **Salvataggio preferenze** in localStorage e cookie

### 🔧 Come Funziona

#### Con Plugin Multilingual
Se hai installato **Polylang** o **WPML**, il tema rileva automaticamente il plugin e usa le sue funzionalità native per il cambio lingua.

#### Senza Plugin (Sistema Manuale)
Il tema include un sistema di fallback che:
- Salva la preferenza di lingua in cookie e localStorage
- Cambia dinamicamente le traduzioni via JavaScript
- Mostra notifiche quando si cambia lingua
- Mantiene la scelta dell'utente tra le visite

## 📁 Struttura File

```
wp-content/themes/marcello-scavo-tattoo/languages/
├── marcello-scavo.pot     # Template di traduzione
├── it_IT.po              # Traduzioni italiane
├── it_IT.mo              # File binario italiano
├── en_US.po              # Traduzioni inglesi  
├── en_US.mo              # File binario inglese
├── es_ES.po              # Traduzioni spagnole
└── es_ES.mo              # File binario spagnolo
```

## 🚀 Uso

### Nel Header
Il selettore di lingua appare automaticamente nel header del sito.

### Via Shortcode
Puoi aggiungere un selettore di lingua ovunque usando:

```php
[language_switcher]                          # Dropdown predefinito
[language_switcher style="links"]            # Link separati
[language_switcher show_flags="false"]       # Solo testo
[language_switcher show_names="false"]       # Solo bandiere
```

### Via PHP
```php
// Ottenere la lingua corrente
$current_lang = marcello_scavo_get_current_language();

// Mostrare contenuto condizionale
if ($current_lang === 'en') {
    echo __('Welcome!', 'marcello-scavo');
} else {
    echo __('Benvenuto!', 'marcello-scavo');
}
```

### Via JavaScript
```javascript
// Cambiare lingua manualmente
marcelloScavoChangeLanguage('en');

// Ottenere lingua salvata
const savedLang = localStorage.getItem('marcello_scavo_language');
```

## 🛠️ Plugin Consigliati

### Polylang (Gratuito)
- Plugin più popolare per WordPress
- Supporto completo integrato nel tema
- Gestione URL separati per lingua
- SEO-friendly

### WPML (Premium)
- Soluzione enterprise
- Supporto avanzato per e-commerce
- Traduzione automatica
- Integrazione con molti plugin

## 🔧 Personalizzazione

### Aggiungere Nuove Stringhe
Quando aggiungi nuovo contenuto, marca le stringhe per la traduzione:

```php
// Testo semplice
echo __('Nuovo testo', 'marcello-scavo');

// Testo con echo
_e('Nuovo testo', 'marcello-scavo');

// Testo con variabili
printf(__('Ciao %s!', 'marcello-scavo'), $nome);

// Plurali
_n('1 tatuaggio', '%d tatuaggi', $count, 'marcello-scavo');
```

### Aggiornare Traduzioni
1. Modifica i file `.po` nella cartella `/languages/`
2. Ricompila i file `.mo`:
   ```bash
   cd wp-content/themes/marcello-scavo-tattoo/languages/
   msgfmt en_US.po -o en_US.mo
   msgfmt es_ES.po -o es_ES.mo
   msgfmt it_IT.po -o it_IT.mo
   ```

### Aggiungere Nuove Lingue
1. Crea nuovo file `.po` (es. `fr_FR.po`)
2. Traduci tutte le stringhe
3. Compila in `.mo`: `msgfmt fr_FR.po -o fr_FR.mo`
4. Aggiungi supporto in `functions.php` e `main.js`

## 🌐 URL e SEO

### Con Plugin
I plugin gestiscono automaticamente:
- URL separati per lingua (`/en/`, `/es/`)
- Meta tag hreflang
- Sitemap multilingue
- Redirects automatici

### Sistema Manuale
- Usa parametri URL: `?lang=en`
- Salva preferenze in cookie
- Non influenza SEO (stesso contenuto)

## 🧪 Test

### Testare Traduzioni
1. Vai su `wp-admin/options-general.php`
2. Cambia "Lingua del sito"
3. Verifica che le stringhe cambino

### Testare Switch Manuale
1. Usa il selettore nel header
2. Verifica che le traduzioni si applichino
3. Ricarica la pagina per testare la persistenza

## 📝 Note Tecniche

- **Text Domain**: `marcello-scavo`
- **Cartella traduzioni**: `/languages/`
- **Encoding**: UTF-8
- **Formato plurali**: Standard GNU gettext

## 🆘 Risoluzione Problemi

### Le traduzioni non funzionano
1. Verifica che i file `.mo` esistano
2. Controlla i permessi dei file
3. Svuota cache se usi plugin di cache
4. Verifica il text domain nelle stringhe

### Plugin multilingual non rilevato
1. Verifica che il plugin sia attivo
2. Controlla la configurazione del plugin
3. Svuota cache
4. Riattiva il tema

### JavaScript non funziona
1. Controlla la console per errori
2. Verifica che jQuery sia caricato
3. Controlla conflitti con altri plugin

## 🔄 Manutenzione

### Aggiornamenti Regolari
- Aggiungi nuove stringhe ai file di traduzione
- Mantieni sincronizzati tutti i file `.po`
- Testa dopo aggiornamenti di plugin/WordPress
- Backup dei file di traduzione personalizzati

---

**Creato il**: 2 Settembre 2025  
**Versione tema**: 1.0.0  
**Compatibilità**: WordPress 5.0+  
