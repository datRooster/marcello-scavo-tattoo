/**
 * Portfolio Slider JavaScript
 * @package MarcelloScavoTattoo
 */

class PortfolioSlider {
    constructor(container) {
        this.container = container;
        this.wrapper = container.querySelector('.portfolio-slider-wrapper');
        this.track = container.querySelector('.portfolio-slider-track');
        this.slides = container.querySelectorAll('.portfolio-slider-slide');
        this.prevBtn = container.querySelector('.portfolio-slider-prev');
        this.nextBtn = container.querySelector('.portfolio-slider-next');
        this.dots = container.querySelectorAll('.portfolio-slider-dot');
        
        this.currentSlide = 0;
        this.slideCount = this.slides.length;
        this.autoplay = container.dataset.autoplay === 'true';
        this.autoplaySpeed = parseInt(container.dataset.autoplaySpeed) || 5000;
        this.autoplayTimer = null;
        
        this.init();
    }
    
    init() {
        if (this.slideCount <= 1) return;
        
        this.setupSlider();
        this.bindEvents();
        
        if (this.autoplay) {
            this.startAutoplay();
        }
        
        // Pausa autoplay al hover
        this.container.addEventListener('mouseenter', () => this.pauseAutoplay());
        this.container.addEventListener('mouseleave', () => {
            if (this.autoplay) this.startAutoplay();
        });
    }
    
    setupSlider() {
        // Imposta larghezza totale del track
        this.track.style.width = `${this.slideCount * 100}%`;
        
        // Imposta larghezza di ogni slide
        this.slides.forEach(slide => {
            slide.style.width = `${100 / this.slideCount}%`;
        });
        
        // Aggiorna indicatori
        this.updateDots();
    }
    
    bindEvents() {
        if (this.prevBtn) {
            this.prevBtn.addEventListener('click', () => this.prevSlide());
        }
        
        if (this.nextBtn) {
            this.nextBtn.addEventListener('click', () => this.nextSlide());
        }
        
        // Eventi sui dot indicators
        this.dots.forEach((dot, index) => {
            dot.addEventListener('click', () => this.goToSlide(index));
        });
        
        // Touch/swipe support
        let startX = 0;
        let endX = 0;
        
        this.track.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
        });
        
        this.track.addEventListener('touchend', (e) => {
            endX = e.changedTouches[0].clientX;
            this.handleSwipe(startX, endX);
        });
        
        // Mouse drag support
        let isDragging = false;
        let startPos = 0;
        let currentTranslate = 0;
        let prevTranslate = 0;
        
        this.track.addEventListener('mousedown', (e) => {
            isDragging = true;
            startPos = e.clientX;
            this.track.style.cursor = 'grabbing';
            this.pauseAutoplay();
        });
        
        this.track.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            e.preventDefault();
            const currentPosition = e.clientX;
            currentTranslate = prevTranslate + currentPosition - startPos;
        });
        
        this.track.addEventListener('mouseup', () => {
            isDragging = false;
            this.track.style.cursor = 'grab';
            
            const movedBy = currentTranslate - prevTranslate;
            
            if (Math.abs(movedBy) > 50) {
                if (movedBy < 0) {
                    this.nextSlide();
                } else {
                    this.prevSlide();
                }
            }
            
            prevTranslate = currentTranslate;
            
            if (this.autoplay) this.startAutoplay();
        });
        
        this.track.addEventListener('mouseleave', () => {
            if (isDragging) {
                isDragging = false;
                this.track.style.cursor = 'grab';
                prevTranslate = currentTranslate;
                if (this.autoplay) this.startAutoplay();
            }
        });
        
        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (!this.isInViewport()) return;
            
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                this.prevSlide();
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                this.nextSlide();
            }
        });
    }
    
    handleSwipe(startX, endX) {
        const swipeThreshold = 50;
        const diff = startX - endX;
        
        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0) {
                this.nextSlide();
            } else {
                this.prevSlide();
            }
        }
    }
    
    goToSlide(slideIndex) {
        this.currentSlide = slideIndex;
        this.updateSlider();
    }
    
    nextSlide() {
        this.currentSlide = (this.currentSlide + 1) % this.slideCount;
        this.updateSlider();
    }
    
    prevSlide() {
        this.currentSlide = (this.currentSlide - 1 + this.slideCount) % this.slideCount;
        this.updateSlider();
    }
    
    updateSlider() {
        const translateX = -this.currentSlide * (100 / this.slideCount);
        this.track.style.transform = `translateX(${translateX}%)`;
        this.updateDots();
    }
    
    updateDots() {
        this.dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === this.currentSlide);
        });
    }
    
    startAutoplay() {
        if (!this.autoplay || this.slideCount <= 1) return;
        
        this.autoplayTimer = setInterval(() => {
            this.nextSlide();
        }, this.autoplaySpeed);
    }
    
    pauseAutoplay() {
        if (this.autoplayTimer) {
            clearInterval(this.autoplayTimer);
            this.autoplayTimer = null;
        }
    }
    
    isInViewport() {
        const rect = this.container.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }
}

// Initialize all portfolio sliders when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const sliders = document.querySelectorAll('.portfolio-slider-container');
    sliders.forEach(slider => {
        new PortfolioSlider(slider);
    });
});

// Reinitialize sliders if content is loaded dynamically
document.addEventListener('widget-added', function() {
    const sliders = document.querySelectorAll('.portfolio-slider-container:not([data-initialized])');
    sliders.forEach(slider => {
        slider.setAttribute('data-initialized', 'true');
        new PortfolioSlider(slider);
    });
});

// Enhanced lazy loading for portfolio images
document.addEventListener('DOMContentLoaded', function() {
    // Handle lazy loaded images
    const lazyImages = document.querySelectorAll('img[loading="lazy"]');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.addEventListener('load', () => {
                        img.classList.add('loaded');
                    });
                    observer.unobserve(img);
                }
            });
        });
        
        lazyImages.forEach(img => imageObserver.observe(img));
    } else {
        // Fallback for browsers without IntersectionObserver
        lazyImages.forEach(img => {
            img.addEventListener('load', () => {
                img.classList.add('loaded');
            });
        });
    }
    
    // Smooth scrolling for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                e.preventDefault();
                const headerOffset = 100;
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                
                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Add animation classes on scroll for portfolio items
    const portfolioItems = document.querySelectorAll('.portfolio-masonry-item');
    
    if (portfolioItems.length > 0) {
        const itemObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        
        portfolioItems.forEach((item, index) => {
            // Set initial state
            item.style.opacity = '0';
            item.style.transform = 'translateY(30px)';
            item.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
            
            itemObserver.observe(item);
        });
    }
});