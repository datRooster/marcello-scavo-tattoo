#!/bin/bash

# Marcello Scavo Tattoo Theme - Development Helper Scripts
# Usage: ./dev-tools.sh [command]

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Theme directory
THEME_DIR="/Users/thatrooster/Sites/localhost/wordpress-dev/wp-content/themes/marcello-scavo-tattoo"

echo -e "${BLUE}🎨 Marcello Scavo Tattoo Theme - Development Tools${NC}"
echo -e "${PURPLE}=================================================${NC}"

# Function to check if in theme directory
check_theme_dir() {
    if [ ! -d "$THEME_DIR" ]; then
        echo -e "${RED}❌ Theme directory not found: $THEME_DIR${NC}"
        exit 1
    fi
    cd "$THEME_DIR"
}

# Function to run PHP CodeSniffer
run_phpcs() {
    echo -e "\n${YELLOW}🔍 Running PHP CodeSniffer (WordPress Standards)...${NC}"
    if [ -f "vendor/bin/phpcs" ]; then
        ./vendor/bin/phpcs --standard=phpcs.xml --report=summary .
        echo -e "${GREEN}✅ PHP CodeSniffer completed${NC}"
    else
        echo -e "${RED}❌ PHP CodeSniffer not found. Run: composer install --dev${NC}"
    fi
}

# Function to fix PHP CodeSniffer issues
run_phpcbf() {
    echo -e "\n${YELLOW}🔧 Running PHP Code Beautifier and Fixer...${NC}"
    if [ -f "vendor/bin/phpcbf" ]; then
        ./vendor/bin/phpcbf --standard=phpcs.xml .
        echo -e "${GREEN}✅ PHP Code Beautifier completed${NC}"
    else
        echo -e "${RED}❌ PHP Code Beautifier not found. Run: composer install --dev${NC}"
    fi
}

# Function to check PHP compatibility
check_php_compatibility() {
    echo -e "\n${YELLOW}🐘 Checking PHP Compatibility (7.4+)...${NC}"
    if [ -f "vendor/bin/phpcs" ]; then
        ./vendor/bin/phpcs --standard=PHPCompatibilityWP --runtime-set testVersion 7.4- .
        echo -e "${GREEN}✅ PHP Compatibility check completed${NC}"
    else
        echo -e "${RED}❌ PHP CodeSniffer not found. Run: composer install --dev${NC}"
    fi
}

# Function to generate minified CSS
minify_css() {
    echo -e "\n${YELLOW}📦 Minifying CSS files...${NC}"
    
    # Create min directory if it doesn't exist
    mkdir -p assets/css/min
    
    # Minify main stylesheet if exists
    if [ -f "style.css" ]; then
        echo -e "${CYAN}🔄 Minifying style.css...${NC}"
        # Simple CSS minification (remove comments and whitespace)
        sed -e 's/\/\*[^*]*\*\+\([^/][^*]*\*\+\)*\///g' \
            -e 's/^\s*//g' \
            -e 's/\s*$//g' \
            -e '/^$/d' \
            -e 's/\s*{\s*/{/g' \
            -e 's/\s*}\s*/}/g' \
            -e 's/\s*;\s*/;/g' \
            -e 's/\s*:\s*/:/g' \
            -e 's/\s*,\s*/,/g' \
            style.css > assets/css/min/style.min.css
        echo -e "${GREEN}✅ style.css minified${NC}"
    fi
    
    # Minify other CSS files
    for css_file in assets/css/*.css; do
        if [ -f "$css_file" ] && [[ "$css_file" != *"/min/"* ]]; then
            filename=$(basename "$css_file" .css)
            echo -e "${CYAN}🔄 Minifying $filename.css...${NC}"
            sed -e 's/\/\*[^*]*\*\+\([^/][^*]*\*\+\)*\///g' \
                -e 's/^\s*//g' \
                -e 's/\s*$//g' \
                -e '/^$/d' \
                -e 's/\s*{\s*/{/g' \
                -e 's/\s*}\s*/}/g' \
                -e 's/\s*;\s*/;/g' \
                -e 's/\s*:\s*/:/g' \
                -e 's/\s*,\s*/,/g' \
                "$css_file" > "assets/css/min/$filename.min.css"
            echo -e "${GREEN}✅ $filename.css minified${NC}"
        fi
    done
    
    echo -e "${GREEN}✅ CSS minification completed${NC}"
}

# Function to generate development report
generate_report() {
    echo -e "\n${YELLOW}📊 Generating Development Report...${NC}"
    
    REPORT_FILE="development-report.md"
    
    echo "# Development Report - $(date)" > $REPORT_FILE
    echo "## Marcello Scavo Tattoo Theme" >> $REPORT_FILE
    echo "" >> $REPORT_FILE
    
    # File count
    echo "### 📁 File Statistics" >> $REPORT_FILE
    echo "- PHP Files: $(find . -name '*.php' -not -path './vendor/*' | wc -l)" >> $REPORT_FILE
    echo "- CSS Files: $(find . -name '*.css' -not -path './vendor/*' | wc -l)" >> $REPORT_FILE
    echo "- JS Files: $(find . -name '*.js' -not -path './vendor/*' | wc -l)" >> $REPORT_FILE
    echo "" >> $REPORT_FILE
    
    # Theme structure
    echo "### 🏗️ Theme Structure" >> $REPORT_FILE
    echo '```' >> $REPORT_FILE
    find . -type f -name '*.php' -not -path './vendor/*' -not -path './backup/*' | head -20 >> $REPORT_FILE
    echo '```' >> $REPORT_FILE
    echo "" >> $REPORT_FILE
    
    # Performance metrics
    echo "### ⚡ Performance Metrics" >> $REPORT_FILE
    echo "- Functions.php size: $(wc -l functions.php | awk '{print $1}') lines" >> $REPORT_FILE
    echo "- Main CSS size: $(du -h style.css | awk '{print $1}')" >> $REPORT_FILE
    echo "- Vendor dependencies: $(du -h vendor/ | tail -1 | awk '{print $1}')" >> $REPORT_FILE
    echo "" >> $REPORT_FILE
    
    echo -e "${GREEN}✅ Report generated: $REPORT_FILE${NC}"
}

# Function to backup theme
backup_theme() {
    echo -e "\n${YELLOW}💾 Creating theme backup...${NC}"
    
    BACKUP_DIR="../backups"
    BACKUP_NAME="marcello-scavo-tattoo-$(date +%Y%m%d-%H%M%S).tar.gz"
    
    mkdir -p "$BACKUP_DIR"
    
    tar --exclude='./vendor' \
        --exclude='./node_modules' \
        --exclude='./assets/css/min' \
        --exclude='./.git' \
        -czf "$BACKUP_DIR/$BACKUP_NAME" .
    
    echo -e "${GREEN}✅ Backup created: $BACKUP_DIR/$BACKUP_NAME${NC}"
}

# Function to setup development environment
setup_dev() {
    echo -e "\n${YELLOW}🚀 Setting up development environment...${NC}"
    
    # Install composer dependencies
    if [ ! -d "vendor" ]; then
        echo -e "${CYAN}📦 Installing Composer dependencies...${NC}"
        composer install --dev
    fi
    
    # Create necessary directories
    echo -e "${CYAN}📁 Creating directories...${NC}"
    mkdir -p assets/css/min
    mkdir -p assets/js/min
    mkdir -p backup
    mkdir -p docs
    
    # Set permissions
    echo -e "${CYAN}🔐 Setting permissions...${NC}"
    chmod -R 755 assets/
    chmod 644 *.php
    chmod 644 *.css
    
    echo -e "${GREEN}✅ Development environment setup completed${NC}"
}

# Function to show help
show_help() {
    echo -e "\n${CYAN}Available commands:${NC}"
    echo -e "  ${GREEN}lint${NC}         - Run PHP CodeSniffer"
    echo -e "  ${GREEN}fix${NC}          - Run PHP Code Beautifier and Fixer"
    echo -e "  ${GREEN}compat${NC}       - Check PHP compatibility"
    echo -e "  ${GREEN}minify${NC}       - Minify CSS files"
    echo -e "  ${GREEN}report${NC}       - Generate development report"
    echo -e "  ${GREEN}backup${NC}       - Create theme backup"
    echo -e "  ${GREEN}setup${NC}        - Setup development environment"
    echo -e "  ${GREEN}help${NC}         - Show this help"
    echo ""
}

# Main execution
check_theme_dir

case "${1:-help}" in
    "lint")
        run_phpcs
        ;;
    "fix")
        run_phpcbf
        ;;
    "compat")
        check_php_compatibility
        ;;
    "minify")
        minify_css
        ;;
    "report")
        generate_report
        ;;
    "backup")
        backup_theme
        ;;
    "setup")
        setup_dev
        ;;
    "help"|*)
        show_help
        ;;
esac

echo -e "\n${PURPLE}🎨 Development task completed!${NC}"