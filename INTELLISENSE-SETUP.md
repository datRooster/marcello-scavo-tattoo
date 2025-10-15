# 🧠 IntelliSense Configuration
## WordPress Development with VS Code

**Configurazione completata:** 2024-12-19

---

## 🎯 Problema Risolto

Gli errori IntelliSense per le funzioni WordPress come `current_user_can()`, `wp_enqueue_style()`, `get_template_directory()` erano falsi positivi che rendevano confuso lo sviluppo.

### ❌ Prima della configurazione:
- Funzioni WordPress non riconosciute
- Errori "Undefined function" ovunque
- Nessun autocompletamento
- Confusione tra errori reali e falsi positivi

### ✅ Dopo la configurazione:
- WordPress Stubs installati tramite Composer
- Tutte le funzioni WordPress riconosciute
- Autocompletamento completo
- Solo errori reali mostrati

---

## 🛠️ Componenti Installati

### WordPress Stubs (via Composer)
```json
{
    "require-dev": {
        "php-stubs/wordpress-stubs": "^6.3",
        "squizlabs/php_codesniffer": "^3.7",
        "wp-coding-standards/wpcs": "^3.0",
        "phpcompatibility/phpcompatibility-wp": "^2.1"
    }
}
```

### VS Code Configuration (`.vscode/settings.json`)
```json
{
    "intelephense.stubs": ["wordpress"],
    "intelephense.environment.includePaths": [
        "./wp-content/themes/marcello-scavo-tattoo/vendor/php-stubs/wordpress-stubs",
        "./wp-intellisense-helper.php"
    ],
    "intelephense.diagnostics.undefinedFunctions": true,
    "intelephense.completion.insertUseDeclaration": true
}
```

### IntelliSense Helper (`wp-intellisense-helper.php`)
- Definisce costanti WordPress comuni
- Include WordPress Stubs automaticamente  
- Configura ambiente di sviluppo
- Constants specifiche del tema

---

## 🚀 Strumenti di Sviluppo

### PHP CodeSniffer
```bash
# Lint WordPress coding standards
./dev-tools.sh lint

# Fix coding standards automatically  
./dev-tools.sh fix

# Check PHP compatibility
./dev-tools.sh compat
```

### VS Code Tasks
- **Ctrl/Cmd + Shift + P** → "Tasks: Run Task"
- PHP CodeSniffer Lint
- PHP CodeSniffer Fix
- PHP Compatibility Check
- Minify CSS
- Generate Development Report
- Backup Theme

### Development Scripts (`dev-tools.sh`)
```bash
./dev-tools.sh setup    # Setup development environment
./dev-tools.sh lint     # Run PHP CodeSniffer
./dev-tools.sh fix      # Auto-fix coding standards
./dev-tools.sh compat   # Check PHP compatibility
./dev-tools.sh minify   # Minify CSS files
./dev-tools.sh report   # Generate development report
./dev-tools.sh backup   # Create theme backup
```

---

## 📁 File Structure

```
.vscode/
├── settings.json       # IntelliSense configuration
├── tasks.json         # Development tasks
└── extensions.json    # Recommended extensions

wp-content/themes/marcello-scavo-tattoo/
├── composer.json      # PHP dependencies
├── phpcs.xml         # Coding standards rules
├── dev-tools.sh      # Development helper scripts
├── intellisense-test.php  # Test IntelliSense functionality
└── vendor/
    └── php-stubs/
        └── wordpress-stubs/  # WordPress function definitions

wp-intellisense-helper.php  # WordPress constants and includes
```

---

## ✅ Verifica Configurazione

### Test IntelliSense
1. Apri `inc/ajax-functions.php`
2. Inizia a digitare `wp_enqueue_` 
3. Dovrebbe apparire l'autocompletamento
4. Non dovrebbero più apparire errori "Undefined function"

### Test WordPress Constants
```php
// Queste costanti ora sono riconosciute:
WP_DEBUG
ABSPATH
WP_CONTENT_DIR
MARCELLO_THEME_DIR
```

### Test WordPress Functions
```php
// Queste funzioni ora hanno autocompletamento:
current_user_can('manage_options')
wp_enqueue_style('handle', 'style.css')
get_template_directory()
add_action('wp_head', 'callback')
```

---

## 🎨 WordPress Coding Standards

### Regole Attive
- **WordPress Core** standards
- **WordPress Extra** standards  
- **WordPress Docs** standards
- **PHP Compatibility** (7.4+)

### Prefissi Tema
- `marcello_scavo_*` per funzioni
- `MARCELLO_SCAVO_*` per costanti
- `MarcelloScavo*` per classi

### Esclusioni
- File vendor/ ignorati
- CSS minificati ignorati  
- Backup ignorati
- Sintassi array breve permessa

---

## 🔧 Risoluzione Problemi

### IntelliSense non funziona?
1. **Riavvia VS Code**
2. **Verifica estensione IntelliSense installata**
3. **Controlla paths in settings.json**
4. **Esegui:** `composer install --dev`

### Errori di memoria con PHPCS?
```bash
# Aumenta memoria
./vendor/bin/phpcs -d memory_limit=512M
```

### WordPress Stubs non trovati?
```bash
# Reinstalla dipendenze
cd wp-content/themes/marcello-scavo-tattoo
composer install --dev
```

---

## 📚 Estensioni VS Code Consigliate

### Installate Automaticamente
- **IntelliSense for PHP** (bmewburn.vscode-intelephense-client)
- **PHP Debug** (xdebug.php-debug)
- **Error Lens** (usernamehw.errorlens)
- **Docker** (ms-vscode.vscode-docker)

### Configurazione Completata
```json
{
    "recommendations": [
        "bmewburn.vscode-intelephense-client",
        "xdebug.php-debug",
        "usernamehw.errorlens",
        "ms-vscode.vscode-docker"
    ]
}
```

---

## 🎉 Risultato

**✅ IntelliSense WordPress completamente funzionante**
- Nessun errore falso positivo
- Autocompletamento completo delle funzioni WordPress
- Documentazione inline per tutte le funzioni
- Rilevamento errori accurato
- Sviluppo molto più fluido e professionale

**🚀 Pronto per lo sviluppo avanzato del tema!**