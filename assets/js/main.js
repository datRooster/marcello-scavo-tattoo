/* FORCED UPDATE $(date +%s) */
/**
 * Marcello Scavo Tattoo Theme JavaScript
 * 
 * @package MarcelloScavoTattoo
 * @version 1.0.0
 */

// Funzione di debug per mostrare tutti gli elementi con data-translatable
function debugTranslatableElements() {
    console.log('=== DEBUG: ELEMENTI TRADUCIBILI ===');
    const elements = document.querySelectorAll('[data-translatable="true"]');
    console.log(`Trovati ${elements.length} elementi traducibili:`);
    
    elements.forEach((el, index) => {
        console.log(`${index + 1}. Elemento:`, {
            tag: el.tagName,
            text: el.textContent.trim(),
            originalText: el.getAttribute('data-original-text'),
            innerHTML: el.innerHTML.substring(0, 100) + (el.innerHTML.length > 100 ? '...' : ''),
            element: el
        });
    });
    
    return elements;
}

// Esponiamo la funzione globalmente per debug
window.debugTranslatableElements = debugTranslatableElements;

// Global function for applying language changes - MUST BE OUTSIDE IIFE
// Sistema di traduzione con Google Translate + Cache
let translationCache = new Map();

// Assicurati che ajaxurl sia definito
if (typeof ajaxurl === 'undefined') {
    window.ajaxurl = '/wp-admin/admin-ajax.php';
}

// Funzione per tradurre multiple stringhe in una sola richiesta (BATCH)
async function translateBatch(texts, targetLang) {
    try {
        console.log('🌐 Calling BATCH Translation:', texts.length, 'texts →', targetLang);
        console.log('🌐 Texts to translate:', texts);
        
        const formData = new FormData();
        formData.append('action', 'batch_translate');
        formData.append('target_lang', targetLang);
        formData.append('source_lang', 'it');
        
        // Aggiungi tutti i testi come array
        texts.forEach((text, index) => {
            formData.append(`texts[${index}]`, text);
        });
        
        const response = await fetch(ajaxurl, {
            method: 'POST',
            body: formData
        });
        
        console.log('🌐 BATCH Response status:', response.status);
        const data = await response.json();
        console.log('🌐 BATCH Response data:', data);
        
        if (data.success) {
            const translations = data.data.translations;
            
            // Aggiungi tutte le traduzioni alla cache locale
            Object.entries(translations).forEach(([original, translated]) => {
                const cacheKey = `${original}_${targetLang}`;
                translationCache.set(cacheKey, translated);
            });
            
            console.log('✅ BATCH Translations received:', translations);
            return translations;
        } else {
            console.error('❌ BATCH Translation failed:', data);
            return null;
        }
    } catch (error) {
        console.error('❌ BATCH Translation error:', error);
        return null;
    }
}

function applyLanguageChanges(langCode) {
    console.log('=== Apply Language Changes Started ===');
    console.log('Target language:', langCode);
    
    // SISTEMA DI TRADUZIONE TEMPORANEAMENTE DISABILITATO
    console.log('🚧 Translation system temporarily disabled for development');
    
    // Mostra notifica per informare l'utente
    showLanguageChangeNotification('Sistema di traduzione in sviluppo - Coming Soon!');
    
    return; // Esci dalla funzione senza applicare traduzioni
    
    // CONTROLLO: Se siamo nel Customizer, non applicare traduzioni automatiche
    if (window.location.href.includes('customize.php') || parent.window.location.href.includes('customize.php')) {
        console.log('🚫 CUSTOMIZER DETECTED - Skipping automatic translations');
        return;
    }
    
    const translatableElements = document.querySelectorAll('[data-translatable="true"]');
    console.log('Found translatable elements:', translatableElements.length);
    
    if (langCode === 'it') {
        // Ripristina italiano originale
        translatableElements.forEach(element => {
            const originalText = element.getAttribute('data-original-text');
            if (originalText) {
                // Normalizza il testo originale rimuovendo gli escape chars
                const cleanOriginalText = originalText.replace(/\\'/g, "'").replace(/\\"/g, '"');
                
                if (originalText.includes('<br>')) {
                    element.innerHTML = cleanOriginalText;
                } else {
                    element.textContent = cleanOriginalText;
                }
                console.log('🇮🇹 Restored Italian text:', cleanOriginalText);
            }
        });
        console.log('🇮🇹 Restored Italian text');
        return;
    }
    
    // Raccoglie tutti i testi da tradurre
    const textsToTranslate = [];
    const elementsMap = new Map();
    
    translatableElements.forEach(element => {
        let originalText = element.getAttribute('data-original-text');
        
        // Se non ha data-original-text, usa il testo attuale pulito
        if (!originalText) {
            originalText = element.textContent.trim();
            element.setAttribute('data-original-text', originalText);
            console.log('🔧 Set missing data-original-text:', originalText);
        }
        
        if (originalText) {
            console.log('🔍 Found element with text:', originalText);
            console.log('🔍 Element details:', {
                tagName: element.tagName,
                textContent: element.textContent.trim(),
                innerHTML: element.innerHTML,
                attributes: Array.from(element.attributes).map(attr => `${attr.name}="${attr.value}"`).join(' ')
            });
            
            // Controlla cache locale prima
            const cacheKey = `${originalText}_${langCode}`;
            if (translationCache.has(cacheKey)) {
                const cachedTranslation = translationCache.get(cacheKey);
                if (originalText.includes('<br>')) {
                    element.innerHTML = cachedTranslation;
                } else {
                    element.textContent = cachedTranslation;
                }
                console.log('📋 Using cached translation:', originalText, '→', cachedTranslation);
            } else {
                // Aggiungi alla lista per traduzione batch
                if (!textsToTranslate.includes(originalText)) {
                    textsToTranslate.push(originalText);
                    console.log('📝 Added to translation queue:', originalText);
                }
                
                // Mappa elemento per aggiornamento successivo
                if (!elementsMap.has(originalText)) {
                    elementsMap.set(originalText, []);
                }
                elementsMap.get(originalText).push(element);
            }
        } else {
            console.warn('⚠️ Element without text content:', element);
        }
    });
    
    if (textsToTranslate.length === 0) {
        console.log('📋 All translations found in cache!');
        return;
    }
    
    console.log('🌐 Need to translate:', textsToTranslate.length, 'unique texts');
    
    // Mostra loading indicator
    document.body.classList.add('translating');
    
    // Traduci tutti i testi in una sola richiesta
    translateBatch(textsToTranslate, langCode).then(translations => {
        console.log('🎯 Received translations:', translations);
        
        if (translations) {
            // Applica le traduzioni agli elementi
            Object.entries(translations).forEach(([originalText, translatedText]) => {
                let elements = elementsMap.get(originalText);
                console.log(`🔄 Processing: "${originalText}" → "${translatedText}"`);
                console.log('📍 Elements to update:', elements?.length || 0);
                
                // Se non trova elementi con il testo esatto, prova una ricerca fuzzy
                if (!elements || elements.length === 0) {
                    console.log('🔍 Searching for elements with similar text...');
                    const allTranslatableElements = document.querySelectorAll('[data-translatable="true"]');
                    const matchingElements = [];
                    
                    // Normalizza il testo di ricerca (rimuovi escape chars)
                    const normalizedOriginal = originalText.replace(/\\'/g, "'").replace(/\\"/g, '"');
                    
                    allTranslatableElements.forEach(el => {
                        const elText = (el.getAttribute('data-original-text') || el.textContent).trim();
                        const normalizedElText = elText.replace(/\\'/g, "'").replace(/\\"/g, '"');
                        
                        // Cerca match esatti o contenuti parziali con testi normalizzati
                        if (normalizedElText === normalizedOriginal || 
                            elText === originalText ||
                            normalizedElText.includes(normalizedOriginal) || 
                            normalizedOriginal.includes(normalizedElText) ||
                            normalizedElText.replace(/\s+/g, ' ') === normalizedOriginal.replace(/\s+/g, ' ')) {
                            matchingElements.push(el);
                            console.log('🎯 Found matching element:', el, 'with text:', elText);
                            console.log('🎯 Normalized match:', normalizedElText, '===', normalizedOriginal);
                        }
                    });
                    
                    if (matchingElements.length > 0) {
                        elements = matchingElements;
                        console.log('✅ Found alternative matches:', matchingElements.length);
                    }
                }
                
                if (elements && elements.length > 0) {
                    elements.forEach((element, index) => {
                        console.log(`📝 Updating element ${index + 1}:`, element);
                        
                        if (originalText.includes('<br>')) {
                            element.innerHTML = translatedText;
                            console.log('✅ Updated innerHTML:', translatedText);
                        } else {
                            element.textContent = translatedText;
                            console.log('✅ Updated textContent:', translatedText);
                        }
                        
                        // Aggiorna anche l'attributo data-original-text con la traduzione
                        element.setAttribute('data-original-text', originalText);
                    });
                    console.log('✅ Updated elements for:', originalText, '→', translatedText);
                } else {
                    console.warn('⚠️ No elements found for text:', originalText);
                    console.warn('🔍 Available elements map keys:', Array.from(elementsMap.keys()));
                    console.warn('🔍 Original text normalized:', originalText.replace(/\\'/g, "'").replace(/\\"/g, '"'));
                    
                    // Debug: mostra tutti gli elementi disponibili
                    const allElements = document.querySelectorAll('[data-translatable="true"]');
                    console.warn('🔍 All translatable elements on page:');
                    allElements.forEach((el, i) => {
                        const elText = (el.getAttribute('data-original-text') || el.textContent).trim();
                        console.warn(`  ${i+1}. "${elText}"`);
                    });
                }
            });
            
            console.log('🎉 All batch translations applied!');
        } else {
            console.error('❌ Batch translation failed');
        }
        
        // Rimuovi loading indicator
        document.body.classList.remove('translating');
    }).catch(error => {
        console.error('❌ Batch translation error:', error);
        document.body.classList.remove('translating');
    });
}

// DEBUG: Funzione globale per testare le traduzioni manualmente
window.testTranslation = function(lang) {
    console.log('🧪 Manual test translation to:', lang);
    applyLanguageChanges(lang);
};

// DEBUG: Esponi le funzioni per debugging
window.applyLanguageChanges = applyLanguageChanges;
window.translateBatch = translateBatch;

/**
 * Manual Language Change Function - GLOBAL
 * This function is called when no multilingual plugin is available
 */

// Add event listener for language select when DOM is ready - DISABILITATO
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOM ContentLoaded - Translation system disabled');
    
    // Sistema di traduzione temporaneamente disabilitato
    // L'element language-select è stato rimosso dal frontend
    console.log('� Language switcher removed from frontend during development');
});

// Also try with window.onload as backup - DISABILITATO
window.addEventListener('load', function() {
    console.log('Window loaded - translation system disabled');
    // Sistema di traduzione temporaneamente disabilitato
});

(function($) {
    'use strict';

    // Suppress jQuery Migrate warnings in production
    if (typeof jQuery.migrateWarnings !== 'undefined') {
        jQuery.migrateWarnings = [];
    }

    // DOM Ready
    $(document).ready(function() {
        initTheme();
    });

    // Window Load
    $(window).on('load', function() {
        hideLoadingOverlay();
        initAnimations();
    });

    /**
     * Initialize Theme
     */
    function initTheme() {
        initMobileMenu();
        initSmoothScrolling();
        initPortfolioFilter();
        initBooklyIntegration();
        initContactForm();
        initNewsletterForm();
        initLoadingStates();
        initScrollEffects();
        initLazyLoading();
        initImageViewerModal();
        initMapInteractionFix(); // Nuovo fix per la mappa
        initLanguageSwitcher(); // Sistema di cambio lingua
        initPortfolioTextVisibility(); // Forza visibilità testo portfolio
    }

    /**
     * Force Portfolio Text Visibility
     */
    function initPortfolioTextVisibility() {
        // Forza la visibilità del testo descrittivo portfolio
        function forcePortfolioTextVisibility() {
            // Forza portfolio subtitle
            const portfolioSubtitle = $('.portfolio-section .section-subtitle span');
            if (portfolioSubtitle.length > 0) {
                portfolioSubtitle.css({
                    'color': '#ffffff !important',
                    'background-color': 'rgba(0,0,0,0.85) !important',
                    'text-shadow': '4px 4px 8px rgba(0,0,0,1) !important',
                    'padding': '1.2rem 2.5rem !important',
                    'border-radius': '15px !important',
                    'display': 'inline-block !important',
                    'font-weight': '700 !important',
                    'font-size': '1.3rem !important',
                    'border': '3px solid rgba(255,255,255,0.4) !important',
                    'box-shadow': '0 6px 20px rgba(0,0,0,0.7) !important',
                    'backdrop-filter': 'blur(15px) !important',
                    '-webkit-backdrop-filter': 'blur(15px) !important'
                });
                console.log('Portfolio subtitle forced visible:', portfolioSubtitle.text());
            }
            
            // Forza gallery subtitle (WIDGET)
            const gallerySubtitle = $('.gallery-subtitle, .widget_marcello_gallery_showcase .gallery-subtitle');
            if (gallerySubtitle.length > 0) {
                gallerySubtitle.css({
                    'color': '#ffffff !important',
                    'background-color': 'transparent !important', // NESSUNO sfondo
                    'text-shadow': '2px 2px 6px rgba(0,0,0,1), 0 0 10px rgba(0,0,0,0.8) !important', // Ombra doppia
                    'padding': '0.5rem 1rem !important', // Padding ridotto
                    'border-radius': '0 !important', // Nessun bordo arrotondato
                    'display': 'inline-block !important',
                    'font-weight': '400 !important', // Peso normale
                    'font-size': '0.95rem !important', // Testo più piccolo
                    'border': 'none !important', // Nessun bordo
                    'box-shadow': 'none !important', // Nessuna ombra del box
                    'backdrop-filter': 'none !important', // Nessun filtro
                    '-webkit-backdrop-filter': 'none !important',
                    'opacity': '1 !important'
                });
                console.log('Gallery subtitle forced visible:', gallerySubtitle.text());
            }
        }
        
        // Applica immediatamente
        forcePortfolioTextVisibility();
        
        // Riapplica dopo 500ms per sicurezza
        setTimeout(forcePortfolioTextVisibility, 500);
        
        // Riapplica quando la finestra è completamente caricata
        $(window).on('load', forcePortfolioTextVisibility);
    }

    /**
     * Hide Loading Overlay
     */
    function hideLoadingOverlay() {
        $('#loading-overlay').fadeOut(500);
    }

    /**
     * Mobile Menu
     */
    function initMobileMenu() {
        // Alternative selectors in case the default ones don't work
        const toggleSelector = '.menu-toggle';
        const menuSelector = '#primary-menu, .primary-menu';
        
        $(toggleSelector).on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const $this = $(this);
            const $menu = $(menuSelector).first(); // Get the first matching menu
            
            $this.toggleClass('active');
            $menu.toggleClass('active');
            $('body').toggleClass('menu-open');
            
            // Force display for better browser compatibility
            if ($this.hasClass('active')) {
                $menu.css({
                    'transform': 'translateX(-50%) translateY(0)',
                    'opacity': '1',
                    'visibility': 'visible',
                    'display': 'flex'
                });
            } else {
                $menu.css({
                    'transform': 'translateX(-50%) translateY(-20px)',
                    'opacity': '0',
                    'visibility': 'hidden',
                    'display': 'none'
                });
            }
            
            // Update aria-expanded
            const expanded = $this.attr('aria-expanded') === 'true';
            $this.attr('aria-expanded', !expanded);
        });

        // Close menu on resize
        $(window).on('resize', function() {
            if ($(window).width() > 1024) { // Updated breakpoint
                $(toggleSelector).removeClass('active');
                $(menuSelector).removeClass('active');
                $('body').removeClass('menu-open');
            }
        });

        // Close menu when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.main-navigation').length) {
                $(toggleSelector).removeClass('active');
                $(menuSelector).removeClass('active');
                $('body').removeClass('menu-open');
                // Reset inline styles
                $(menuSelector).css({
                    'transform': '',
                    'opacity': '',
                    'visibility': '',
                    'display': ''
                });
            }
        });
        
        // Close menu when clicking on menu links
        $('.main-navigation a').on('click', function() {
            $(toggleSelector).removeClass('active');
            $(menuSelector).removeClass('active');
            $('body').removeClass('menu-open');
            // Reset inline styles
            $(menuSelector).css({
                'transform': '',
                'opacity': '',
                'visibility': '',
                'display': ''
            });
        });
    }

    /**
     * Smooth Scrolling
     */
    function initSmoothScrolling() {
        $('a[href*="#"]:not([href="#"])').on('click', function() {
            if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && location.hostname === this.hostname) {
                let target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                
                if (target.length) {
                    const headerHeight = $('.site-header').outerHeight();
                    $('html, body').animate({
                        scrollTop: target.offset().top - headerHeight - 20
                    }, 800);
                    
                    // Close mobile menu if open
                    $('.menu-toggle').removeClass('active');
                    $('#primary-menu').removeClass('active');
                    $('body').removeClass('menu-open');
                    
                    return false;
                }
            }
        });
    }

    /**
     * Portfolio Filter
     */
    function initPortfolioFilter() {
        $('.filter-btn').on('click', function() {
            const filter = $(this).data('filter');
            
            // Update active state
            $('.filter-btn').removeClass('active');
            $(this).addClass('active');
            
            // Filter portfolio items
            if (filter === 'all') {
                $('.portfolio-item').fadeIn(400);
            } else {
                $('.portfolio-item').hide();
                $('.portfolio-item[data-category="' + filter + '"]').fadeIn(400);
            }
        });
    }

    /**
     * Open image viewer modal
     */
    function openImageViewer(imageSrc, title, caption) {
        console.log('Opening image viewer with:', { imageSrc, title, caption });
        
        const $imageViewerModal = $('#image-viewer-modal');
        const $imageViewerImg = $('#image-viewer-img');
        const $imageViewerTitle = $('#image-viewer-title');
        const $imageViewerDescription = $('#image-viewer-description');
        
        console.log('Modal elements found:', {
            modal: $imageViewerModal.length,
            img: $imageViewerImg.length,
            title: $imageViewerTitle.length,
            description: $imageViewerDescription.length
        });
        
        if ($imageViewerModal.length === 0) {
            console.error('Image viewer modal not found in DOM!');
            return;
        }
        
        // Set image and info
        $imageViewerImg.attr('src', imageSrc);
        $imageViewerImg.attr('alt', title || 'Immagine');
        $imageViewerTitle.text(title || '');
        $imageViewerDescription.text(caption || '');
        
        // Show modal
        $imageViewerModal.addClass('show');
        $('body').addClass('modal-open');
        
        console.log('Image viewer modal opened successfully');
        console.log('Modal classes:', $imageViewerModal[0].className);
    }

    /**
     * Initialize image viewer modal
     */
    function initImageViewerModal() {
        const $imageViewerModal = $('#image-viewer-modal');
        
        // Close button
        $('.image-viewer-close').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            closeImageViewer();
        });
        
        // Click outside to close
        $imageViewerModal.on('click', function(e) {
            if (e.target === this) {
                closeImageViewer();
            }
        });
        
        // ESC key to close
        $(document).on('keyup.imageViewer', function(e) {
            if (e.keyCode === 27 && $imageViewerModal.hasClass('show')) {
                closeImageViewer();
            }
        });
    }

    /**
     * Close image viewer modal
     */
    function closeImageViewer() {
        const $imageViewerModal = $('#image-viewer-modal');
        $imageViewerModal.removeClass('show');
        $('body').removeClass('modal-open');
        
        console.log('Image viewer modal closed');
    }

    /**
     * Open Image Modal for full-size viewing
     */
    function openImageModal(src, alt, title, description) {
        const imageModal = $(`
            <div class="image-modal-overlay">
                <div class="image-modal-content">
                    <div class="image-modal-header">
                        <h3>${title || alt}</h3>
                        <button class="image-modal-close">&times;</button>
                    </div>
                    <div class="image-modal-body">
                        <img src="${src}" alt="${alt}" class="full-size-image">
                        ${description ? `<p class="image-description">${description}</p>` : ''}
                    </div>
                </div>
            </div>
        `);

        $('body').append(imageModal).addClass('image-modal-open');
        
        // Fade in effect
        setTimeout(function() {
            imageModal.addClass('active');
        }, 50);

        // Close modal handlers
        imageModal.find('.image-modal-close').on('click', function() {
            closeImageModal(imageModal);
        });

        imageModal.on('click', function(e) {
            if (e.target === this) {
                closeImageModal(imageModal);
            }
        });

        // ESC key handler
        $(document).on('keyup.imageModal', function(e) {
            if (e.keyCode === 27) {
                closeImageModal(imageModal);
            }
        });
    }

    /**
     * Close Image Modal
     */
    function closeImageModal($modal) {
        $modal.removeClass('active');
        $('body').removeClass('image-modal-open');
        
        setTimeout(function() {
            $modal.remove();
        }, 300);
        
        $(document).off('keyup.imageModal');
    }

    /**
     * Bookly Integration Enhancement
     */
    function initBooklyIntegration() {
        // Enhanced Bookly form styling and functionality
        
        // Wait for Bookly to load and then enhance it
        const checkBookly = setInterval(function() {
            if ($('.bookly-form, .bookly-booking-form').length) {
                clearInterval(checkBookly);
                enhanceBooklyForm();
            }
        }, 500);
        
        function enhanceBooklyForm() {
            const $booklyContainer = $('.bookly-form, .bookly-booking-form').closest('.bookly-integration-container');
            
            // Add loading animation when Bookly form is submitted
            $(document).on('click', '.bookly-next-step, .bookly-form button[type="submit"]', function() {
                const $btn = $(this);
                const originalText = $btn.text();
                
                $btn.addClass('loading').prop('disabled', true);
                $btn.html('<i class="fas fa-spinner fa-spin"></i> ' + 'Caricamento...');
                
                // Re-enable after a delay (Bookly will handle the actual state)
                setTimeout(function() {
                    $btn.removeClass('loading').prop('disabled', false);
                    $btn.html(originalText);
                }, 3000);
            });
            
            // Track Bookly interactions for analytics
            $(document).on('click', '.bookly-service', function() {
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'bookly_service_selected', {
                        'event_category': 'engagement',
                        'event_label': 'booking_flow'
                    });
                }
            });
            
            $(document).on('change', '.bookly-calendar input', function() {
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'bookly_date_selected', {
                        'event_category': 'engagement',
                        'event_label': 'booking_flow'
                    });
                }
            });
            
            $(document).on('click', '.bookly-time-slot', function() {
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'bookly_time_selected', {
                        'event_category': 'engagement',
                        'event_label': 'booking_flow'
                    });
                }
            });
            
            // Enhance Bookly notifications
            $(document).on('bookly.booking.success', function() {
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'booking_completed', {
                        'event_category': 'conversion',
                        'event_label': 'bookly_booking'
                    });
                }
                
                // Show success animation
                $booklyContainer.addClass('booking-success');
                
                // Auto-scroll to success message
                setTimeout(function() {
                    $('html, body').animate({
                        scrollTop: $booklyContainer.offset().top - 100
                    }, 800);
                }, 500);
            });
            
            // Handle Bookly errors
            $(document).on('bookly.booking.error', function() {
                $booklyContainer.addClass('booking-error');
                
                setTimeout(function() {
                    $booklyContainer.removeClass('booking-error');
                }, 5000);
            });
            
            // Enhance accessibility
            $('.bookly-form input, .bookly-form select, .bookly-form textarea').each(function() {
                const $field = $(this);
                const label = $field.closest('.bookly-form-group').find('label').text();
                
                if (label && !$field.attr('aria-label')) {
                    $field.attr('aria-label', label);
                }
            });
        }
        
        // Fallback contact methods enhancement
        $('.contact-method a[href^="tel:"]').on('click', function() {
            if (typeof gtag !== 'undefined') {
                gtag('event', 'phone_click', {
                    'event_category': 'engagement',
                    'event_label': 'alternative_contact'
                });
            }
        });
        
        $('.contact-method a[href^="mailto:"]').on('click', function() {
            if (typeof gtag !== 'undefined') {
                gtag('event', 'email_click', {
                    'event_category': 'engagement',
                    'event_label': 'alternative_contact'
                });
            }
        });
    }

    /**
     * Contact Form
     */
    function initContactForm() {
        // Basic contact form functionality
        $('#contact-form').on('submit', function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const $button = $form.find('button[type="submit"]');
            
            // Show loading state
            $button.addClass('loading').prop('disabled', true);
            $button.html('<i class="fas fa-spinner fa-spin"></i> Invio...');
            
            // Simulate form submission (integrate with your contact form handler)
            setTimeout(function() {
                $button.removeClass('loading').prop('disabled', false);
                $button.html('<i class="fas fa-paper-plane"></i> Invia Messaggio');
                showNotification('Messaggio inviato con successo!', 'success');
                
                // Track contact form submission
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'contact_form_submit', {
                        'event_category': 'engagement',
                        'event_label': 'contact_form'
                    });
                }
            }, 1000);
        });
    }

    /**
     * Newsletter Form
     */
    function initNewsletterForm() {
        $('#newsletter-form').on('submit', function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const $email = $form.find('input[name="newsletter_email"]');
            const $button = $form.find('button[type="submit"]');
            
            // Basic email validation
            const email = $email.val().trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (!email || !emailRegex.test(email)) {
                $email.addClass('error');
                showNotification('Inserisci un indirizzo email valido', 'error');
                return;
            }
            
            $email.removeClass('error');
            
            // Show loading state
            $button.addClass('loading').prop('disabled', true);
            $button.html('<i class="fas fa-spinner fa-spin"></i>');
            
            // Simulate newsletter subscription (integrate with your email service)
            setTimeout(function() {
                $button.removeClass('loading').prop('disabled', false);
                $button.html('<i class="fas fa-paper-plane"></i>');
                $form[0].reset();
                showNotification('Iscrizione completata con successo!', 'success');
                
                // Track newsletter signup
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'newsletter_signup', {
                        'event_category': 'engagement',
                        'event_label': 'footer_newsletter'
                    });
                }
            }, 1000);
        });
    }

    /**
     * Loading States
     */
    function initLoadingStates() {
        // Add loading state to external links
        $('a[href^="http"]:not([href*="' + location.hostname + '"])').on('click', function() {
            $(this).addClass('loading');
        });
        
        // Add loading state to form submissions
        $('form:not(#newsletter-form)').on('submit', function() {
            const $btn = $(this).find('button[type="submit"], input[type="submit"]');
            $btn.addClass('loading').prop('disabled', true);
        });
    }

    /**
     * Scroll Effects
     */
    function initScrollEffects() {
        let lastScrollTop = 0;
        const $header = $('.site-header');
        
        $(window).on('scroll', function() {
            const scrollTop = $(this).scrollTop();
            
            // Header hide/show on scroll
            if (scrollTop > lastScrollTop && scrollTop > 100) {
                // Scrolling down
                $header.addClass('header-hidden');
            } else {
                // Scrolling up
                $header.removeClass('header-hidden');
            }
            
            // Add scrolled class for styling
            if (scrollTop > 50) {
                $header.addClass('scrolled');
            } else {
                $header.removeClass('scrolled');
            }
            
            lastScrollTop = scrollTop;
        });
    }

    /**
     * Initialize Animations
     */
    function initAnimations() {
        // Intersection Observer for fade-in animations
        if ('IntersectionObserver' in window) {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animated');
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);
            
            // Observe all fade-in-up elements
            document.querySelectorAll('.fade-in-up').forEach(function(el) {
                observer.observe(el);
            });
        } else {
            // Fallback for older browsers
            $('.fade-in-up').addClass('animated');
        }
    }

    /**
     * Lazy Loading
     */
    function initLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        img.classList.add('loaded');
                        imageObserver.unobserve(img);
                    }
                });
            });
            
            document.querySelectorAll('img[data-src]').forEach(function(img) {
                imageObserver.observe(img);
            });
        }
    }

    /**
     * Show Notification
     */
    function showNotification(message, type = 'info') {
        const notification = $('<div class="notification notification-' + type + '">' +
            '<i class="fas fa-' + (type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle') + '"></i>' +
            '<span>' + message + '</span>' +
            '<button class="notification-close"><i class="fas fa-times"></i></button>' +
            '</div>');
        
        $('body').append(notification);
        
        // Show notification
        setTimeout(function() {
            notification.addClass('show');
        }, 100);
        
        // Auto hide after 5 seconds
        setTimeout(function() {
            hideNotification(notification);
        }, 5000);
        
        // Close button handler
        notification.find('.notification-close').on('click', function() {
            hideNotification(notification);
        });
    }

    /**
     * Hide Notification
     */
    function hideNotification($notification) {
        $notification.removeClass('show');
        setTimeout(function() {
            $notification.remove();
        }, 300);
    }

    /**
     * Image Gallery/Lightbox
     */
    function initImageGallery() {
        $('.portfolio-item img, .gallery img').on('click', function(e) {
            e.preventDefault();
            
            const src = $(this).attr('src') || $(this).data('src');
            const alt = $(this).attr('alt') || '';
            
            const lightbox = $('<div class="lightbox">' +
                '<div class="lightbox-overlay"></div>' +
                '<div class="lightbox-content">' +
                '<img src="' + src + '" alt="' + alt + '">' +
                '<button class="lightbox-close"><i class="fas fa-times"></i></button>' +
                '</div>' +
                '</div>');
            
            $('body').append(lightbox).addClass('lightbox-open');
            
            // Show lightbox
            setTimeout(function() {
                lightbox.addClass('show');
            }, 50);
            
            // Close handlers
            lightbox.find('.lightbox-close, .lightbox-overlay').on('click', function() {
                closeLightbox(lightbox);
            });
            
            // ESC key handler
            $(document).on('keyup.lightbox', function(e) {
                if (e.keyCode === 27) {
                    closeLightbox(lightbox);
                }
            });
        });
    }

    /**
     * Close Lightbox
     */
    function closeLightbox($lightbox) {
        $lightbox.removeClass('show');
        $('body').removeClass('lightbox-open');
        
        setTimeout(function() {
            $lightbox.remove();
        }, 300);
        
        $(document).off('keyup.lightbox');
    }

    // Initialize image gallery
    initImageGallery();

    /**
     * Cookie Consent (GDPR)
     */
    function initCookieConsent() {
        if (!localStorage.getItem('cookieConsent')) {
            const consent = $('<div class="cookie-consent">' +
                '<div class="cookie-content">' +
                '<p>Questo sito utilizza cookie per migliorare la tua esperienza. Continuando la navigazione accetti il loro utilizzo.</p>' +
                '<button class="btn btn-primary cookie-accept">Accetta</button>' +
                '<a href="/privacy-policy" class="cookie-policy">Privacy Policy</a>' +
                '</div>' +
                '</div>');
            
            $('body').append(consent);
            
            setTimeout(function() {
                consent.addClass('show');
            }, 1000);
            
            consent.find('.cookie-accept').on('click', function() {
                localStorage.setItem('cookieConsent', 'accepted');
                consent.removeClass('show');
                setTimeout(function() {
                    consent.remove();
                }, 300);
            });
        }
    }

    // Initialize cookie consent
    initCookieConsent();

    /**
     * Performance Monitoring
     */
    function trackPerformance() {
        if ('performance' in window) {
            window.addEventListener('load', function() {
                setTimeout(function() {
                    const perfData = performance.timing;
                    const loadTime = perfData.loadEventEnd - perfData.navigationStart;
                    
                    // Track with analytics if available
                    if (typeof gtag !== 'undefined') {
                        gtag('event', 'timing_complete', {
                            name: 'load',
                            value: loadTime
                        });
                    }
                }, 0);
            });
        }
    }

    // Initialize performance tracking
    trackPerformance();

    /**
     * Map Interaction Fix
     * Prevents map interactions from affecting navigation styles
     */
    function initMapInteractionFix() {
        // Wait for map to be initialized
        setTimeout(function() {
            // Find all map containers
            const mapContainers = document.querySelectorAll('.leaflet-container, .osm-map, .location-map-container');
            
            mapContainers.forEach(function(container) {
                // Prevent focus events from bubbling up
                container.addEventListener('focus', function(e) {
                    e.stopPropagation();
                }, true);
                
                container.addEventListener('focusin', function(e) {
                    e.stopPropagation();
                }, true);
                
                // Reset any navigation styles that might have been affected
                container.addEventListener('click', function() {
                    // Reset navigation colors after a brief delay
                    setTimeout(function() {
                        const navElements = document.querySelectorAll('.main-navigation a, .site-header');
                        navElements.forEach(function(nav) {
                            nav.style.color = '';
                            nav.style.backgroundColor = '';
                        });
                    }, 100);
                });
            });
        }, 1000);
    }

    /**
     * Language Switcher Functionality - DISABILITATO
     * Sistema di traduzione temporaneamente rimosso durante lo sviluppo
     */
    function initLanguageSwitcher() {
        console.log('Language switcher disabled during development');
        // Funzionalità temporaneamente disabilitata
        return;
    }


/**
 * Manual Language Change Function - GLOBAL
 * This function is called when no multilingual plugin is available
 */

// Add event listener for language select when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('Setting up language select event listener...');
    
    const languageSelect = document.getElementById('language-select');
    console.log('Language select found:', languageSelect ? 'YES' : 'NO');
    
    if (languageSelect) {
        languageSelect.addEventListener('change', function() {
            console.log('Language select changed via event listener to:', this.value);
            window.marcelloScavoChangeLanguage(this.value);
        });
        console.log('Language select event listener attached');
    }
});

/**
 * Main Theme JavaScript
 */
    /**
     * Show Language Change Notification
     */
    function showLanguageChangeNotification(langCode) {
        const messages = {
            'it': 'Lingua cambiata in Italiano',
            'en': 'Language changed to English', 
            'es': 'Idioma cambiado a Español'
        };
        
        const message = messages[langCode] || 'Language changed';
        
        // Create notification
        const notification = $('<div class="language-notification">' + message + '</div>');
        notification.css({
            'position': 'fixed',
            'top': '20px',
            'right': '20px',
            'background': '#28a745',
            'color': 'white',
            'padding': '10px 20px',
            'border-radius': '5px',
            'z-index': '9999',
            'font-size': '14px',
            'box-shadow': '0 2px 10px rgba(0,0,0,0.1)'
        });
        
        $('body').append(notification);
        
        // Auto remove after 3 seconds
        setTimeout(function() {
            notification.fadeOut(500, function() {
                notification.remove();
            });
        }, 3000);
    }

    /**
     * Load Saved Language Preference
     */
    function loadSavedLanguage() {
        const savedLang = localStorage.getItem('marcello_scavo_language');
        if (savedLang) {
            const languageSelect = document.getElementById('language-select');
            if (languageSelect) {
                languageSelect.value = savedLang;
                applyLanguageChanges(savedLang);
            }
        }
    }

    // Load saved language on page load
    $(document).ready(function() {
        console.log('Document ready - initializing language system');
        
        // Debug: mostra tutti gli elementi traducibili
        setTimeout(function() {
            debugTranslatableElements();
        }, 500);
        
        loadSavedLanguage();
        
        // Test the language system after a brief delay
        setTimeout(function() {
            console.log('Testing language system...');
            console.log('Hero label element:', $('.hero-label').length);
            console.log('Hero label text:', $('.hero-label').text());
            console.log('Section titles:', $('.section-title').length);
            console.log('Navigation items:', $('.primary-menu a').length);
            console.log('Language select:', $('#language-select').length);
        }, 1000);
    });

})(jQuery);
/* Cache busted Mar  2 Set 2025 07:35:10 CEST */
