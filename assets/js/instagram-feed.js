/**
 * Instagram Feed JavaScript for Marcello Scavo Theme
 * Uses a simpler approach with local images and external API fallback
 */

class MarcelloInstagramFeed {
    constructor(container, options = {}) {
        this.container = container;
        this.options = {
            count: options.count || 6,
            layout: options.layout || 'grid',
            showCaptions: options.showCaptions || false,
            username: options.username || 'marcelloscavo_art',
            ...options
        };
        
        this.init();
    }

    async init() {
        try {
            // Try to fetch from a simple Instagram feed service
            await this.loadInstagramPosts();
        } catch (error) {
            console.log('Instagram API not available, using fallback images');
            this.loadFallbackImages();
        }
    }

    async loadInstagramPosts() {
        // Using a public Instagram feed service (like RSS or simplified API)
        const proxyUrl = `${window.location.origin}/wp-json/marcello/v1/instagram-feed`;
        
        try {
            const response = await fetch(proxyUrl);
            const data = await response.json();
            
            if (data && data.posts && data.posts.length > 0) {
                this.renderPosts(data.posts.slice(0, this.options.count));
                return;
            }
        } catch (error) {
            console.log('Proxy API failed, using fallback');
        }
        
        // If API fails, use fallback
        this.loadFallbackImages();
    }

    loadFallbackImages() {
        const themeUrl = marcelloInstagramData.themeUrl;
        
        const fallbackPosts = [
            {
                id: 'fallback_1',
                image_url: `${themeUrl}/assets/images/IMG_4800.jpg`,
                caption: 'Lavoro di tatuaggio artistico - @marcelloscavo_art',
                timestamp: Date.now() - (3600000 * 2),
                link: 'https://instagram.com/marcelloscavo_art',
                likes: 124,
                comments: 15
            },
            {
                id: 'fallback_2',
                image_url: `${themeUrl}/assets/images/IMG_4854.JPG`,
                caption: 'Arte del tatuaggio - Dettagli e precisione',
                timestamp: Date.now() - (3600000 * 24),
                link: 'https://instagram.com/marcelloscavo_art',
                likes: 89,
                comments: 12
            },
            {
                id: 'fallback_3',
                image_url: `${themeUrl}/assets/images/DSC03105.JPEG`,
                caption: 'Studio di tatuaggio professionale',
                timestamp: Date.now() - (3600000 * 48),
                link: 'https://instagram.com/marcelloscavo_art',
                likes: 67,
                comments: 8
            },
            {
                id: 'fallback_4',
                image_url: `${themeUrl}/assets/images/54B3F245-E22C-4DDC-B5D2-885750AD64E6.JPG`,
                caption: 'Processo creativo del tatuaggio',
                timestamp: Date.now() - (3600000 * 72),
                link: 'https://instagram.com/marcelloscavo_art',
                likes: 156,
                comments: 21
            },
            {
                id: 'fallback_5',
                image_url: 'https://picsum.photos/600/600?random=tattoo1',
                caption: 'Tatuaggio artistico personalizzato',
                timestamp: Date.now() - (3600000 * 96),
                link: 'https://instagram.com/marcelloscavo_art',
                likes: 92,
                comments: 6
            },
            {
                id: 'fallback_6',
                image_url: 'https://picsum.photos/600/600?random=tattoo2',
                caption: 'Arte sulla pelle - Marcello Scavo',
                timestamp: Date.now() - (3600000 * 120),
                link: 'https://instagram.com/marcelloscavo_art',
                likes: 78,
                comments: 9
            }
        ];

        this.renderPosts(fallbackPosts.slice(0, this.options.count));
    }

    renderPosts(posts) {
        const layoutClass = `instagram-${this.options.layout}`;
        
        const html = `
            <div class="marcello-instagram-widget ${layoutClass}">
                <div class="instagram-header">
                    <div class="instagram-user-info">
                        <i class="fab fa-instagram"></i>
                        <span class="instagram-username">@${this.options.username}</span>
                    </div>
                    <a href="https://instagram.com/${this.options.username}" target="_blank" rel="noopener" class="instagram-follow-btn">
                        <i class="fas fa-external-link-alt"></i>
                        Segui
                    </a>
                </div>

                <div class="instagram-grid">
                    ${posts.map(post => this.renderPost(post)).join('')}
                </div>

                <div class="instagram-footer">
                    <a href="https://instagram.com/${this.options.username}" target="_blank" rel="noopener" class="instagram-view-more">
                        Visualizza su Instagram
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        `;

        this.container.innerHTML = html;
    }

    renderPost(post) {
        const caption = this.options.showCaptions && post.caption ? 
            `<div class="instagram-post-caption">
                <p>${this.truncateText(post.caption, 15)}</p>
            </div>` : '';

        return `
            <div class="instagram-post">
                <div class="instagram-post-image">
                    <img src="${post.image_url}" alt="Instagram Post" loading="lazy">
                    <div class="instagram-post-overlay">
                        <div class="instagram-post-stats">
                            <span class="instagram-likes">
                                <i class="fas fa-heart"></i>
                                ${this.formatNumber(post.likes)}
                            </span>
                            <span class="instagram-comments">
                                <i class="fas fa-comment"></i>
                                ${this.formatNumber(post.comments)}
                            </span>
                        </div>
                    </div>
                    <a href="${post.link}" target="_blank" rel="noopener" class="instagram-post-link"></a>
                </div>
                ${caption}
            </div>
        `;
    }

    truncateText(text, wordLimit) {
        const words = text.split(' ');
        return words.length > wordLimit ? 
            words.slice(0, wordLimit).join(' ') + '...' : 
            text;
    }

    formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
}

// Initialize Instagram feeds when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const instagramContainers = document.querySelectorAll('.marcello-instagram-container');
    
    instagramContainers.forEach(container => {
        const options = {
            count: parseInt(container.dataset.count) || 6,
            layout: container.dataset.layout || 'grid',
            showCaptions: container.dataset.showCaptions === 'true',
            username: container.dataset.username || 'marcelloscavo_art'
        };
        
        new MarcelloInstagramFeed(container, options);
    });
});
