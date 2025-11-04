/**
 * 3D Gallery Hero - Three.js & Parallax Effects
 * Crea una galleria d'arte virtuale immersiva
 */

class Gallery3D {
    constructor() {
        this.container = document.querySelector('.hero-3d-gallery');
        if (!this.container) return;

        this.canvas = document.getElementById('gallery-3d-canvas');
        this.loadingScreen = document.querySelector('.gallery-loading');
        this.galleryData = this.getGalleryData();
        
        // Fallback se canvas non trovato
        if (!this.canvas) {
            console.error('Canvas 3D non trovato! Creando canvas...');
            this.createCanvas();
        }
        
        this.scene = null;
        this.camera = null;
        this.renderer = null;
        this.controls = null;
        this.raycaster = null;
        this.mouse = null;
        
        this.artworks = [];
        this.room = null;
        this.isLoading = true;
        this.tourActive = false;
        this.tourStep = 0;
        this.focusedArtwork = null; // Traccia il quadro attualmente in focus
        this.isTransitioning = false; // Blocca input durante transizioni
        
        this.init();
    }

    createCanvas() {
        const canvas = document.createElement('canvas');
        canvas.id = 'gallery-3d-canvas';
        canvas.style.width = '100%';
        canvas.style.height = '100vh';
        canvas.style.display = 'block';
        
        if (this.container) {
            this.container.appendChild(canvas);
            this.canvas = canvas;
            console.log('Canvas creato con successo');
        }
    }

    getGalleryData() {
        console.log('=== RECUPERO GALLERY DATA ===');
        const dataScript = document.getElementById('gallery-data');
        
        if (!dataScript) {
            console.error('❌ Elemento #gallery-data non trovato nel DOM');
            return null;
        }
        
        console.log('✅ Elemento gallery-data trovato');
        console.log('Contenuto:', dataScript.textContent.substring(0, 200) + '...');
        
        try {
            const data = JSON.parse(dataScript.textContent);
            console.log('✅ JSON parsato con successo');
            console.log('Chiavi disponibili:', Object.keys(data));
            console.log('Numero immagini:', data.images ? data.images.length : 0);
            return data;
        } catch (e) {
            console.error('❌ Errore parsing gallery data:', e);
            return null;
        }
    }

    async init() {
        if (!this.galleryData || !this.galleryData.settings.enable3D) {
            this.hideLoading();
            this.initParallaxOnly();
            return;
        }

        try {
            // Carica Three.js dinamicamente se non presente
            if (typeof THREE === 'undefined') {
                await this.loadThreeJS();
            }

            this.setupScene();
            this.createRoom();
            await this.loadArtworks();
            this.setupControls();
            this.setupEventListeners();
            this.setupLighting();
            this.setupAudio();
            this.animate();
            
            // Disabilita parallax quando 3D è attivo
            this.disableParallaxEffects();
            
            setTimeout(() => this.hideLoading(), 1500);
            
        } catch (error) {
            console.error('Errore inizializzazione 3D:', error);
            this.hideLoading();
            this.initParallaxOnly();
        }
    }

    async loadThreeJS() {
        return new Promise((resolve, reject) => {
            // Verifica se Three.js è già caricato
            if (typeof THREE !== 'undefined') {
                console.log('✅ Three.js già caricato');
                // Carica moduli post-processing anche se THREE esiste già
                this.loadPostProcessingModules().then(resolve).catch(reject);
                return;
            }

            // Carica Three.js core
            const script1 = document.createElement('script');
            script1.src = 'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js';
            script1.onload = () => {
                console.log('✅ Three.js caricato con successo');
                // Dopo Three.js, carica moduli post-processing
                this.loadPostProcessingModules()
                    .then(() => {
                        console.log('✅ Post-processing modules pronti');
                        resolve();
                    })
                    .catch((err) => {
                        console.warn('⚠️ Post-processing non disponibile:', err);
                        resolve(); // Continua comunque
                    });
            };
            script1.onerror = (error) => {
                console.error('❌ Errore caricamento Three.js:', error);
                reject(error);
            };
            document.head.appendChild(script1);
        });
    }

    async loadPostProcessingModules() {
        // Carica moduli essenziali per rendering fotorealistico
        const modules = [
            'https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/postprocessing/EffectComposer.js',
            'https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/postprocessing/RenderPass.js',
            'https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/postprocessing/ShaderPass.js',
            'https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/shaders/CopyShader.js',
            // SimplexNoise per texture procedurali avanzate
            'https://cdnjs.cloudflare.com/ajax/libs/simplex-noise/2.4.0/simplex-noise.min.js'
        ];

        for (const url of modules) {
            try {
                await this.loadScript(url);
            } catch (error) {
                console.warn(`⚠️ Modulo non caricato: ${url.split('/').pop()}`);
            }
        }
        
        // Inizializza SimplexNoise se disponibile
        try {
            if (typeof SimplexNoise !== 'undefined') {
                this.simplexNoise = new SimplexNoise();
                console.log('✅ SimplexNoise inizializzato per texture avanzate');
            }
        } catch (error) {
            console.warn('⚠️ SimplexNoise non disponibile, usando fallback');
        }
    }

    loadScript(src) {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = src;
            script.onload = () => {
                console.log(`✅ Caricato: ${src.split('/').pop()}`);
                resolve();
            };
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    setupScene() {
        // Scene con sfondo bianco galleria moderna
        this.scene = new THREE.Scene();
        this.scene.background = new THREE.Color(0xf8f8f8); // Bianco neutro

        // Camera
        this.camera = new THREE.PerspectiveCamera(
            75, 
            this.canvas.clientWidth / this.canvas.clientHeight, 
            0.1, 
            1000
        );
        this.camera.position.set(0, 1.6, 5);

        // Renderer con impostazioni REALISTICHE
        this.renderer = new THREE.WebGLRenderer({ 
            canvas: this.canvas,
            antialias: true,
            alpha: true,
            powerPreference: 'high-performance'
        });
        this.renderer.setSize(this.canvas.clientWidth, this.canvas.clientHeight);
        this.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        
        // Shadow mapping realistico
        this.renderer.shadowMap.enabled = true;
        this.renderer.shadowMap.type = THREE.PCFSoftShadowMap;
        
        // Tone mapping per rendering naturale (NO vignettatura)
        this.renderer.toneMapping = THREE.LinearToneMapping;
        this.renderer.toneMappingExposure = 1.4;
        
        // Encoding colori realistico
        this.renderer.outputEncoding = THREE.sRGBEncoding;
        
        // Fisica delle luci corretta
        this.renderer.physicallyCorrectLights = true;

        // Initialize raycaster and mouse
        this.raycaster = new THREE.Raycaster();
        this.mouse = new THREE.Vector2();
        
        // Setup post-processing per rendering fotorealistico
        this.setupPostProcessing();
    }

    setupPostProcessing() {
        // Verifica se EffectComposer è disponibile
        if (typeof THREE.EffectComposer === 'undefined') {
            console.log('⏳ Post-processing non ancora disponibile, skip...');
            this.useComposer = false;
            return;
        }

        try {
            console.log('🎨 Inizializzazione post-processing...');
            
            // Effect Composer per rendering multi-pass
            this.composer = new THREE.EffectComposer(this.renderer);
            
            // Pass 1: Render base della scena
            const renderPass = new THREE.RenderPass(this.scene, this.camera);
            this.composer.addPass(renderPass);
            
            // Pass 2: Copy finale (necessario per visualizzazione)
            const copyPass = new THREE.ShaderPass(THREE.CopyShader);
            copyPass.renderToScreen = true;
            this.composer.addPass(copyPass);
            
            this.useComposer = true;
            console.log('✅ Post-processing attivo!');
            
        } catch (error) {
            console.warn('⚠️ Post-processing fallback:', error);
            this.useComposer = false;
        }
    }

    createParquetTexture() {
        // Crea canvas per texture ULTRA-REALISTICA con SimplexNoise
        const width = 2048;  // Alta risoluzione
        const height = 2048;
        const canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;
        const ctx = canvas.getContext('2d');

        // Colori parquet FREDDI ULTRA-REALISTICI (tonalità fotografiche)
        const baseColors = [
            '#75614B', // Marrone freddo medio
            '#6D5D4B', // Marrone grigio
            '#5C4A35', // Marrone scuro freddo
            '#685642', // Marrone neutro
            '#7A6A58', // Grigio-marrone
            '#625342', // Marrone cenere
            '#594B3A', // Marrone scuro neutro
            '#6B5D4F', // Marrone grigio chiaro
            '#5A4C3B'  // Marrone terra
        ];
        
        // Sfondo base con noise per texture sottile
        const baseGradient = ctx.createLinearGradient(0, 0, width, height);
        baseGradient.addColorStop(0, '#6D6156');
        baseGradient.addColorStop(0.5, '#5D5248');
        baseGradient.addColorStop(1, '#4D4338');
        ctx.fillStyle = baseGradient;
        ctx.fillRect(0, 0, width, height);

        // Aggiungi rumore di base con SimplexNoise se disponibile
        if (this.simplexNoise) {
            this.addNoiseToCanvas(ctx, width, height);
        }

        // Crea doghe realistiche con pattern variato
        const plankLength = 380 + Math.random() * 40; // Lunghezza variabile
        const plankWidth = 75 + Math.random() * 10;   // Larghezza variabile
        
        for (let y = 0; y < height; y += plankWidth) {
            for (let x = 0; x < width; x += plankLength) {
                const colorIndex = Math.floor(Math.random() * baseColors.length);
                const currentPlankLength = plankLength + (Math.random() * 40 - 20);
                const currentPlankWidth = plankWidth + (Math.random() * 6 - 3);
                
                this.drawUltraRealisticWoodPlank(
                    ctx, x, y, 
                    currentPlankLength, currentPlankWidth, 
                    baseColors[colorIndex]
                );
            }
        }

        // Post-processing per maggiore realismo
        this.addWoodPostProcessing(ctx, width, height);

        // Crea texture Three.js con parametri ottimizzati
        const texture = new THREE.CanvasTexture(canvas);
        texture.wrapS = THREE.RepeatWrapping;
        texture.wrapT = THREE.RepeatWrapping;
        texture.repeat.set(2.5, 2.5); // Scala ottimizzata
        texture.anisotropy = Math.min(16, this.renderer.capabilities.getMaxAnisotropy());
        
        // Mipmapping per qualità a distanza
        texture.generateMipmaps = true;
        texture.minFilter = THREE.LinearMipmapLinearFilter;
        texture.magFilter = THREE.LinearFilter;

        console.log('✅ Texture parquet ULTRA-realistica creata con SimplexNoise (2048x2048)');
        return texture;
    }

    addNoiseToCanvas(ctx, width, height) {
        // Aggiungi rumore di base con SimplexNoise per texture più realistica
        const imageData = ctx.getImageData(0, 0, width, height);
        const data = imageData.data;
        
        for (let y = 0; y < height; y++) {
            for (let x = 0; x < width; x++) {
                const i = (y * width + x) * 4;
                
                // Genera rumore con più frequenze per dettaglio
                const noise1 = this.simplexNoise.noise2D(x * 0.01, y * 0.01) * 0.3;
                const noise2 = this.simplexNoise.noise2D(x * 0.02, y * 0.02) * 0.2;
                const noise3 = this.simplexNoise.noise2D(x * 0.005, y * 0.005) * 0.4;
                
                const totalNoise = (noise1 + noise2 + noise3) * 15;
                
                // Applica rumore ai canali RGB
                data[i] = Math.max(0, Math.min(255, data[i] + totalNoise));
                data[i + 1] = Math.max(0, Math.min(255, data[i + 1] + totalNoise));
                data[i + 2] = Math.max(0, Math.min(255, data[i + 2] + totalNoise));
            }
        }
        
        ctx.putImageData(imageData, 0, 0);
    }

    addWoodPostProcessing(ctx, width, height) {
        // Aggiungi effetti di post-processing per maggiore realismo
        
        // Overlay sottile per variazioni di colore
        const overlayGradient = ctx.createRadialGradient(
            width/2, height/2, 0, 
            width/2, height/2, Math.max(width, height)/2
        );
        overlayGradient.addColorStop(0, 'rgba(255, 255, 255, 0.02)');
        overlayGradient.addColorStop(1, 'rgba(0, 0, 0, 0.05)');
        
        ctx.globalCompositeOperation = 'overlay';
        ctx.fillStyle = overlayGradient;
        ctx.fillRect(0, 0, width, height);
        
        // Ripristina composite operation
        ctx.globalCompositeOperation = 'source-over';
        
        // Aggiungi particelle di polvere/usura
        for (let i = 0; i < 50; i++) {
            const px = Math.random() * width;
            const py = Math.random() * height;
            const size = Math.random() * 3 + 1;
            
            ctx.fillStyle = `rgba(200, 180, 160, ${Math.random() * 0.1})`;
            ctx.beginPath();
            ctx.arc(px, py, size, 0, Math.PI * 2);
            ctx.fill();
        }
    }

    drawUltraRealisticWoodPlank(ctx, x, y, length, width, baseColor) {
        ctx.save();
        
        // Converti colore hex in RGB
        const r = parseInt(baseColor.slice(1, 3), 16);
        const g = parseInt(baseColor.slice(3, 5), 16);
        const b = parseInt(baseColor.slice(5, 7), 16);
        
        // Variazione casuale più naturale
        const variation = (Math.random() - 0.5) * 40;
        
        // Gradiente più complesso per effetto 3D ultra-realistico
        const gradient = ctx.createLinearGradient(x, y, x + length * 0.3, y + width);
        gradient.addColorStop(0, `rgb(${Math.round(r + variation + 15)}, ${Math.round(g + variation + 15)}, ${Math.round(b + variation + 15)})`);
        gradient.addColorStop(0.3, `rgb(${Math.round(r + variation + 5)}, ${Math.round(g + variation + 5)}, ${Math.round(b + variation + 5)})`);
        gradient.addColorStop(0.7, `rgb(${Math.round(r + variation)}, ${Math.round(g + variation)}, ${Math.round(b + variation)})`);
        gradient.addColorStop(1, `rgb(${Math.round(r + variation - 12)}, ${Math.round(g + variation - 12)}, ${Math.round(b + variation - 12)})`);
        
        ctx.fillStyle = gradient;
        ctx.fillRect(x, y, length, width);

        // Venature ULTRA-REALISTICHE con SimplexNoise
        this.drawWoodGrainWithNoise(ctx, x, y, length, width, r, g, b);
        
        // Nodi del legno più realistici
        if (Math.random() > 0.7) { // 30% chance per nodo
            this.drawRealisticWoodKnot(ctx, x, y, length, width);
        }
        
        // Micro-texture con pattern naturale
        this.addMicroTexture(ctx, x, y, length, width);

        // Bordo doga con variazioni naturali
        this.drawPlankEdges(ctx, x, y, length, width);

        ctx.restore();
    }

    drawWoodGrainWithNoise(ctx, x, y, length, width, r, g, b) {
        // Venature principali con SimplexNoise se disponibile
        const numMainGrains = 15 + Math.floor(Math.random() * 10);
        
        for (let i = 0; i < numMainGrains; i++) {
            const grainY = y + (i / numMainGrains) * width;
            const opacity = 0.1 + Math.random() * 0.3;
            const grainWidth = 0.5 + Math.random() * 2;
            
            // Colore venatura più naturale
            const grainR = Math.max(20, r - 40 - Math.random() * 30);
            const grainG = Math.max(15, g - 35 - Math.random() * 25);
            const grainB = Math.max(10, b - 30 - Math.random() * 20);
            
            ctx.strokeStyle = `rgba(${grainR}, ${grainG}, ${grainB}, ${opacity})`;
            ctx.lineWidth = grainWidth;
            ctx.beginPath();
            
            // Percorso della venatura con SimplexNoise per naturalezza
            const points = [];
            const numPoints = 20;
            
            for (let p = 0; p <= numPoints; p++) {
                const progress = p / numPoints;
                const baseX = x + progress * length;
                const baseY = grainY;
                
                let offsetY = 0;
                if (this.simplexNoise) {
                    // Usa SimplexNoise per curvatura naturale
                    offsetY = this.simplexNoise.noise2D(baseX * 0.01, baseY * 0.01) * 8;
                    offsetY += this.simplexNoise.noise2D(baseX * 0.02, baseY * 0.02) * 4;
                } else {
                    // Fallback con Math.sin
                    offsetY = Math.sin(progress * Math.PI * 2 + Math.random()) * 6;
                }
                
                points.push({ x: baseX, y: baseY + offsetY });
            }
            
            // Disegna venatura fluida
            ctx.moveTo(points[0].x, points[0].y);
            for (let p = 1; p < points.length - 2; p++) {
                const cpX = (points[p].x + points[p + 1].x) / 2;
                const cpY = (points[p].y + points[p + 1].y) / 2;
                ctx.quadraticCurveTo(points[p].x, points[p].y, cpX, cpY);
            }
            ctx.stroke();
            
            // Venature secondarie ramificate
            if (Math.random() > 0.6) {
                this.drawSecondaryGrain(ctx, points, grainR, grainG, grainB, opacity * 0.6);
            }
        }
    }

    drawSecondaryGrain(ctx, mainPoints, r, g, b, opacity) {
        const branchPoint = Math.floor(Math.random() * (mainPoints.length - 2)) + 1;
        const startPoint = mainPoints[branchPoint];
        
        ctx.strokeStyle = `rgba(${r}, ${g}, ${b}, ${opacity})`;
        ctx.lineWidth = 0.3 + Math.random() * 0.8;
        ctx.beginPath();
        ctx.moveTo(startPoint.x, startPoint.y);
        
        const branchLength = 20 + Math.random() * 40;
        const endX = startPoint.x + (Math.random() - 0.5) * branchLength;
        const endY = startPoint.y + (Math.random() - 0.5) * 15;
        
        ctx.quadraticCurveTo(
            startPoint.x + (Math.random() - 0.5) * 20,
            startPoint.y + (Math.random() - 0.5) * 10,
            endX, endY
        );
        ctx.stroke();
    }

    drawRealisticWoodKnot(ctx, x, y, length, width) {
        const knotX = x + Math.random() * length;
        const knotY = y + Math.random() * width;
        const knotSize = 6 + Math.random() * 16;
        
        // Gradiente radiale ultra-realistico per nodo
        const knotGradient = ctx.createRadialGradient(knotX, knotY, 0, knotX, knotY, knotSize);
        knotGradient.addColorStop(0, 'rgba(45, 25, 15, 0.9)');   // Centro molto scuro
        knotGradient.addColorStop(0.2, 'rgba(65, 35, 20, 0.7)'); // Anello interno
        knotGradient.addColorStop(0.5, 'rgba(85, 50, 30, 0.5)'); // Medio
        knotGradient.addColorStop(0.8, 'rgba(100, 65, 40, 0.3)'); // Esterno
        knotGradient.addColorStop(1, 'rgba(100, 65, 40, 0)');    // Trasparente
        
        ctx.fillStyle = knotGradient;
        ctx.beginPath();
        ctx.arc(knotX, knotY, knotSize, 0, Math.PI * 2);
        ctx.fill();
        
        // Anelli concentrici nel nodo con variazioni
        const numRings = 3 + Math.floor(Math.random() * 3);
        for (let ring = 1; ring <= numRings; ring++) {
            const ringOpacity = 0.25 - (ring * 0.05);
            const ringRadius = knotSize * (ring / numRings);
            
            ctx.strokeStyle = `rgba(40, 25, 15, ${ringOpacity})`;
            ctx.lineWidth = 0.5 + Math.random() * 0.5;
            ctx.beginPath();
            ctx.arc(knotX, knotY, ringRadius, 0, Math.PI * 2);
            ctx.stroke();
        }
        
        // Crepe radiali nel nodo
        if (Math.random() > 0.6) {
            const numCracks = 2 + Math.floor(Math.random() * 4);
            for (let c = 0; c < numCracks; c++) {
                const angle = (c / numCracks) * Math.PI * 2 + Math.random() * 0.5;
                const crackLength = knotSize * (0.3 + Math.random() * 0.4);
                
                ctx.strokeStyle = 'rgba(30, 20, 10, 0.4)';
                ctx.lineWidth = 0.3;
                ctx.beginPath();
                ctx.moveTo(knotX, knotY);
                ctx.lineTo(
                    knotX + Math.cos(angle) * crackLength,
                    knotY + Math.sin(angle) * crackLength
                );
                ctx.stroke();
            }
        }
    }

    addMicroTexture(ctx, x, y, length, width) {
        // Micro-texture per rugosità del legno ultra-dettagliata
        const numParticles = 200 + Math.floor(Math.random() * 100);
        
        for (let i = 0; i < numParticles; i++) {
            const px = x + Math.random() * length;
            const py = y + Math.random() * width;
            const size = Math.random() * 1.5;
            const opacity = Math.random() * 0.08;
            
            // Variazione colore particelle
            const isLight = Math.random() > 0.5;
            const colorBase = isLight ? '120,100,80' : '50,35,25';
            
            ctx.fillStyle = `rgba(${colorBase}, ${opacity})`;
            ctx.fillRect(px, py, size, size);
        }
    }

    drawPlankEdges(ctx, x, y, length, width) {
        // Bordo principale con variazioni naturali
        ctx.strokeStyle = 'rgba(60, 40, 25, 0.5)';
        ctx.lineWidth = 1.5 + Math.random() * 0.5;
        ctx.strokeRect(x, y, length, width);
        
        // Ombra interna per profondità con gradiente
        const shadowGradient = ctx.createLinearGradient(x, y, x + 3, y + 3);
        shadowGradient.addColorStop(0, 'rgba(0, 0, 0, 0.15)');
        shadowGradient.addColorStop(1, 'rgba(0, 0, 0, 0)');
        
        ctx.strokeStyle = shadowGradient;
        ctx.lineWidth = 2;
        ctx.strokeRect(x + 1, y + 1, length - 2, width - 2);
        
        // Usura sui bordi
        if (Math.random() > 0.7) {
            const edgeVariations = 5 + Math.floor(Math.random() * 8);
            for (let e = 0; e < edgeVariations; e++) {
                const edgeX = x + Math.random() * length;
                const edgeY = y + (Math.random() > 0.5 ? 0 : width);
                const wearSize = 1 + Math.random() * 3;
                
                ctx.fillStyle = 'rgba(80, 60, 40, 0.3)';
                ctx.fillRect(edgeX, edgeY - wearSize/2, wearSize, wearSize);
            }
        }
    }

    createParquetNormalMap() {
        // Crea normal map dedicata per maggiore profondità 3D
        const width = 2048;
        const height = 2048;
        const canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;
        const ctx = canvas.getContext('2d');

        // Base neutra per normal map (grigio medio = superficie piatta)
        ctx.fillStyle = '#8080ff'; // Blu-grigio neutro per normal map
        ctx.fillRect(0, 0, width, height);

        const plankLength = 380 + Math.random() * 40;
        const plankWidth = 75 + Math.random() * 10;
        
        for (let y = 0; y < height; y += plankWidth) {
            for (let x = 0; x < width; x += plankLength) {
                this.drawNormalMapPlank(ctx, x, y, plankLength, plankWidth);
            }
        }

        const texture = new THREE.CanvasTexture(canvas);
        texture.wrapS = THREE.RepeatWrapping;
        texture.wrapT = THREE.RepeatWrapping;
        texture.repeat.set(2.5, 2.5);
        texture.anisotropy = Math.min(16, this.renderer.capabilities.getMaxAnisotropy());

        console.log('✅ Normal map parquet creata (2048x2048)');
        return texture;
    }

    drawNormalMapPlank(ctx, x, y, length, width) {
        // Disegna le normali delle doghe per effetto 3D
        
        // Gradiente per bordi rialzati delle doghe
        const edgeGradient = ctx.createLinearGradient(x, y, x, y + width);
        edgeGradient.addColorStop(0, '#9090ff');   // Bordo superiore rialzato
        edgeGradient.addColorStop(0.1, '#8080ff'); // Neutro
        edgeGradient.addColorStop(0.9, '#8080ff'); // Neutro
        edgeGradient.addColorStop(1, '#7070ff');   // Bordo inferiore incassato
        
        ctx.fillStyle = edgeGradient;
        ctx.fillRect(x, y, length, width);

        // Venature come depressioni nella normal map
        const numGrains = 10 + Math.floor(Math.random() * 8);
        for (let i = 0; i < numGrains; i++) {
            const grainY = y + (i / numGrains) * width;
            
            ctx.strokeStyle = '#6060ff'; // Depressione (più scuro)
            ctx.lineWidth = 1 + Math.random() * 2;
            ctx.beginPath();
            ctx.moveTo(x, grainY);
            
            // Curva delle venature
            const cp1x = x + length * 0.3;
            const cp1y = grainY + (Math.random() - 0.5) * 8;
            const cp2x = x + length * 0.7;
            const cp2y = grainY + (Math.random() - 0.5) * 8;
            
            ctx.bezierCurveTo(cp1x, cp1y, cp2x, cp2y, x + length, grainY);
            ctx.stroke();
        }

        // Bordi doghe come rilievi
        ctx.strokeStyle = '#a0a0ff'; // Rilievo (più chiaro)
        ctx.lineWidth = 3;
        ctx.strokeRect(x, y, length, width);
    }

    createParquetRoughnessMap() {
        // Crea roughness map per variazioni di lucidità realistica
        const width = 2048;
        const height = 2048;
        const canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;
        const ctx = canvas.getContext('2d');

        // Base grigia per roughness moderata
        ctx.fillStyle = '#666666'; // Grigio medio = roughness normale
        ctx.fillRect(0, 0, width, height);

        const plankLength = 380 + Math.random() * 40;
        const plankWidth = 75 + Math.random() * 10;
        
        for (let y = 0; y < height; y += plankWidth) {
            for (let x = 0; x < width; x += plankLength) {
                this.drawRoughnessMapPlank(ctx, x, y, plankLength, plankWidth);
            }
        }

        const texture = new THREE.CanvasTexture(canvas);
        texture.wrapS = THREE.RepeatWrapping;
        texture.wrapT = THREE.RepeatWrapping;
        texture.repeat.set(2.5, 2.5);
        texture.anisotropy = Math.min(16, this.renderer.capabilities.getMaxAnisotropy());

        console.log('✅ Roughness map parquet creata (2048x2048)');
        return texture;
    }

    drawRoughnessMapPlank(ctx, x, y, length, width) {
        // Variazioni di roughness per ogni doga
        const baseRoughness = 0.5 + Math.random() * 0.3; // Variazione per doga
        const grayValue = Math.floor(baseRoughness * 255);
        
        ctx.fillStyle = `rgb(${grayValue}, ${grayValue}, ${grayValue})`;
        ctx.fillRect(x, y, length, width);

        // Venature più ruvide (legno naturale)
        const numGrains = 8 + Math.floor(Math.random() * 6);
        for (let i = 0; i < numGrains; i++) {
            const grainY = y + Math.random() * width;
            const roughness = Math.max(0.7, baseRoughness + 0.2); // Venature più ruvide
            const grainGray = Math.floor(roughness * 255);
            
            ctx.strokeStyle = `rgb(${grainGray}, ${grainGray}, ${grainGray})`;
            ctx.lineWidth = 2 + Math.random() * 3;
            ctx.beginPath();
            ctx.moveTo(x, grainY);
            ctx.lineTo(x + length, grainY + (Math.random() - 0.5) * 6);
            ctx.stroke();
        }

        // Nodi più lucidi (verniciatura accumula)
        if (Math.random() > 0.8) {
            const knotX = x + Math.random() * length;
            const knotY = y + Math.random() * width;
            const knotSize = 8 + Math.random() * 12;
            const knotRoughness = Math.max(0.1, baseRoughness - 0.3); // Nodi più lucidi
            const knotGray = Math.floor(knotRoughness * 255);
            
            ctx.fillStyle = `rgb(${knotGray}, ${knotGray}, ${knotGray})`;
            ctx.beginPath();
            ctx.arc(knotX, knotY, knotSize, 0, Math.PI * 2);
            ctx.fill();
        }

        // Bordi leggermente più ruvidi
        ctx.strokeStyle = `rgb(${Math.min(255, grayValue + 20)}, ${Math.min(255, grayValue + 20)}, ${Math.min(255, grayValue + 20)})`;
        ctx.lineWidth = 2;
        ctx.strokeRect(x, y, length, width);
    }

    createWhiteWallTexture() {
        // Crea texture CARTONGESSO ULTRA-REALISTICA con SimplexNoise
        const width = 2048;  // Alta risoluzione
        const height = 2048;
        const canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;
        const ctx = canvas.getContext('2d');

        // Base cartongesso bianco con leggere variazioni naturali
        const baseGradient = ctx.createLinearGradient(0, 0, width, height);
        baseGradient.addColorStop(0, '#fdfcfa');   // Bianco caldo molto leggero
        baseGradient.addColorStop(0.3, '#faf9f7'); // Bianco neutro
        baseGradient.addColorStop(0.7, '#f8f7f5'); // Bianco leggermente grigio
        baseGradient.addColorStop(1, '#f6f5f3');   // Bianco sporco sottile
        ctx.fillStyle = baseGradient;
        ctx.fillRect(0, 0, width, height);

        // Aggiungi rumore di base con SimplexNoise per texture cartongesso
        if (this.simplexNoise) {
            this.addDrywallNoise(ctx, width, height);
        }

        // Texture tipica del cartongesso (superficie leggermente granulosa)
        this.addDrywallGranulation(ctx, width, height);
        
        // Imperfezioni tipiche del cartongesso
        this.addDrywallImperfections(ctx, width, height);
        
        // Giunti e viti del cartongesso
        this.addDrywallJoints(ctx, width, height);
        
        // Micro-ombre e variazioni di luce
        this.addDrywallLightVariations(ctx, width, height);

        // Post-processing per maggiore realismo
        this.addDrywallPostProcessing(ctx, width, height);

        // Crea texture Three.js con parametri ottimizzati
        const texture = new THREE.CanvasTexture(canvas);
        texture.wrapS = THREE.RepeatWrapping;
        texture.wrapT = THREE.RepeatWrapping;
        texture.repeat.set(1.5, 1.5); // Scala ottimizzata per cartongesso
        texture.anisotropy = Math.min(16, this.renderer.capabilities.getMaxAnisotropy());
        texture.encoding = THREE.sRGBEncoding;
        texture.generateMipmaps = true;
        texture.minFilter = THREE.LinearMipmapLinearFilter;
        texture.magFilter = THREE.LinearFilter;

        console.log('✅ Texture cartongesso ULTRA-realistica creata con SimplexNoise (2048x2048)');
        return texture;
    }

    addDrywallNoise(ctx, width, height) {
        // Rumore di base per texture cartongesso con SimplexNoise
        const imageData = ctx.getImageData(0, 0, width, height);
        const data = imageData.data;
        
        for (let y = 0; y < height; y++) {
            for (let x = 0; x < width; x++) {
                const i = (y * width + x) * 4;
                
                // Rumore multi-frequenza per texture cartongesso naturale
                const noise1 = this.simplexNoise.noise2D(x * 0.008, y * 0.008) * 0.4;
                const noise2 = this.simplexNoise.noise2D(x * 0.02, y * 0.02) * 0.2;
                const noise3 = this.simplexNoise.noise2D(x * 0.05, y * 0.05) * 0.1;
                
                const totalNoise = (noise1 + noise2 + noise3) * 8; // Più sottile del parquet
                
                // Applica rumore mantenendo tonalità bianche
                data[i] = Math.max(240, Math.min(255, data[i] + totalNoise));     // R
                data[i + 1] = Math.max(240, Math.min(255, data[i + 1] + totalNoise)); // G
                data[i + 2] = Math.max(240, Math.min(255, data[i + 2] + totalNoise)); // B
            }
        }
        
        ctx.putImageData(imageData, 0, 0);
    }

    addDrywallGranulation(ctx, width, height) {
        // Granulazione tipica della superficie del cartongesso
        const numGrains = 3000; // Granulazione fine
        
        for (let i = 0; i < numGrains; i++) {
            const x = Math.random() * width;
            const y = Math.random() * height;
            const size = Math.random() * 1.5 + 0.5;
            const opacity = Math.random() * 0.03 + 0.01;
            
            // Variazioni molto sottili di grigio
            const grayVariation = Math.random() > 0.5 ? 5 : -5;
            const grayValue = 248 + grayVariation;
            
            ctx.fillStyle = `rgba(${grayValue}, ${grayValue}, ${grayValue}, ${opacity})`;
            ctx.beginPath();
            ctx.arc(x, y, size, 0, Math.PI * 2);
            ctx.fill();
        }
    }

    addDrywallImperfections(ctx, width, height) {
        // Piccole imperfezioni del cartongesso (non perfettamente liscio)
        
        // Micro-crepe occasionali
        const numCracks = 8 + Math.floor(Math.random() * 12);
        for (let i = 0; i < numCracks; i++) {
            const startX = Math.random() * width;
            const startY = Math.random() * height;
            const length = 20 + Math.random() * 80;
            const angle = Math.random() * Math.PI * 2;
            
            ctx.strokeStyle = 'rgba(235, 235, 235, 0.3)';
            ctx.lineWidth = 0.3 + Math.random() * 0.5;
            ctx.beginPath();
            ctx.moveTo(startX, startY);
            ctx.lineTo(
                startX + Math.cos(angle) * length,
                startY + Math.sin(angle) * length
            );
            ctx.stroke();
        }
        
        // Piccole ondulazioni della superficie
        const numWaves = 15 + Math.floor(Math.random() * 10);
        for (let i = 0; i < numWaves; i++) {
            const centerX = Math.random() * width;
            const centerY = Math.random() * height;
            const radius = 40 + Math.random() * 100;
            
            const waveGradient = ctx.createRadialGradient(
                centerX, centerY, 0,
                centerX, centerY, radius
            );
            waveGradient.addColorStop(0, 'rgba(250, 250, 250, 0.02)');
            waveGradient.addColorStop(0.5, 'rgba(245, 245, 245, 0.01)');
            waveGradient.addColorStop(1, 'rgba(248, 248, 248, 0)');
            
            ctx.fillStyle = waveGradient;
            ctx.beginPath();
            ctx.arc(centerX, centerY, radius, 0, Math.PI * 2);
            ctx.fill();
        }
    }

    addDrywallJoints(ctx, width, height) {
        // Giunti tipici del cartongesso (ogni ~120cm)
        const jointSpacing = 400; // Pixel equivalenti a ~120cm
        
        // Giunti verticali
        for (let x = jointSpacing; x < width; x += jointSpacing + Math.random() * 40 - 20) {
            const jointVariation = (Math.random() - 0.5) * 20;
            
            ctx.strokeStyle = 'rgba(240, 240, 240, 0.4)';
            ctx.lineWidth = 2 + Math.random() * 1;
            ctx.beginPath();
            ctx.moveTo(x + jointVariation, 0);
            ctx.lineTo(x + jointVariation, height);
            ctx.stroke();
            
            // Nastro del giunto (leggermente più chiaro)
            ctx.strokeStyle = 'rgba(248, 248, 248, 0.2)';
            ctx.lineWidth = 8 + Math.random() * 4;
            ctx.beginPath();
            ctx.moveTo(x + jointVariation, 0);
            ctx.lineTo(x + jointVariation, height);
            ctx.stroke();
        }
        
        // Giunti orizzontali  
        for (let y = jointSpacing; y < height; y += jointSpacing + Math.random() * 40 - 20) {
            const jointVariation = (Math.random() - 0.5) * 20;
            
            ctx.strokeStyle = 'rgba(240, 240, 240, 0.4)';
            ctx.lineWidth = 2 + Math.random() * 1;
            ctx.beginPath();
            ctx.moveTo(0, y + jointVariation);
            ctx.lineTo(width, y + jointVariation);
            ctx.stroke();
            
            // Nastro del giunto
            ctx.strokeStyle = 'rgba(248, 248, 248, 0.2)';
            ctx.lineWidth = 8 + Math.random() * 4;
            ctx.beginPath();
            ctx.moveTo(0, y + jointVariation);
            ctx.lineTo(width, y + jointVariation);
            ctx.stroke();
        }
        
        // Viti del cartongesso (occasionali)
        const numScrews = 25 + Math.floor(Math.random() * 15);
        for (let i = 0; i < numScrews; i++) {
            const screwX = Math.random() * width;
            const screwY = Math.random() * height;
            const screwSize = 2 + Math.random() * 1;
            
            // Piccola depressione della vite
            const screwGradient = ctx.createRadialGradient(
                screwX, screwY, 0,
                screwX, screwY, screwSize * 2
            );
            screwGradient.addColorStop(0, 'rgba(230, 230, 230, 0.3)');
            screwGradient.addColorStop(0.5, 'rgba(240, 240, 240, 0.1)');
            screwGradient.addColorStop(1, 'rgba(248, 248, 248, 0)');
            
            ctx.fillStyle = screwGradient;
            ctx.beginPath();
            ctx.arc(screwX, screwY, screwSize * 2, 0, Math.PI * 2);
            ctx.fill();
        }
    }

    addDrywallLightVariations(ctx, width, height) {
        // Variazioni sottili di luce come su vero cartongesso
        const numZones = 20 + Math.floor(Math.random() * 15);
        
        for (let i = 0; i < numZones; i++) {
            const zoneX = Math.random() * width;
            const zoneY = Math.random() * height;
            const zoneRadius = 100 + Math.random() * 200;
            const intensity = Math.random() * 0.02 + 0.005;
            
            const lightGradient = ctx.createRadialGradient(
                zoneX, zoneY, 0,
                zoneX, zoneY, zoneRadius
            );
            
            const isHighlight = Math.random() > 0.5;
            if (isHighlight) {
                lightGradient.addColorStop(0, `rgba(255, 255, 255, ${intensity})`);
                lightGradient.addColorStop(1, 'rgba(255, 255, 255, 0)');
            } else {
                lightGradient.addColorStop(0, `rgba(240, 240, 240, ${intensity})`);
                lightGradient.addColorStop(1, 'rgba(240, 240, 240, 0)');
            }
            
            ctx.fillStyle = lightGradient;
            ctx.beginPath();
            ctx.arc(zoneX, zoneY, zoneRadius, 0, Math.PI * 2);
            ctx.fill();
        }
    }

    addDrywallPostProcessing(ctx, width, height) {
        // Post-processing per maggiore autenticità del cartongesso
        
        // Overlay generale per uniformare
        const overlayGradient = ctx.createLinearGradient(0, 0, width, height);
        overlayGradient.addColorStop(0, 'rgba(252, 252, 252, 0.03)');
        overlayGradient.addColorStop(0.5, 'rgba(248, 248, 248, 0.01)');
        overlayGradient.addColorStop(1, 'rgba(250, 250, 250, 0.02)');
        
        ctx.globalCompositeOperation = 'overlay';
        ctx.fillStyle = overlayGradient;
        ctx.fillRect(0, 0, width, height);
        
        // Ripristina composite operation
        ctx.globalCompositeOperation = 'source-over';
        
        // Polvere di cartongesso (molto sottile)
        for (let i = 0; i < 100; i++) {
            const px = Math.random() * width;
            const py = Math.random() * height;
            const size = Math.random() * 2;
            
            ctx.fillStyle = `rgba(255, 255, 255, ${Math.random() * 0.02})`;
            ctx.beginPath();
            ctx.arc(px, py, size, 0, Math.PI * 2);
            ctx.fill();
        }
    }

    createDrywallNormalMap() {
        // Crea normal map per cartongesso con rilievi sottili
        const width = 2048;
        const height = 2048;
        const canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;
        const ctx = canvas.getContext('2d');

        // Base neutra per normal map
        ctx.fillStyle = '#8080ff'; // Blu-grigio neutro
        ctx.fillRect(0, 0, width, height);

        // Giunti del cartongesso come rilievi/depressioni
        this.drawDrywallJointsNormal(ctx, width, height);
        
        // Texture granulare sottile
        this.addDrywallNormalGrain(ctx, width, height);

        const texture = new THREE.CanvasTexture(canvas);
        texture.wrapS = THREE.RepeatWrapping;
        texture.wrapT = THREE.RepeatWrapping;
        texture.repeat.set(1.5, 1.5);
        texture.anisotropy = Math.min(16, this.renderer.capabilities.getMaxAnisotropy());

        console.log('✅ Normal map cartongesso creata (2048x2048)');
        return texture;
    }

    drawDrywallJointsNormal(ctx, width, height) {
        const jointSpacing = 400;
        
        // Giunti verticali nella normal map
        for (let x = jointSpacing; x < width; x += jointSpacing + Math.random() * 40 - 20) {
            const jointVariation = (Math.random() - 0.5) * 20;
            
            // Depressione del giunto
            ctx.strokeStyle = '#7070ff'; // Più scuro = depressione
            ctx.lineWidth = 3 + Math.random() * 2;
            ctx.beginPath();
            ctx.moveTo(x + jointVariation, 0);
            ctx.lineTo(x + jointVariation, height);
            ctx.stroke();
            
            // Bordi rialzati del nastro
            ctx.strokeStyle = '#9090ff'; // Più chiaro = rilievo
            ctx.lineWidth = 1;
            ctx.beginPath();
            ctx.moveTo(x + jointVariation - 4, 0);
            ctx.lineTo(x + jointVariation - 4, height);
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(x + jointVariation + 4, 0);
            ctx.lineTo(x + jointVariation + 4, height);
            ctx.stroke();
        }
        
        // Giunti orizzontali nella normal map
        for (let y = jointSpacing; y < height; y += jointSpacing + Math.random() * 40 - 20) {
            const jointVariation = (Math.random() - 0.5) * 20;
            
            ctx.strokeStyle = '#7070ff';
            ctx.lineWidth = 3 + Math.random() * 2;
            ctx.beginPath();
            ctx.moveTo(0, y + jointVariation);
            ctx.lineTo(width, y + jointVariation);
            ctx.stroke();
            
            // Bordi rialzati
            ctx.strokeStyle = '#9090ff';
            ctx.lineWidth = 1;
            ctx.beginPath();
            ctx.moveTo(0, y + jointVariation - 4);
            ctx.lineTo(width, y + jointVariation - 4);
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(0, y + jointVariation + 4);
            ctx.lineTo(width, y + jointVariation + 4);
            ctx.stroke();
        }
    }

    addDrywallNormalGrain(ctx, width, height) {
        // Granulazione sottile per normal map
        const numGrains = 1000;
        
        for (let i = 0; i < numGrains; i++) {
            const x = Math.random() * width;
            const y = Math.random() * height;
            const size = Math.random() * 2;
            const variation = (Math.random() - 0.5) * 30;
            
            const normalValue = 128 + variation;
            const normalColor = `rgb(${normalValue}, ${normalValue}, ${Math.max(200, normalValue + 50)})`;
            
            ctx.fillStyle = normalColor;
            ctx.beginPath();
            ctx.arc(x, y, size, 0, Math.PI * 2);
            ctx.fill();
        }
    }

    createDrywallRoughnessMap() {
        // Crea roughness map per cartongesso
        const width = 2048;
        const height = 2048;
        const canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;
        const ctx = canvas.getContext('2d');

        // Base per cartongesso (moderatamente ruvido)
        ctx.fillStyle = '#999999'; // Grigio medio
        ctx.fillRect(0, 0, width, height);

        // Variazioni di roughness per cartongesso
        this.addDrywallRoughnessVariations(ctx, width, height);

        const texture = new THREE.CanvasTexture(canvas);
        texture.wrapS = THREE.RepeatWrapping;
        texture.wrapT = THREE.RepeatWrapping;
        texture.repeat.set(1.5, 1.5);
        texture.anisotropy = Math.min(16, this.renderer.capabilities.getMaxAnisotropy());

        console.log('✅ Roughness map cartongesso creata (2048x2048)');
        return texture;
    }

    addDrywallRoughnessVariations(ctx, width, height) {
        // Zone più lisce (nastro del giunto)
        const jointSpacing = 400;
        
        for (let x = jointSpacing; x < width; x += jointSpacing + Math.random() * 40 - 20) {
            ctx.strokeStyle = '#777777'; // Più scuro = più liscio
            ctx.lineWidth = 12 + Math.random() * 4;
            ctx.beginPath();
            ctx.moveTo(x, 0);
            ctx.lineTo(x, height);
            ctx.stroke();
        }
        
        for (let y = jointSpacing; y < height; y += jointSpacing + Math.random() * 40 - 20) {
            ctx.strokeStyle = '#777777';
            ctx.lineWidth = 12 + Math.random() * 4;
            ctx.beginPath();
            ctx.moveTo(0, y);
            ctx.lineTo(width, y);
            ctx.stroke();
        }
        
        // Variazioni casuali di roughness
        const numZones = 50;
        for (let i = 0; i < numZones; i++) {
            const zoneX = Math.random() * width;
            const zoneY = Math.random() * height;
            const zoneRadius = 30 + Math.random() * 100;
            const roughnessVar = (Math.random() - 0.5) * 40;
            const grayValue = Math.max(100, Math.min(200, 153 + roughnessVar));
            
            const roughnessGradient = ctx.createRadialGradient(
                zoneX, zoneY, 0,
                zoneX, zoneY, zoneRadius
            );
            roughnessGradient.addColorStop(0, `rgb(${grayValue}, ${grayValue}, ${grayValue})`);
            roughnessGradient.addColorStop(1, `rgba(${grayValue}, ${grayValue}, ${grayValue}, 0)`);
            
            ctx.fillStyle = roughnessGradient;
            ctx.beginPath();
            ctx.arc(zoneX, zoneY, zoneRadius, 0, Math.PI * 2);
            ctx.fill();
        }
    }

    createEntranceWall() {
        // Parete d'ingresso (dietro l'utente) con porte in vetro
        const wallHeight = 4;
        const wallWidth = 10;
        
        const wallTexture = this.createWhiteWallTexture();
        const wallNormalMap = this.createDrywallNormalMap();
        const wallRoughnessMap = this.createDrywallRoughnessMap();
        
        const wallMaterial = new THREE.MeshStandardMaterial({ 
            map: wallTexture,
            normalMap: wallNormalMap,           // Normal map per profondità giunti
            roughnessMap: wallRoughnessMap,     // Roughness map per variazioni lucidità
            color: 0xffffff,
            roughness: 0.8,                     // Cartongesso semi-opaco
            metalness: 0.0,                     // Non metallico
            normalScale: new THREE.Vector2(0.3, 0.3), // Normali più sottili del parquet
            bumpMap: wallTexture,               // Bump mapping sottile
            bumpScale: 0.001,                   // Rilievo molto sottile per cartongesso
            emissive: 0xffffff,                 // Leggera emissione per luminosità
            emissiveIntensity: 0.2              // Intensità ridotta
        });

        // Parete sinistra dell'ingresso
        const leftPanelGeom = new THREE.PlaneGeometry(3, wallHeight);
        const leftPanel = new THREE.Mesh(leftPanelGeom, wallMaterial);
        leftPanel.position.set(-3.5, wallHeight/2, wallWidth/2);
        leftPanel.rotation.y = Math.PI;
        leftPanel.receiveShadow = true;
        this.scene.add(leftPanel);

        // Parete destra dell'ingresso
        const rightPanelGeom = new THREE.PlaneGeometry(3, wallHeight);
        const rightPanel = new THREE.Mesh(rightPanelGeom, wallMaterial);
        rightPanel.position.set(3.5, wallHeight/2, wallWidth/2);
        rightPanel.rotation.y = Math.PI;
        rightPanel.receiveShadow = true;
        this.scene.add(rightPanel);

        // Sopra la porta
        const topPanelGeom = new THREE.PlaneGeometry(4, wallHeight - 2.5);
        const topPanel = new THREE.Mesh(topPanelGeom, wallMaterial);
        topPanel.position.set(0, wallHeight - (wallHeight - 2.5)/2, wallWidth/2);
        topPanel.rotation.y = Math.PI;
        topPanel.receiveShadow = true;
        this.scene.add(topPanel);

        // Crea porte in vetro
        this.createGlassDoors(wallWidth);
        
        // Cornice porta
        this.createDoorFrame(wallWidth);
    }

    createGlassDoors(wallWidth) {
        // Materiale vetro realistico
        const glassMaterial = new THREE.MeshPhysicalMaterial({
            color: 0xffffff,
            metalness: 0.0,
            roughness: 0.1,
            transparent: true,
            opacity: 0.3,
            transmission: 0.9,  // Trasmissione luce per vetro
            thickness: 0.05,
            clearcoat: 1.0,
            clearcoatRoughness: 0.1
        });

        // Porta sinistra
        const doorGeom = new THREE.BoxGeometry(1.8, 2.4, 0.05);
        const leftDoor = new THREE.Mesh(doorGeom, glassMaterial);
        leftDoor.position.set(-0.95, 1.2, wallWidth/2 - 0.025);
        leftDoor.castShadow = true;
        leftDoor.receiveShadow = true;
        this.scene.add(leftDoor);

        // Porta destra
        const rightDoor = new THREE.Mesh(doorGeom, glassMaterial);
        rightDoor.position.set(0.95, 1.2, wallWidth/2 - 0.025);
        rightDoor.castShadow = true;
        rightDoor.receiveShadow = true;
        this.scene.add(rightDoor);

        // Maniglie porta (dettagli realistici)
        const handleMaterial = new THREE.MeshStandardMaterial({
            color: 0xc0c0c0,
            metalness: 0.9,
            roughness: 0.2
        });

        // Maniglia porta sinistra
        const handleGeom = new THREE.CylinderGeometry(0.02, 0.02, 0.15, 8);
        const handleLeft = new THREE.Mesh(handleGeom, handleMaterial);
        handleLeft.rotation.z = Math.PI / 2;
        handleLeft.position.set(-0.2, 1.2, wallWidth/2);
        this.scene.add(handleLeft);

        // Maniglia porta destra
        const handleRight = new THREE.Mesh(handleGeom, handleMaterial);
        handleRight.rotation.z = Math.PI / 2;
        handleRight.position.set(0.2, 1.2, wallWidth/2);
        this.scene.add(handleRight);
    }

    createDoorFrame(wallWidth) {
        // Materiale cornice (alluminio spazzolato)
        const frameMaterial = new THREE.MeshStandardMaterial({
            color: 0xd4d4d4,
            metalness: 0.7,
            roughness: 0.4
        });

        const frameDepth = 0.08;
        const frameWidth = 0.06;

        // Montante sinistro
        const leftPost = new THREE.Mesh(
            new THREE.BoxGeometry(frameWidth, 2.5, frameDepth),
            frameMaterial
        );
        leftPost.position.set(-1.9, 1.25, wallWidth/2);
        this.scene.add(leftPost);

        // Montante destro
        const rightPost = new THREE.Mesh(
            new THREE.BoxGeometry(frameWidth, 2.5, frameDepth),
            frameMaterial
        );
        rightPost.position.set(1.9, 1.25, wallWidth/2);
        this.scene.add(rightPost);

        // Architrave (sopra)
        const topFrame = new THREE.Mesh(
            new THREE.BoxGeometry(3.92, frameWidth, frameDepth),
            frameMaterial
        );
        topFrame.position.set(0, 2.5, wallWidth/2);
        this.scene.add(topFrame);
    }

    createRoom() {
        // Galleria d'arte moderna - pavimento in parquet realistico
        const floorGeometry = new THREE.PlaneGeometry(20, 20);
        
        // Usa TextureLoader per texture più realistiche
        const textureLoader = new THREE.TextureLoader();
        
        // Parquet texture ULTRA-REALISTICA usando canvas migliorato
        const parquetTexture = this.createParquetTexture();
        const parquetNormalMap = this.createParquetNormalMap();
        const parquetRoughnessMap = this.createParquetRoughnessMap();
        
        const floorMaterial = new THREE.MeshStandardMaterial({ 
            map: parquetTexture,
            normalMap: parquetNormalMap,     // Normal map dedicata per maggiore profondità
            roughnessMap: parquetRoughnessMap, // Roughness map separata per variazioni realistiche
            bumpMap: parquetTexture,         // Aggiungi bump mapping per rilievo
            roughness: 0.6,                  // Parquet verniciato semi-lucido
            metalness: 0.0,                  // Legno non è metallico
            bumpScale: 0.003,                // Rilievo più pronunciato
            normalScale: new THREE.Vector2(0.5, 0.5), // Intensità normal map
            envMapIntensity: 0.3             // Riflessi ambientali più evidenti
        });
        
        const floor = new THREE.Mesh(floorGeometry, floorMaterial);
        floor.rotation.x = -Math.PI / 2;
        floor.position.y = 0;
        floor.receiveShadow = true;
        this.scene.add(floor);

        // Pareti e decorazioni
        this.createWalls();
        this.createEntranceWall(); // Nuova parete d'ingresso con porte
        this.createGalleryDecorations();
    }

    createWalls() {
        const wallHeight = 4;
        const wallWidth = 10;
        
        // Crea texture CARTONGESSO ULTRA-REALISTICA per pareti
        const wallTexture = this.createWhiteWallTexture();
        const wallNormalMap = this.createDrywallNormalMap();
        const wallRoughnessMap = this.createDrywallRoughnessMap();
        
        // Pareti cartongesso FOTOREALISTICHE con PBR materials
        const wallMaterial = new THREE.MeshStandardMaterial({ 
            map: wallTexture,
            normalMap: wallNormalMap,           // Normal map per giunti e texture
            roughnessMap: wallRoughnessMap,     // Roughness map per variazioni realistiche
            color: 0xffffff,                    // Bianco puro
            roughness: 0.8,                     // Cartongesso semi-opaco
            metalness: 0.0,                     // Completamente non metallico
            normalScale: new THREE.Vector2(0.3, 0.3), // Normali sottili
            bumpMap: wallTexture,               // Rilievo micro-texture cartongesso
            bumpScale: 0.001,                   // Rilievo sottile per cartongesso
            envMapIntensity: 0.1,               // Riflessi ambientali minimi
            emissive: 0xffffff,                 // Emissione per garantire luminosità
            emissiveIntensity: 0.2              // Ridotto per più realismo
        });

        // Parete posteriore
        const backWallGeometry = new THREE.PlaneGeometry(wallWidth, wallHeight);
        const backWall = new THREE.Mesh(backWallGeometry, wallMaterial);
        backWall.position.set(0, wallHeight/2, -wallWidth/2);
        backWall.receiveShadow = true;
        this.scene.add(backWall);

        // Pareti laterali
        const sideWallGeometry = new THREE.PlaneGeometry(wallWidth, wallHeight);
        
        const leftWall = new THREE.Mesh(sideWallGeometry, wallMaterial);
        leftWall.rotation.y = Math.PI / 2;
        leftWall.position.set(-wallWidth/2, wallHeight/2, 0);
        leftWall.receiveShadow = true;
        this.scene.add(leftWall);

        const rightWall = new THREE.Mesh(sideWallGeometry, wallMaterial);
        rightWall.rotation.y = -Math.PI / 2;
        rightWall.position.set(wallWidth/2, wallHeight/2, 0);
        rightWall.receiveShadow = true;
        this.scene.add(rightWall);
        
        // Soffitto bianco da galleria
        const ceilingGeometry = new THREE.PlaneGeometry(wallWidth, wallWidth);
        const ceilingMaterial = new THREE.MeshStandardMaterial({ 
            map: wallTexture,
            color: 0xffffff, // Bianco puro
            roughness: 0.9,
            metalness: 0.0,
            emissive: 0xffffff,
            emissiveIntensity: 0.3
        });
        const ceiling = new THREE.Mesh(ceilingGeometry, ceilingMaterial);
        ceiling.rotation.x = -Math.PI / 2;
        ceiling.position.set(0, wallHeight, 0);
        ceiling.receiveShadow = true;
        this.scene.add(ceiling);
    }

    createGalleryDecorations() {
        // Panca minimalista al centro
        this.createMinimalSeating();
        
        // Illuminazione da galleria professionale
        this.createGalleryLighting();
        
        // Elementi decorativi minimal
        this.createMinimalDecor();
    }

    createPlants() {
        // Pianta 1 - Palma nell'angolo
        const plant1 = this.createPlant(0x228B22, 0x8B4513);
        plant1.position.set(-4, 0, 3);
        plant1.scale.set(0.8, 1.2, 0.8);
        this.scene.add(plant1);

        // Pianta 2 - Piccola pianta decorativa
        const plant2 = this.createPlant(0x32CD32, 0x654321);
        plant2.position.set(3.5, 0, 2.5);
        plant2.scale.set(0.5, 0.7, 0.5);
        this.scene.add(plant2);

        // Pianta 3 - Pianta nell'angolo (non sospesa)
        const plant3 = this.createPlant(0x90EE90, 0x8B4513);
        plant3.position.set(4, 0, -4); // Nell'angolo in basso a destra
        plant3.scale.set(0.4, 0.8, 0.4);
    }

    createMinimalSeating() {
        // Panca elegante da galleria (LEGNO CHIARO)
        const benchGeometry = new THREE.BoxGeometry(1.8, 0.08, 0.5);
        const benchMaterial = new THREE.MeshStandardMaterial({ 
            color: 0xdeb887, // Legno chiaro (burlywood)
            roughness: 0.7,
            metalness: 0.0
        });
        const bench = new THREE.Mesh(benchGeometry, benchMaterial);
        bench.position.set(0, 0.45, 1);
        bench.castShadow = true;
        bench.receiveShadow = true;

        // Gambe in legno scuro per contrasto
        const legHeight = 0.4;
        for (let i = 0; i < 4; i++) {
            const legGeometry = new THREE.BoxGeometry(0.04, legHeight, 0.04);
            const legMaterial = new THREE.MeshStandardMaterial({ 
                color: 0x8b7355, // Legno scuro
                roughness: 0.6,
                metalness: 0.0
            });
            const leg = new THREE.Mesh(legGeometry, legMaterial);
            
            const x = i < 2 ? -0.7 : 0.7;
            const z = i % 2 === 0 ? -0.2 : 0.2;
            
            // Posiziona gambe SOTTO il piano (non sopra)
            leg.position.set(x, -(legHeight / 2) - 0.04, z);
            leg.castShadow = true;
            bench.add(leg);
        }

        this.scene.add(bench);
    }

    createGalleryLighting() {
        // Faretti a soffitto per illuminazione professionale
        this.createSpotlights();
    }

    createSpotlights() {
        // Faretti direzionali sul soffitto
        const spotPositions = [
            { x: -2, z: -3 },
            { x: 2, z: -3 },
            { x: -3, z: 0 },
            { x: 3, z: 0 },
            { x: 0, z: 2 }
        ];

        spotPositions.forEach(pos => {
            // Base del faretto (corpo nero)
            const spotGeometry = new THREE.CylinderGeometry(0.08, 0.12, 0.2, 8);
            const spotMaterial = new THREE.MeshLambertMaterial({ color: 0x333333 });
            const spotlight = new THREE.Mesh(spotGeometry, spotMaterial);
            spotlight.position.set(pos.x, 3.8, pos.z);
            spotlight.castShadow = true;
            this.scene.add(spotlight);
            
            // Luce interna del faretto (ACCESA - effetto luminoso)
            const lightGeometry = new THREE.CylinderGeometry(0.07, 0.1, 0.05, 8);
            const lightMaterial = new THREE.MeshBasicMaterial({ 
                color: 0xffffcc, // Giallo caldo
                emissive: 0xffffcc,
                emissiveIntensity: 1.0
            });
            const lightBulb = new THREE.Mesh(lightGeometry, lightMaterial);
            lightBulb.position.set(pos.x, 3.7, pos.z); // Leggermente più basso
            this.scene.add(lightBulb);
            
            // Punto luce reale per illuminazione
            const pointLight = new THREE.PointLight(0xffffcc, 0.8, 10);
            pointLight.position.set(pos.x, 3.7, pos.z);
            pointLight.castShadow = true;
            pointLight.shadow.mapSize.width = 1024;
            pointLight.shadow.mapSize.height = 1024;
            this.scene.add(pointLight);
        });
    }

    createMinimalDecor() {
        // ORNAMENTI ELEGANTI DA GALLERIA D'ARTE
        
        // 1. Plinto marmoreo bianco per scultura
        const plinthGeometry = new THREE.BoxGeometry(0.6, 0.8, 0.6);
        const plinthMaterial = new THREE.MeshStandardMaterial({ 
            color: 0xffffff,
            roughness: 0.3,
            metalness: 0.1
        });
        const plinth = new THREE.Mesh(plinthGeometry, plinthMaterial);
        plinth.position.set(-3.5, 0.4, 3);
        plinth.castShadow = true;
        plinth.receiveShadow = true;
        this.scene.add(plinth);

        // 2. Scultura moderna astratta in bronzo
        const sculptureGeometry = new THREE.SphereGeometry(0.2, 16, 16);
        const sculptureMaterial = new THREE.MeshStandardMaterial({ 
            color: 0xcd7f32, // Bronzo
            roughness: 0.4,
            metalness: 0.8
        });
        const sculpture = new THREE.Mesh(sculptureGeometry, sculptureMaterial);
        sculpture.position.set(-3.5, 1.1, 3);
        sculpture.castShadow = true;
        this.scene.add(sculpture);

        // 3. Vaso decorativo in ceramica bianca
        const vaseGeometry = new THREE.CylinderGeometry(0.15, 0.2, 0.5, 16);
        const vaseMaterial = new THREE.MeshStandardMaterial({ 
            color: 0xf5f5f5,
            roughness: 0.2,
            metalness: 0.0
        });
        const vase = new THREE.Mesh(vaseGeometry, vaseMaterial);
        vase.position.set(3.5, 0.25, 3);
        vase.castShadow = true;
        vase.receiveShadow = true;
        this.scene.add(vase);

        // 4. Pianta decorativa nel vaso
        for (let i = 0; i < 5; i++) {
            const leafGeometry = new THREE.SphereGeometry(0.08, 8, 8);
            const leafMaterial = new THREE.MeshStandardMaterial({ 
                color: 0x2d5016, // Verde scuro
                roughness: 0.8
            });
            const leaf = new THREE.Mesh(leafGeometry, leafMaterial);
            const angle = (i / 5) * Math.PI * 2;
            leaf.position.set(
                3.5 + Math.cos(angle) * 0.1, 
                0.6 + Math.random() * 0.2, 
                3 + Math.sin(angle) * 0.1
            );
            leaf.castShadow = true;
            this.scene.add(leaf);
        }

        // 5. Libreria/scaffale moderno (angolo sinistro)
        const shelfMaterial = new THREE.MeshStandardMaterial({ 
            color: 0x5d4e37, // Marrone legno
            roughness: 0.7,
            metalness: 0.0
        });
        
        // Struttura verticale
        const shelfFrame = new THREE.Mesh(
            new THREE.BoxGeometry(0.8, 1.6, 0.3),
            shelfMaterial
        );
        shelfFrame.position.set(-4.5, 0.8, -3);
        shelfFrame.castShadow = true;
        shelfFrame.receiveShadow = true;
        this.scene.add(shelfFrame);

        // Ripiani
        for (let i = 0; i < 3; i++) {
            const shelf = new THREE.Mesh(
                new THREE.BoxGeometry(0.75, 0.03, 0.28),
                shelfMaterial
            );
            shelf.position.set(-4.5, 0.4 + i * 0.5, -3);
            shelf.receiveShadow = true;
            this.scene.add(shelf);
        }

        // 6. Catalogo/libri d'arte sugli scaffali
        for (let shelf = 0; shelf < 3; shelf++) {
            for (let book = 0; book < 3; book++) {
                const bookGeometry = new THREE.BoxGeometry(0.15, 0.22, 0.03);
                const bookColors = [0x8b4513, 0x2f4f4f, 0x800000, 0x4b0082];
                const bookMaterial = new THREE.MeshStandardMaterial({ 
                    color: bookColors[Math.floor(Math.random() * bookColors.length)],
                    roughness: 0.9
                });
                const bookMesh = new THREE.Mesh(bookGeometry, bookMaterial);
                bookMesh.position.set(
                    -4.5 - 0.25 + book * 0.2, 
                    0.52 + shelf * 0.5, 
                    -3 + Math.random() * 0.05
                );
                bookMesh.rotation.y = Math.random() * 0.1 - 0.05;
                bookMesh.castShadow = true;
                this.scene.add(bookMesh);
            }
        }

        // 7. Tappeto decorativo sotto la panca
        const rugGeometry = new THREE.PlaneGeometry(2.5, 1.2);
        const rugMaterial = new THREE.MeshStandardMaterial({ 
            color: 0x8b7355, // Beige/sabbia
            roughness: 1.0,
            metalness: 0.0
        });
        const rug = new THREE.Mesh(rugGeometry, rugMaterial);
        rug.rotation.x = -Math.PI / 2;
        rug.position.set(0, 0.01, 1);
        rug.receiveShadow = true;
        this.scene.add(rug);
    }

    createPlant(leafColor, trunkColor) {
        const plant = new THREE.Group();

        // Vaso
        const potGeometry = new THREE.CylinderGeometry(0.15, 0.2, 0.3, 8);
        const potMaterial = new THREE.MeshLambertMaterial({ color: 0x8B4513 });
        const pot = new THREE.Mesh(potGeometry, potMaterial);
        pot.position.y = 0.15;
        plant.add(pot);

        // Tronco/gambo principale
        const trunkGeometry = new THREE.CylinderGeometry(0.05, 0.08, 0.8, 6);
        const trunkMaterial = new THREE.MeshLambertMaterial({ color: trunkColor });
        const trunk = new THREE.Mesh(trunkGeometry, trunkMaterial);
        trunk.position.y = 0.7;
        plant.add(trunk);

        // Foglie (multiple sfere per semplicità)
        const leafMaterial = new THREE.MeshLambertMaterial({ color: leafColor });
        for (let i = 0; i < 6; i++) {
            const leafGeometry = new THREE.SphereGeometry(0.2, 8, 6);
            const leaf = new THREE.Mesh(leafGeometry, leafMaterial);
            
            const angle = (i / 6) * Math.PI * 2;
            leaf.position.set(
                Math.cos(angle) * 0.3,
                1.1 + Math.sin(i) * 0.2,
                Math.sin(angle) * 0.3
            );
            leaf.scale.set(1, 0.3, 1);
            plant.add(leaf);
        }

        return plant;
    }

    createSeating(roomStyle) {
        // Panca moderna al centro
        const benchGeometry = new THREE.BoxGeometry(2, 0.1, 0.8);
        let benchColor;
        switch (roomStyle) {
            case 'modern': benchColor = 0xffffff; break;
            case 'classic': benchColor = 0x8b4513; break;
            case 'industrial': benchColor = 0x2c2c2c; break;
            default: benchColor = 0xd3d3d3;
        }
        
        const benchMaterial = new THREE.MeshLambertMaterial({ color: benchColor });
        const bench = new THREE.Mesh(benchGeometry, benchMaterial);
        bench.position.set(0, 0.55, 0); // Alzo leggermente la panca
        bench.castShadow = true;
        bench.receiveShadow = true;

        // Gambe della panca
        for (let i = 0; i < 4; i++) {
            const legGeometry = new THREE.BoxGeometry(0.1, 0.5, 0.1);
            const legMaterial = new THREE.MeshLambertMaterial({ color: benchColor });
            const leg = new THREE.Mesh(legGeometry, legMaterial);
            
            const x = i < 2 ? -0.8 : 0.8;
            const z = i % 2 === 0 ? -0.3 : 0.3;
            leg.position.set(x, 0.25, z);
            leg.castShadow = true;
            bench.add(leg);
        }

        this.scene.add(bench);
    }

    createSculptures(roomStyle) {
        // Scultura geometrica
        const sculptureGeometry = new THREE.OctahedronGeometry(0.3, 0);
        const sculptureMaterial = new THREE.MeshPhysicalMaterial({
            color: 0xc0c0c0,
            metalness: 0.8,
            roughness: 0.2,
            clearcoat: 1.0
        });
        const sculpture = new THREE.Mesh(sculptureGeometry, sculptureMaterial);
        sculpture.position.set(-2, 1, 1.5);
        sculpture.castShadow = true;
        this.scene.add(sculpture);

        // Piedistallo per la scultura
        const pedestalGeometry = new THREE.CylinderGeometry(0.4, 0.4, 0.6, 8);
        const pedestalMaterial = new THREE.MeshLambertMaterial({ color: 0xf5f5f5 });
        const pedestal = new THREE.Mesh(pedestalGeometry, pedestalMaterial);
        pedestal.position.set(-2, 0.3, 1.5);
        pedestal.castShadow = true;
        pedestal.receiveShadow = true;
        this.scene.add(pedestal);
    }

    createDecorativeLighting() {
        // Lampada da terra elegante
        const lampPost = new THREE.Group();

        // Base della lampada
        const baseGeometry = new THREE.CylinderGeometry(0.15, 0.2, 0.1, 8);
        const baseMaterial = new THREE.MeshLambertMaterial({ color: 0x333333 });
        const base = new THREE.Mesh(baseGeometry, baseMaterial);
        base.position.y = 0.05;
        lampPost.add(base);

        // Asta della lampada
        const poleGeometry = new THREE.CylinderGeometry(0.02, 0.02, 2, 8);
        const poleMaterial = new THREE.MeshLambertMaterial({ color: 0x444444 });
        const pole = new THREE.Mesh(poleGeometry, poleMaterial);
        pole.position.y = 1;
        lampPost.add(pole);

        // Paralume
        const shadeGeometry = new THREE.ConeGeometry(0.3, 0.4, 8);
        const shadeMaterial = new THREE.MeshLambertMaterial({ 
            color: 0xf5f5dc,
            transparent: true,
            opacity: 0.8 
        });
        const shade = new THREE.Mesh(shadeGeometry, shadeMaterial);
        shade.position.y = 2.2;
        lampPost.add(shade);

        // Luce interna
        const lampLight = new THREE.PointLight(0xfff8dc, 0.5, 5);
        lampLight.position.y = 2;
        lampPost.add(lampLight);

        lampPost.position.set(2.5, 0, -2);
        this.scene.add(lampPost);
    }

    async loadArtworks() {
        console.log('=== INIZIO CARICAMENTO OPERE ===');
        console.log('Gallery Data:', this.galleryData);
        
        // Prima carichiamo gli skeleton come placeholder
        this.createSkeletonFrames();
        
        // Verifica dati della gallery
        if (!this.galleryData) {
            console.error('❌ galleryData è undefined o null');
            return;
        }
        
        if (!this.galleryData.images) {
            console.error('❌ galleryData.images non esiste');
            console.log('Struttura galleryData disponibile:', Object.keys(this.galleryData));
            return;
        }
        
        if (this.galleryData.images.length === 0) {
            console.warn('⚠️ galleryData.images è vuoto (nessuna immagine)');
            return;
        }
        
        console.log(`✅ Trovate ${this.galleryData.images.length} immagini da caricare`);
        console.log('Prima immagine:', this.galleryData.images[0]);
        
        const textureLoader = new THREE.TextureLoader();
        
        // Posizioni corrispondenti agli skeleton
        const artworkPositions = [
            // Parete di fondo (3 posizioni)
            { x: -3, y: 2, z: -4.99, rotation: 0, wall: 'fondo-sx' },
            { x: 0, y: 2, z: -4.99, rotation: 0, wall: 'fondo-centro' },
            { x: 3, y: 2, z: -4.99, rotation: 0, wall: 'fondo-dx' },
            
            // Parete sinistra (3 posizioni)
            { x: -4.99, y: 2, z: -2, rotation: Math.PI/2, wall: 'sinistra-1' },
            { x: -4.99, y: 2, z: 0, rotation: Math.PI/2, wall: 'sinistra-2' },
            { x: -4.99, y: 2, z: 2, rotation: Math.PI/2, wall: 'sinistra-3' },
            
            // Parete destra (3 posizioni)
            { x: 4.99, y: 2, z: -2, rotation: -Math.PI/2, wall: 'destra-1' },
            { x: 4.99, y: 2, z: 0, rotation: -Math.PI/2, wall: 'destra-2' },
            { x: 4.99, y: 2, z: 2, rotation: -Math.PI/2, wall: 'destra-3' }
        ];
        
        // Carica le immagini in modo asincrono
        const loadPromises = this.galleryData.images.map((image, index) => {
            if (index >= artworkPositions.length) {
                console.log(`Immagine ${index} saltata (troppi elementi)`);
                return Promise.resolve();
            }
            
            return new Promise((resolve) => {
                console.log(`Caricamento immagine ${index}: ${image.title}`);
                
                textureLoader.load(
                    image.medium || image.url,
                    (texture) => {
                        console.log(`✅ Immagine ${index} caricata: ${image.title}`);
                        const artwork = this.createArtwork(texture, artworkPositions[index], image);
                        this.artworks.push(artwork);
                        this.scene.add(artwork.group);
                        resolve();
                    },
                    undefined,
                    (error) => {
                        console.error(`❌ Errore caricamento immagine ${index}:`, error);
                        resolve();
                    }
                );
            });
        });
        
        await Promise.all(loadPromises);
        console.log(`Caricamento completato: ${this.artworks.length} opere caricate`);
        
        // Rimuovi gli skeleton ora che le immagini sono caricate
        setTimeout(() => {
            this.removeSkeletonFrames();
        }, 500); // Piccolo delay per transizione più fluida
    }

    createSkeletonFrames() {
        console.log('Creando 9 skeleton frames placeholder (3 per parete)...');
        
        // Array per tenere traccia degli skeleton
        this.skeletonFrames = [];
        
        // Dimensioni standard per gli skeleton
        const skeletonWidth = 1.0;
        const skeletonHeight = 1.2;
        
        // 3 skeleton per ogni parete (fondo, sinistra, destra)
        const skeletonPositions = [
            // Parete di fondo (3 skeleton)
            { x: -3, y: 2, z: -4.99, rotation: 0, wall: 'fondo-sx' },
            { x: 0, y: 2, z: -4.99, rotation: 0, wall: 'fondo-centro' },
            { x: 3, y: 2, z: -4.99, rotation: 0, wall: 'fondo-dx' },
            
            // Parete sinistra (3 skeleton)
            { x: -4.99, y: 2, z: -2, rotation: Math.PI/2, wall: 'sinistra-1' },
            { x: -4.99, y: 2, z: 0, rotation: Math.PI/2, wall: 'sinistra-2' },
            { x: -4.99, y: 2, z: 2, rotation: Math.PI/2, wall: 'sinistra-3' },
            
            // Parete destra (3 skeleton)
            { x: 4.99, y: 2, z: -2, rotation: -Math.PI/2, wall: 'destra-1' },
            { x: 4.99, y: 2, z: 0, rotation: -Math.PI/2, wall: 'destra-2' },
            { x: 4.99, y: 2, z: 2, rotation: -Math.PI/2, wall: 'destra-3' }
        ];
        
        skeletonPositions.forEach((pos, index) => {
            const skeleton = this.createSkeletonFrame(skeletonWidth, skeletonHeight, pos, index);
            this.skeletonFrames.push(skeleton);
            this.scene.add(skeleton);
        });
        
        console.log('✅ Creati', skeletonPositions.length, 'skeleton frames (verranno sostituiti dalle immagini)');
    }

    removeSkeletonFrames() {
        console.log('Rimozione skeleton frames...');
        if (this.skeletonFrames && this.skeletonFrames.length > 0) {
            this.skeletonFrames.forEach(skeleton => {
                this.scene.remove(skeleton);
            });
            this.skeletonFrames = [];
            console.log('✅ Skeleton frames rimossi');
        }
    }

    createSkeletonFrame(width, height, position, index) {
        const group = new THREE.Group();
        
        // Cornice grigia visibile per testare il posizionamento
        const frameThickness = 0.08;
        const frameDepth = 0.03;
        
        // Cornice superiore
        const topFrame = new THREE.Mesh(
            new THREE.BoxGeometry(width + frameThickness * 2, frameThickness, frameDepth),
            new THREE.MeshLambertMaterial({ color: 0x888888 })
        );
        topFrame.position.y = height / 2 + frameThickness / 2;
        group.add(topFrame);
        
        // Cornice inferiore
        const bottomFrame = new THREE.Mesh(
            new THREE.BoxGeometry(width + frameThickness * 2, frameThickness, frameDepth),
            new THREE.MeshLambertMaterial({ color: 0x888888 })
        );
        bottomFrame.position.y = -height / 2 - frameThickness / 2;
        group.add(bottomFrame);
        
        // Cornice sinistra
        const leftFrame = new THREE.Mesh(
            new THREE.BoxGeometry(frameThickness, height, frameDepth),
            new THREE.MeshLambertMaterial({ color: 0x888888 })
        );
        leftFrame.position.x = -width / 2 - frameThickness / 2;
        group.add(leftFrame);
        
        // Cornice destra
        const rightFrame = new THREE.Mesh(
            new THREE.BoxGeometry(frameThickness, height, frameDepth),
            new THREE.MeshLambertMaterial({ color: 0x888888 })
        );
        rightFrame.position.x = width / 2 + frameThickness / 2;
        group.add(rightFrame);
        
        // Piano interno grigio chiaro (dove andrà l'immagine)
        const innerPlane = new THREE.Mesh(
            new THREE.PlaneGeometry(width, height),
            new THREE.MeshLambertMaterial({ color: 0xdddddd })
        );
        innerPlane.position.z = 0.01;
        group.add(innerPlane);
        
        // Marker colorato per identificare facilmente ogni skeleton
        const colors = [0xff0000, 0x00ff00, 0x0000ff, 0xffff00, 0xff00ff, 0x00ffff, 0xff8800, 0x8800ff, 0x00ff88];
        const marker = new THREE.Mesh(
            new THREE.BoxGeometry(0.15, 0.15, 0.02),
            new THREE.MeshLambertMaterial({ color: colors[index % colors.length] })
        );
        marker.position.y = -height / 2 - 0.25;
        marker.position.z = 0.02;
        group.add(marker);
        
        // Posizionamento finale
        group.position.set(position.x, position.y, position.z);
        group.rotation.y = position.rotation;
        
        console.log(`  Skeleton ${index} [${position.wall}] at x:${position.x.toFixed(2)} y:${position.y} z:${position.z.toFixed(2)} rot:${(position.rotation * 180 / Math.PI).toFixed(0)}°`);
        
        return group;
    }

    createArtwork(texture, position, imageData) {
        const group = new THREE.Group();
        
        // Dimensioni proporzionali all'immagine
        const aspectRatio = texture.image.width / texture.image.height;
        const baseWidth = 1.2;
        const width = baseWidth;
        const height = baseWidth / aspectRatio;

        // CORNICE DORATA ELEGANTE (stile galleria classica)
        const frameThickness = 0.08; // Spessore cornice
        const frameDepth = 0.06; // Profondità 3D
        
        // Materiale cornice dorata con effetto legno/oro
        const frameMaterial = new THREE.MeshStandardMaterial({ 
            color: 0xd4af37, // Oro elegante
            metalness: 0.6,
            roughness: 0.3,
            emissive: 0x8b7355, // Leggero bagliore bronzo
            emissiveIntensity: 0.2
        });

        // Lato superiore cornice
        const frameTop = new THREE.Mesh(
            new THREE.BoxGeometry(width + frameThickness * 2, frameThickness, frameDepth),
            frameMaterial
        );
        frameTop.position.set(0, height/2 + frameThickness/2, frameDepth/2);
        frameTop.castShadow = true;
        group.add(frameTop);

        // Lato inferiore cornice
        const frameBottom = new THREE.Mesh(
            new THREE.BoxGeometry(width + frameThickness * 2, frameThickness, frameDepth),
            frameMaterial
        );
        frameBottom.position.set(0, -height/2 - frameThickness/2, frameDepth/2);
        frameBottom.castShadow = true;
        group.add(frameBottom);

        // Lato sinistro cornice
        const frameLeft = new THREE.Mesh(
            new THREE.BoxGeometry(frameThickness, height, frameDepth),
            frameMaterial
        );
        frameLeft.position.set(-width/2 - frameThickness/2, 0, frameDepth/2);
        frameLeft.castShadow = true;
        group.add(frameLeft);

        // Lato destro cornice
        const frameRight = new THREE.Mesh(
            new THREE.BoxGeometry(frameThickness, height, frameDepth),
            frameMaterial
        );
        frameRight.position.set(width/2 + frameThickness/2, 0, frameDepth/2);
        frameRight.castShadow = true;
        group.add(frameRight);

        // Quadro stesso
        const artworkGeometry = new THREE.PlaneGeometry(width, height);
        const artworkMaterial = new THREE.MeshStandardMaterial({ 
            map: texture,
            transparent: false,
            roughness: 0.7,
            metalness: 0.0
        });
        const artwork = new THREE.Mesh(artworkGeometry, artworkMaterial);
        artwork.position.z = 0.01; // Leggermente arretrato rispetto alla cornice
        artwork.userData = imageData;
        group.add(artwork);

        // Rimuoviamo il vetro per evitare effetti di sospensione

        // Posizionamento finale
        group.position.set(position.x, position.y, position.z);
        group.rotation.y = position.rotation;

        // Nessuna illuminazione spot per evitare ombre che creano effetto "sospeso"

        return { group, artwork, imageData };
    }

    setupLighting() {
        // Illuminazione da galleria d'arte - ATMOSFERA CALDA E ACCOGLIENTE
        
        // Luce ambientale GIALLA/CALDA (invece di bianca fredda)
        const ambientLight = new THREE.AmbientLight(0xfff4e0, 1.5); // Giallo caldo
        this.scene.add(ambientLight);

        // Luce principale dall'alto - TONALITÀ CALDA
        const mainLight = new THREE.DirectionalLight(0xffe9c5, 1.3); // Arancio chiaro
        mainLight.position.set(0, 10, 0);
        mainLight.castShadow = true;
        mainLight.shadow.mapSize.width = 2048;
        mainLight.shadow.mapSize.height = 2048;
        mainLight.shadow.camera.near = 0.1;
        mainLight.shadow.camera.far = 50;
        mainLight.shadow.camera.left = -10;
        mainLight.shadow.camera.right = 10;
        mainLight.shadow.camera.top = 10;
        mainLight.shadow.camera.bottom = -10;
        this.scene.add(mainLight);

        // Luci supplementari DORATE
        const fillLight1 = new THREE.DirectionalLight(0xffd700, 0.7); // Oro
        fillLight1.position.set(5, 5, 5);
        this.scene.add(fillLight1);

        const fillLight2 = new THREE.DirectionalLight(0xffd700, 0.7); // Oro
        fillLight2.position.set(-5, 5, -5);
        this.scene.add(fillLight2);
        
        // Luce laterale AMBRATA
        const sideLight1 = new THREE.DirectionalLight(0xffcc80, 0.6); // Ambra
        sideLight1.position.set(-10, 2, 0);
        this.scene.add(sideLight1);
        
        const sideLight2 = new THREE.DirectionalLight(0xffcc80, 0.6); // Ambra
        sideLight2.position.set(10, 2, 0);
        this.scene.add(sideLight2);

        // Luce dal pavimento CALDA
        const floorLight = new THREE.DirectionalLight(0xffebcd, 0.4); // Beige caldo
        floorLight.position.set(0, -1, 0);
        this.scene.add(floorLight);
        
        // Luci di sfondo DORATE
        const backLight1 = new THREE.DirectionalLight(0xffa500, 0.5); // Arancione
        backLight1.position.set(0, 5, -10);
        this.scene.add(backLight1);
        
        const backLight2 = new THREE.DirectionalLight(0xffa500, 0.5); // Arancione
        backLight2.position.set(0, 5, 10);
        this.scene.add(backLight2);
        
        // NIENTE FOG - mantiene la scena nitida senza ombre scure
    }

    setupControls() {
        // Implementiamo i controlli camera manualmente per maggiore affidabilità
        this.isMouseDown = false;
        this.previousMousePosition = { x: 0, y: 0 };
        this.cameraDistance = 5;
        this.cameraAngleX = 0;
        this.cameraAngleY = 0;
        this.targetPosition = { x: 0, y: 1.6, z: 0 };
        
        // Posiziona la camera iniziale
        this.updateCameraPosition();
    }

    updateCameraPosition() {
        // Limita gli angoli verticali (non guardare troppo in alto o in basso)
        this.cameraAngleY = Math.max(-Math.PI/4, Math.min(Math.PI/4, this.cameraAngleY));
        
        // Limita la distanza (zoom)
        this.cameraDistance = Math.max(2, Math.min(10, this.cameraDistance));
        
        // Calcola la posizione della camera
        const x = this.targetPosition.x + this.cameraDistance * Math.sin(this.cameraAngleX) * Math.cos(this.cameraAngleY);
        const y = this.targetPosition.y + this.cameraDistance * Math.sin(this.cameraAngleY);
        const z = this.targetPosition.z + this.cameraDistance * Math.cos(this.cameraAngleX) * Math.cos(this.cameraAngleY);
        
        // BLOCCO VISUALE: Limita la camera dentro i confini della stanza
        // Stanza: 10x10 metri, centro (0,0,0)
        const roomSize = 10;
        const boundary = roomSize / 2 - 0.5; // Margine di 0.5m dalle pareti
        
        // Limita posizione X (pareti laterali)
        const clampedX = Math.max(-boundary, Math.min(boundary, x));
        
        // Limita posizione Z (parete anteriore/posteriore)
        const clampedZ = Math.max(-boundary, Math.min(boundary, z));
        
        // Limita altezza (pavimento e soffitto)
        const clampedY = Math.max(0.5, Math.min(3.5, y)); // Da 0.5m a 3.5m
        
        // Applica posizione limitata
        this.camera.position.set(clampedX, clampedY, clampedZ);
        
        // Assicurati di guardare sempre verso il centro/opera
        this.camera.lookAt(this.targetPosition.x, this.targetPosition.y, this.targetPosition.z);
    }

    setupEventListeners() {
        // Resize
        window.addEventListener('resize', () => this.onWindowResize());
        
        // Mouse interaction
        if (this.canvas) {
            console.log('✅ Aggiungendo event listeners al canvas');
            console.log('Canvas element:', this.canvas);
            console.log('Canvas z-index:', window.getComputedStyle(this.canvas).zIndex);
            console.log('Canvas pointer-events:', window.getComputedStyle(this.canvas).pointerEvents);
            
            this.canvas.addEventListener('mousedown', (event) => this.onMouseDown(event));
            this.canvas.addEventListener('mousemove', (event) => this.onMouseMove(event));
            this.canvas.addEventListener('mouseup', (event) => this.onMouseUp(event));
            this.canvas.addEventListener('wheel', (event) => this.onMouseWheel(event));
            this.canvas.addEventListener('click', (event) => {
                console.log('🎯 CLICK RICEVUTO SUL CANVAS!');
                this.onMouseClick(event);
            });
            
            // Test: aggiungi stile visivo per debug
            this.canvas.style.cursor = 'grab';
        } else {
            console.error('❌ Canvas non trovato per gli event listeners!');
        }

        // Tour guidato
        const tourButton = document.getElementById('start-gallery-tour');
        if (tourButton) {
            console.log('Tour button trovato, aggiungendo listener');
            tourButton.addEventListener('click', () => {
                console.log('Tour button cliccato!');
                this.startGalleryTour();
            });
        } else {
            console.log('Tour button non trovato');
        }

        // Audio toggle
        const audioButton = document.getElementById('toggle-ambient-sound');
        if (audioButton) {
            console.log('Audio button trovato');
            audioButton.addEventListener('click', () => this.toggleAmbientSound());
        } else {
            console.log('Audio button non trovato');
        }
        
        // 3D Mode toggle (con fallback robusto)
        const toggle3DButton = document.getElementById('toggle-3d-mode');
        if (toggle3DButton) {
            console.log('✅ 3D toggle button trovato');
            toggle3DButton.addEventListener('click', (e) => {
                e.preventDefault();
                console.log('🎮 3D toggle cliccato!');
                this.toggle3DMode();
            });
        } else {
            console.log('⚠️ 3D toggle button non trovato, configurazione fallback...');
            
            // Fallback: cerca per classe o testo
            setTimeout(() => {
                const fallbackButton = document.querySelector('.btn-3d, [data-original-text="Esplora in 3D"], .btn-gold');
                if (fallbackButton) {
                    console.log('✅ Pulsante 3D trovato con fallback');
                    fallbackButton.addEventListener('click', (e) => {
                        e.preventDefault();
                        console.log('🎮 3D toggle (fallback) cliccato!');
                        this.toggle3DMode();
                    });
                } else {
                    console.log('❌ Nessun pulsante 3D trovato');
                }
            }, 1000);
        }
    }

    onMouseClick(event) {
        console.log('Click rilevato sul canvas!');
        
        if (!this.canvas || !this.raycaster || !this.mouse || this.isTransitioning) return;
        
        const rect = this.canvas.getBoundingClientRect();
        this.mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
        this.mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;

        this.raycaster.setFromCamera(this.mouse, this.camera);
        
        const intersects = this.raycaster.intersectObjects(
            this.artworks.map(artwork => artwork.artwork)
        );
        
        if (intersects.length > 0) {
            console.log('Quadro cliccato!');
            const clickedArtwork = intersects[0].object;
            
            // Trova l'artwork completo dall'array
            const artworkData = this.artworks.find(art => art.artwork === clickedArtwork);
            
            if (artworkData) {
                // Se clicco lo stesso quadro già in focus, torno alla vista generale
                if (this.focusedArtwork === artworkData) {
                    console.log('Stesso quadro - torno alla vista generale');
                    this.returnToGeneralView();
                } else {
                    console.log('Nuovo quadro - zoom in');
                    this.focusOnArtwork(artworkData);
                }
            }
        } else {
            console.log('Click su area vuota - torno alla vista generale');
            this.returnToGeneralView();
        }
    }

    returnToGeneralView() {
        if (!this.focusedArtwork) return; // Già in vista generale
        
        console.log('Ritorno alla vista generale');
        this.focusedArtwork = null;
        
        // Torna alla posizione iniziale
        const generalPosition = { x: 0, y: 1.6, z: 5 };
        const lookAtCenter = { x: 0, y: 1.6, z: 0 };
        
        this.smoothCameraTransition(generalPosition, lookAtCenter, () => {
            // Reset variabili controllo dopo la transizione
            this.cameraDistance = 5;
            this.cameraAngleX = 0;
            this.cameraAngleY = 0;
            this.targetPosition = { x: 0, y: 1.6, z: 0 };
        });
    }

    focusOnArtwork(artworkData) {
        console.log('Avvicinamento al quadro:', artworkData.imageData.title);
        
        this.focusedArtwork = artworkData; // Traccia quale quadro è in focus
        
        const artwork = artworkData.group;
        const position = artwork.position;
        const rotation = artwork.rotation.y;
        
        // Distanza ottimale per vedere il quadro da vicino
        const distance = 1.5;
        const viewHeight = position.y; // Mantieni l'altezza del quadro
        
        // Calcola posizione frontale al quadro basata sulla rotazione
        let cameraPosition;
        
        if (Math.abs(rotation) < 0.1) {
            // Parete di fondo (rotation ≈ 0)
            cameraPosition = {
                x: position.x,
                y: viewHeight,
                z: position.z + distance
            };
        } else if (rotation > 1.4 && rotation < 1.8) {
            // Parete sinistra (rotation ≈ π/2)
            cameraPosition = {
                x: position.x + distance,
                y: viewHeight,
                z: position.z
            };
        } else if (rotation < -1.4 && rotation > -1.8) {
            // Parete destra (rotation ≈ -π/2)
            cameraPosition = {
                x: position.x - distance,
                y: viewHeight,
                z: position.z
            };
        } else {
            // Fallback generico per qualsiasi angolo
            cameraPosition = {
                x: position.x + distance * Math.sin(rotation + Math.PI),
                y: viewHeight,
                z: position.z + distance * Math.cos(rotation + Math.PI)
            };
        }
        
        console.log('Posizione camera:', cameraPosition);
        console.log('Target quadro:', position);
        
        // Aggiorna sistema di controllo
        this.targetPosition = { x: position.x, y: position.y, z: position.z };
        
        // Anima movimento fluido con callback
        this.smoothCameraTransition(cameraPosition, position, () => {
            console.log('✅ Avvicinamento completato! Puoi ammirare l\'opera da vicino');
            
            // Aggiorna variabili di controllo per mantenere la posizione
            const dist = Math.sqrt(
                Math.pow(cameraPosition.x - position.x, 2) +
                Math.pow(cameraPosition.y - position.y, 2) +
                Math.pow(cameraPosition.z - position.z, 2)
            );
            this.cameraDistance = dist;
        });
    }

    smoothCameraTransition(targetPos, lookAtPos, onComplete = null) {
        this.isTransitioning = true; // Blocca input durante l'animazione
        
        const startPos = {
            x: this.camera.position.x,
            y: this.camera.position.y,
            z: this.camera.position.z
        };
        
        const duration = 1500; // 1.5 secondi
        const startTime = Date.now();
        
        const animate = () => {
            const elapsed = Date.now() - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Easing function per movimento più naturale (ease-in-out cubic)
            const eased = progress < 0.5
                ? 4 * progress * progress * progress
                : 1 - Math.pow(-2 * progress + 2, 3) / 2;
            
            // Interpola posizione camera
            this.camera.position.x = startPos.x + (targetPos.x - startPos.x) * eased;
            this.camera.position.y = startPos.y + (targetPos.y - startPos.y) * eased;
            this.camera.position.z = startPos.z + (targetPos.z - startPos.z) * eased;
            
            // Guarda sempre il punto target durante l'animazione
            this.camera.lookAt(lookAtPos.x, lookAtPos.y, lookAtPos.z);
            
            if (progress < 1) {
                requestAnimationFrame(animate);
            } else {
                this.isTransitioning = false; // Riabilita input
                console.log('Transizione camera completata');
                
                // Chiama callback se fornito
                if (onComplete && typeof onComplete === 'function') {
                    onComplete();
                }
            }
        };
        
        animate();
    }

    onMouseDown(event) {
        console.log('Mouse down rilevato!');
        this.isMouseDown = true;
        this.previousMousePosition = {
            x: event.clientX,
            y: event.clientY
        };
        if (this.canvas) {
            this.canvas.style.cursor = 'grabbing';
        }
    }

    onMouseUp(event) {
        console.log('Mouse up rilevato!');
        this.isMouseDown = false;
        if (this.canvas) {
            this.canvas.style.cursor = 'grab';
        }
    }

    onMouseMove(event) {
        if (!this.isMouseDown) {
            // Aggiorna solo il cursore per gli hover sui quadri
            if (this.canvas && this.raycaster && this.mouse) {
                const rect = this.canvas.getBoundingClientRect();
                this.mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
                this.mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;

                this.raycaster.setFromCamera(this.mouse, this.camera);
                
                const intersects = this.raycaster.intersectObjects(
                    this.artworks.map(artwork => artwork.artwork)
                );

                this.canvas.style.cursor = intersects.length > 0 ? 'pointer' : 'grab';
            }
            return;
        }

        // Se sono in focus su un quadro, disabilita la rotazione libera
        if (this.focusedArtwork) {
            return;
        }

        console.log('Mouse move durante drag - deltaX:', event.clientX - this.previousMousePosition.x);
        
        // Controllo camera
        const deltaX = event.clientX - this.previousMousePosition.x;
        const deltaY = event.clientY - this.previousMousePosition.y;

        this.cameraAngleX -= deltaX * 0.01;
        this.cameraAngleY -= deltaY * 0.01;
        
        // LIMITA ROTAZIONE: impedisce di voltarsi fuori dalla stanza
        // Limita rotazione orizzontale a ±135° (3/4 di cerchio)
        const maxAngle = Math.PI * 0.75; // 135 gradi
        this.cameraAngleX = Math.max(-maxAngle, Math.min(maxAngle, this.cameraAngleX));

        this.updateCameraPosition();

        this.previousMousePosition = {
            x: event.clientX,
            y: event.clientY
        };
    }

    onMouseWheel(event) {
        event.preventDefault();
        
        // Se sono in focus su un quadro, limita lo zoom
        if (this.focusedArtwork) {
            // Permetti solo piccoli aggiustamenti di zoom
            this.cameraDistance += event.deltaY * 0.005;
            this.cameraDistance = Math.max(0.8, Math.min(2.5, this.cameraDistance)); // Limita range
        } else {
            // Zoom libero in vista generale
            this.cameraDistance += event.deltaY * 0.01;
        }
        
        this.updateCameraPosition();
    }

    startGalleryTour() {
        if (this.tourActive) return;
        
        this.tourActive = true;
        this.tourStep = 0;
        
        const tourButton = document.getElementById('start-gallery-tour');
        if (tourButton) {
            tourButton.innerHTML = '<i class="fas fa-stop"></i> <span>Ferma Tour</span>';
            tourButton.onclick = () => this.stopGalleryTour();
        }

        this.nextTourStep();
    }

    nextTourStep() {
        if (!this.tourActive || this.tourStep >= this.artworks.length) {
            this.stopGalleryTour();
            return;
        }

        const artwork = this.artworks[this.tourStep];
        if (artwork) {
            const position = artwork.group.position;
            const rotation = artwork.group.rotation.y;
            
            // Distanza ottimale per il tour
            const distance = 2.0;
            const viewHeight = position.y;
            
            // Calcola posizione FRONTALE al quadro (stessa logica di focusOnArtwork)
            let cameraPos;
            
            if (Math.abs(rotation) < 0.1) {
                // Parete di fondo (rotation ≈ 0)
                cameraPos = {
                    x: position.x,
                    y: viewHeight,
                    z: position.z + distance
                };
            } else if (rotation > 1.4 && rotation < 1.8) {
                // Parete sinistra (rotation ≈ π/2)
                cameraPos = {
                    x: position.x + distance,
                    y: viewHeight,
                    z: position.z
                };
            } else if (rotation < -1.4 && rotation > -1.8) {
                // Parete destra (rotation ≈ -π/2)
                cameraPos = {
                    x: position.x - distance,
                    y: viewHeight,
                    z: position.z
                };
            } else {
                // Fallback generico
                cameraPos = {
                    x: position.x - distance * Math.sin(rotation),
                    y: viewHeight,
                    z: position.z - distance * Math.cos(rotation)
                };
            }
            
            console.log(`Tour step ${this.tourStep + 1}: Quadro a rot:${(rotation * 180 / Math.PI).toFixed(0)}° - Camera: x:${cameraPos.x.toFixed(2)} y:${cameraPos.y.toFixed(2)} z:${cameraPos.z.toFixed(2)}`);
            
            this.animateCameraTo(cameraPos, position);
            
            setTimeout(() => {
                this.tourStep++;
                this.nextTourStep();
            }, 4000);
        }
    }

    animateCameraTo(position, target) {
        const startPos = {
            x: this.camera.position.x,
            y: this.camera.position.y,
            z: this.camera.position.z
        };
        
        const startTime = Date.now();
        const duration = 2000;

        const animate = () => {
            const elapsed = Date.now() - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const eased = this.easeInOutCubic(progress);

            this.camera.position.x = startPos.x + (position.x - startPos.x) * eased;
            this.camera.position.y = startPos.y + (position.y - startPos.y) * eased;
            this.camera.position.z = startPos.z + (position.z - startPos.z) * eased;
            
            this.camera.lookAt(target.x, target.y, target.z);

            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        };

        animate();
    }

    easeInOutCubic(t) {
        return t < 0.5 ? 4 * t * t * t : (t - 1) * (2 * t - 2) * (2 * t - 2) + 1;
    }

    stopGalleryTour() {
        this.tourActive = false;
        this.tourStep = 0;
        
        const tourButton = document.getElementById('start-gallery-tour');
        if (tourButton) {
            tourButton.innerHTML = '<i class="fas fa-route"></i> <span>Tour Guidato</span>';
            tourButton.onclick = () => this.startGalleryTour();
        }
    }

    setupAudio() {
        // Inizializza il sistema audio
        this.audioContext = null;
        this.ambientSound = null;
        this.isAudioPlaying = false;
        
        // Crea l'audio solo dopo l'interazione dell'utente (requisito browser)
        this.audioInitialized = false;
    }

    async initAudio() {
        if (this.audioInitialized) return;
        
        try {
            // Crea AudioContext
            this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
            
            // Crea i suoni ambientali sintetici (più affidabile dei file audio)
            this.createAmbientSounds();
            
            this.audioInitialized = true;
            console.log('Audio inizializzato con successo');
        } catch (error) {
            console.warn('Errore inizializzazione audio:', error);
        }
    }

    createAmbientSounds() {
        if (!this.audioContext) return;

        // Oscillatore per sottofondo ambientale (suono profondo)
        this.baseOscillator = this.audioContext.createOscillator();
        this.baseGain = this.audioContext.createGain();
        
        this.baseOscillator.type = 'sine';
        this.baseOscillator.frequency.setValueAtTime(60, this.audioContext.currentTime);
        this.baseGain.gain.setValueAtTime(0.03, this.audioContext.currentTime);
        
        this.baseOscillator.connect(this.baseGain);
        this.baseGain.connect(this.audioContext.destination);
        
        // Oscillatore per suoni di atmosfera (più acuto)
        this.ambientOscillator = this.audioContext.createOscillator();
        this.ambientGain = this.audioContext.createGain();
        
        this.ambientOscillator.type = 'triangle';
        this.ambientOscillator.frequency.setValueAtTime(220, this.audioContext.currentTime);
        this.ambientGain.gain.setValueAtTime(0.01, this.audioContext.currentTime);
        
        this.ambientOscillator.connect(this.ambientGain);
        this.ambientGain.connect(this.audioContext.destination);
        
        // Avvia le oscillazioni
        this.baseOscillator.start();
        this.ambientOscillator.start();
        
        // Crea variazioni nell'audio
        this.startAudioVariations();
    }

    startAudioVariations() {
        setInterval(() => {
            if (!this.isAudioPlaying || !this.audioContext) return;
            
            const now = this.audioContext.currentTime;
            
            // Varia leggermente la frequenza di base
            const baseFreq = 60 + Math.sin(now * 0.1) * 10;
            this.baseOscillator.frequency.setValueAtTime(baseFreq, now);
            
            // Varia l'audio ambientale
            const ambientFreq = 220 + Math.sin(now * 0.05) * 50;
            this.ambientOscillator.frequency.setValueAtTime(ambientFreq, now);
            
            // Varia leggermente il volume
            const ambientVolume = 0.01 + Math.sin(now * 0.03) * 0.005;
            this.ambientGain.gain.setValueAtTime(ambientVolume, now);
            
        }, 500);
    }

    async toggleAmbientSound() {
        const button = document.getElementById('toggle-ambient-sound');
        if (!button) return;

        button.classList.toggle('active');
        
        // Inizializza l'audio se non ancora fatto
        if (!this.audioInitialized) {
            await this.initAudio();
        }
        
        if (button.classList.contains('active')) {
            button.innerHTML = '<i class="fas fa-volume-mute"></i> <span>Suoni Ambientali</span>';
            this.startAmbientSound();
        } else {
            button.innerHTML = '<i class="fas fa-volume-up"></i> <span>Suoni Ambientali</span>';
            this.stopAmbientSound();
        }
    }

    startAmbientSound() {
        if (!this.audioContext) return;
        
        this.isAudioPlaying = true;
        
        // Resume AudioContext se è sospeso
        if (this.audioContext.state === 'suspended') {
            this.audioContext.resume();
        }
        
        // Fade in dell'audio
        const now = this.audioContext.currentTime;
        this.baseGain.gain.setValueAtTime(0, now);
        this.ambientGain.gain.setValueAtTime(0, now);
        
        this.baseGain.gain.linearRampToValueAtTime(0.03, now + 2);
        this.ambientGain.gain.linearRampToValueAtTime(0.01, now + 2);
        
        console.log('Suoni ambientali avviati');
    }

    stopAmbientSound() {
        if (!this.audioContext) return;
        
        this.isAudioPlaying = false;
        
        // Fade out dell'audio
        const now = this.audioContext.currentTime;
        this.baseGain.gain.linearRampToValueAtTime(0, now + 1);
        this.ambientGain.gain.linearRampToValueAtTime(0, now + 1);
        
        console.log('Suoni ambientali fermati');
    }
    
    toggle3DMode() {
        console.log('🎮 Toggle 3D Mode attivato!');
        
        const button = document.getElementById('toggle-3d-mode');
        const canvas = document.getElementById('gallery-3d-canvas'); // ID corretto
        
        if (!button) {
            console.error('❌ Pulsante toggle-3d-mode non trovato');
            return;
        }
        
        if (!canvas) {
            console.error('❌ Canvas gallery-3d-canvas non trovato');
            return;
        }
        
        // Determina se attualmente è in modalità 3D
        const isCurrently3D = canvas.style.display !== 'none' && this.renderer;
        
        if (isCurrently3D) {
            // 🖼️ PASSA A MODALITÀ 2D STILE CANVA
            console.log('🎨 Passaggio a modalità 2D stile Canva...');
            canvas.style.display = 'none';
            button.innerHTML = `
                <i class="fas fa-cube"></i>
                <span data-translatable="true" data-original-text="Esplora in 3D">Esplora in 3D</span>
            `;
            
            // Disattiva il rendering 3D
            if (this.animationId) {
                cancelAnimationFrame(this.animationId);
            }
            
            // Attiva modalità 2D stile Canva
            this.activate2DCanvaMode();
            
            // Riabilita effetti parallax di background
            this.enableParallaxEffects();
            
            // Attiva parallax tradizionale
            this.initParallaxOnly();
            
        } else {
            // 🎮 PASSA A MODALITÀ 3D ESPLORABILE
            console.log('🎮 Passaggio a modalità 3D esplorabile...');
            canvas.style.display = 'block';
            button.innerHTML = `
                <i class="fas fa-th-large"></i>
                <span data-translatable="true" data-original-text="Modalità Gallery">Modalità Gallery</span>
            `;
            
            // Disattiva modalità 2D Canva
            this.deactivate2DCanvaMode();
            
            // Disabilita effetti parallax per evitare overlay
            this.disableParallaxEffects();
            
            // Assicurati che il 3D sia inizializzato
            if (!this.renderer) {
                console.log('🔄 Inizializzazione 3D...');
                this.init3D();
            }
            
            // Riposiziona la camera alla posizione iniziale esplorabile
            this.cameraAngleX = 0;
            this.cameraAngleY = 0;
            this.cameraDistance = 5;
            this.targetPosition = { x: 0, y: 1.6, z: 0 };
            this.updateCameraPosition();
            
            // Riattiva il rendering se non è attivo
            if (!this.animationId) {
                this.animate();
            }
            
            console.log('✅ Modalità 3D esplorabile attivata!');
            console.log('🎯 Puoi ora:');
            console.log('  - Trascinare per ruotare la vista');
            console.log('  - Usare scroll per zoom');
            console.log('  - Cliccare sui quadri per focus');
        }
    }

    activate2DCanvaMode() {
        // Attiva la modalità 2D stile Canva
        console.log('🎨 Attivazione modalità 2D Canva...');
        
        // IMPORTANTE: Nascondi completamente il canvas 3D e rimuovi pointer events
        const canvas = document.getElementById('gallery-3d-canvas');
        if (canvas) {
            canvas.style.display = 'none';
            canvas.style.pointerEvents = 'none';
            canvas.style.zIndex = '-1';
        }
        
        // Nascondi anche il container 3D
        const canvas3DContainer = document.querySelector('.canvas-3d-container');
        if (canvas3DContainer) {
            canvas3DContainer.style.display = 'none';
            canvas3DContainer.style.pointerEvents = 'none';
        }
        
        // CRITICAL FIX: Mantieni scroll del body
        document.body.style.overflow = 'auto';
        document.documentElement.style.overflow = 'auto';
        
        const canvaGrid = document.querySelector('.canva-style-grid');
        if (canvaGrid) {
            canvaGrid.classList.add('active');
            canvaGrid.style.pointerEvents = 'auto';
            canvaGrid.style.zIndex = '1000';
            document.body.classList.add('canva-2d-active');
            console.log('✅ Modalità 2D Canva attivata (griglia esistente) - scroll libero');
        } else {
            // Se non esiste, creala
            this.initCanvaStyle2D();
            setTimeout(() => {
                const newGrid = document.querySelector('.canva-style-grid');
                if (newGrid) {
                    newGrid.classList.add('active');
                    newGrid.style.pointerEvents = 'auto';
                    newGrid.style.zIndex = '1000';
                    document.body.classList.add('canva-2d-active');
                    console.log('✅ Modalità 2D Canva creata e attivata');
                }
            }, 100);
        }
    }

    deactivate2DCanvaMode() {
        // Disattiva la modalità 2D stile Canva
        console.log('🎮 Disattivazione modalità 2D Canva...');
        
        const canvaGrid = document.querySelector('.canva-style-grid');
        if (canvaGrid) {
            canvaGrid.classList.remove('active');
            canvaGrid.style.pointerEvents = 'none';
            canvaGrid.style.zIndex = '-1';
        }
        
        // Riabilita il canvas 3D
        const canvas = document.getElementById('gallery-3d-canvas');
        if (canvas) {
            canvas.style.display = 'block';
            canvas.style.pointerEvents = 'auto';
            canvas.style.zIndex = '1';
        }
        
        // Riabilita il container 3D
        const canvas3DContainer = document.querySelector('.canvas-3d-container');
        if (canvas3DContainer) {
            canvas3DContainer.style.display = 'block';
            canvas3DContainer.style.pointerEvents = 'auto';
        }
        
        document.body.classList.remove('canva-2d-active');
        console.log('✅ Modalità 2D Canva disattivata');
    }

    onWindowResize() {
        if (!this.camera || !this.renderer) return;

        this.camera.aspect = this.canvas.clientWidth / this.canvas.clientHeight;
        this.camera.updateProjectionMatrix();
        this.renderer.setSize(this.canvas.clientWidth, this.canvas.clientHeight);
    }

    animate() {
        requestAnimationFrame(() => this.animate());

        // Rimuoviamo il controllo OrbitControls che non stiamo più usando
        
        // Animazioni delle luci
        const time = Date.now() * 0.001;
        this.scene.traverse((object) => {
            if (object instanceof THREE.PointLight) {
                object.intensity = 0.2 + Math.sin(time + object.position.x) * 0.1;
            }
        });

        // USA POST-PROCESSING se disponibile per rendering fotorealistico
        if (this.useComposer && this.composer) {
            this.composer.render();
        } else if (this.renderer) {
            this.renderer.render(this.scene, this.camera);
        }
    }

    hideLoading() {
        if (this.loadingScreen) {
            this.loadingScreen.classList.add('hidden');
            setTimeout(() => {
                this.loadingScreen.style.display = 'none';
            }, 500);
        }
        this.isLoading = false;
    }

    initParallaxOnly() {
        // Fallback: inizializza modalità 2D stile Canva
        this.initCanvaStyle2D();
        this.initParallaxEffects();
    }

    initCanvaStyle2D() {
        // Crea layout 2D VERO stile Canva.com con selezione stanze
        console.log('🎨 Inizializzazione modalità ROOMS stile Canva...');
        
        // Crea container per la selezione stanze
        this.createRoomSelectionInterface();
        
        // Inizializza il sistema di mockup
        this.initRoomMockupSystem();
    }

    createRoomSelectionInterface() {
        // Rimuovi eventuali container esistenti
        const existingGrid = document.querySelector('.canva-style-grid');
        if (existingGrid) existingGrid.remove();

        // Crea il container principale per selezione stanze
        const roomContainer = document.createElement('div');
        roomContainer.className = 'canva-style-grid';
        roomContainer.innerHTML = `
            <!-- Header Canva Style -->
            <div class="canva-header">
                <div class="canva-logo">
                    <i class="fas fa-palette"></i>
                    <span>Gallery Designer</span>
                </div>
                <div class="canva-breadcrumb">
                    <span class="breadcrumb-item active">Scegli Stanza</span>
                    <i class="fas fa-chevron-right"></i>
                    <span class="breadcrumb-item">Posiziona Arte</span>
                </div>
                <button class="btn-back-to-3d">
                    <i class="fas fa-cube"></i>
                    <span>Torna al 3D</span>
                </button>
            </div>

            <!-- Contenuto principale -->
            <div class="canva-main-content">
                
                <!-- Sezione selezione stanze -->
                <div class="room-selection-section active" id="room-selection">
                    <div class="section-header">
                        <h2>Scegli la tua galleria</h2>
                        <p>Seleziona l'ambiente perfetto per esporre le tue illustrazioni</p>
                    </div>

                    <div class="rooms-grid">
                        ${this.generateRoomOptions()}
                    </div>
                </div>

                <!-- Sezione design della stanza -->
                <div class="room-design-section" id="room-design">
                    <div class="design-interface">
                        <div class="room-preview">
                            <div class="room-mockup" id="selected-room-mockup">
                                <!-- Qui verrà caricato il mockup della stanza selezionata -->
                            </div>
                            <div class="room-controls">
                                <button class="btn-room-control" data-action="zoom-in">
                                    <i class="fas fa-search-plus"></i>
                                </button>
                                <button class="btn-room-control" data-action="zoom-out">
                                    <i class="fas fa-search-minus"></i>
                                </button>
                                <button class="btn-room-control" data-action="center">
                                    <i class="fas fa-compress-arrows-alt"></i>
                                </button>
                            </div>
                        </div>

                        <div class="artwork-sidebar">
                            <div class="sidebar-header">
                                <h3>Le tue illustrazioni</h3>
                                <div class="artwork-search">
                                    <i class="fas fa-search"></i>
                                    <input type="text" placeholder="Cerca..." id="artwork-search">
                                </div>
                            </div>
                            <div class="artwork-list" id="artwork-list">
                                <!-- Le illustrazioni verranno caricate qui -->
                            </div>
                        </div>
                    </div>

                    <div class="design-toolbar">
                        <button class="btn-toolbar" id="btn-back-to-rooms">
                            <i class="fas fa-arrow-left"></i>
                            <span>Cambia Stanza</span>
                        </button>
                        <div class="toolbar-center">
                            <button class="btn-toolbar active" data-tool="select">
                                <i class="fas fa-mouse-pointer"></i>
                                <span>Seleziona</span>
                            </button>
                            <button class="btn-toolbar" data-tool="move">
                                <i class="fas fa-arrows-alt"></i>
                                <span>Sposta</span>
                            </button>
                            <button class="btn-toolbar" data-tool="resize">
                                <i class="fas fa-expand-arrows-alt"></i>
                                <span>Ridimensiona</span>
                            </button>
                        </div>
                        <button class="btn-toolbar btn-primary" id="btn-save-design">
                            <i class="fas fa-download"></i>
                            <span>Salva Design</span>
                        </button>
                    </div>
                </div>

            </div>
        `;

        // Inserisci nel DOM
        const heroContent = document.querySelector('.hero-3d-content');
        if (heroContent) {
            heroContent.appendChild(roomContainer);
        }

        console.log('✅ Interfaccia selezione stanze creata');
    }

    initRoomMockupSystem() {
        // Sistema di mockup delle stanze con posizionamento artwork
        console.log('🏛️ Inizializzazione sistema mockup stanze...');
        
        // Event listeners per selezione stanze
        this.setupRoomSelectionEvents();
        
        // Sistema drag & drop per artwork
        this.setupArtworkDragDrop();
        
        // Controlli della toolbar
        this.setupDesignToolbar();
    }

    setupRoomSelectionEvents() {
        // CRITICAL: Aspetta che il DOM sia pronto
        setTimeout(() => {
            const roomCards = document.querySelectorAll('.room-card');
            console.log(`🔍 Trovate ${roomCards.length} room cards`);
            
            roomCards.forEach(card => {
                // Hover effects
                card.addEventListener('mouseenter', () => {
                    const overlay = card.querySelector('.room-overlay');
                    if (overlay) {
                        overlay.style.opacity = '1';
                        card.style.transform = 'translateY(-5px)';
                    }
                });

                card.addEventListener('mouseleave', () => {
                    const overlay = card.querySelector('.room-overlay');
                    if (overlay) {
                        overlay.style.opacity = '0';
                        card.style.transform = 'translateY(0)';
                    }
                });

                // Selezione stanza - CRITICAL FIX
                const selectBtn = card.querySelector('.btn-select-room');
                if (selectBtn) {
                    selectBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        const roomId = card.dataset.roomId;
                        console.log(`🎯 Click su stanza: ${roomId}`);
                        this.selectRoom(roomId);
                    });
                } else {
                    console.warn('⚠️ btn-select-room non trovato in card:', card);
                }
            });

            // Pulsante ritorna a 3D
            const backTo3DBtn = document.querySelector('.btn-back-to-3d');
            if (backTo3DBtn) {
                backTo3DBtn.addEventListener('click', () => {
                    this.toggle3DMode();
                });
            }
            
            console.log('✅ Event listeners setup completato');
        }, 500); // Aspetta che il DOM sia completamente renderizzato
    }

    selectRoom(roomId) {
        console.log(`🏛️ Stanza selezionata: ${roomId}`);
        
        // Nascondi sezione selezione
        const roomSelection = document.getElementById('room-selection');
        const roomDesign = document.getElementById('room-design');
        
        if (!roomSelection || !roomDesign) {
            console.error('❌ Sezioni room non trovate, ricreazione...');
            // Ricrea l'interfaccia se mancante
            this.createRoomSelectionInterface();
            return;
        }
        
        roomSelection.classList.remove('active');
        roomDesign.classList.add('active');
        
        // Aggiorna breadcrumb
        this.updateBreadcrumb('design');
        
        // Carica mockup della stanza
        this.loadRoomMockup(roomId);
        
        // Carica artwork disponibili + File Upload
        this.loadArtworkForDesign();
        // Sistema di upload rimosso per usare solo artwork admin
        console.log('📁 Sistema admin artwork attivo');
        
        // Setup area design
        this.setupDesignArea(roomId);
        
        console.log(`✅ Transizione alla stanza ${roomId} completata`);
    }

    generateRoomOptions() {
        // Definisci le stanze disponibili (come in Canva)
        const rooms = [
            {
                id: 'modern-gallery',
                name: 'Galleria Moderna',
                description: 'Spazio minimalista con pareti bianche',
                thumbnail: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjhmOWZhIi8+PHJlY3QgeD0iNTAiIHk9IjUwIiB3aWR0aD0iMjAwIiBoZWlnaHQ9IjEwMCIgZmlsbD0iI2U5ZWNlZiIgc3Ryb2tlPSIjZGVlMmU2IiBzdHJva2Utd2lkdGg9IjIiLz48L3N2Zz4=',
                popular: true
            },
            {
                id: 'classic-salon',
                name: 'Salone Classico',
                description: 'Ambiente elegante con cornici dorate',
                thumbnail: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjVmM2VmIi8+PHJlY3QgeD0iNDAiIHk9IjQwIiB3aWR0aD0iMjIwIiBoZWlnaHQ9IjEyMCIgZmlsbD0iI2M5YjA1ZiIgc3Ryb2tlPSIjYjhhMDUwIiBzdHJva2Utd2lkdGg9IjMiLz48L3N2Zz4=',
                popular: false
            },
            {
                id: 'industrial-loft',
                name: 'Loft Industriale',
                description: 'Stile urbano con mattoni e metallo',
                thumbnail: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjNDM0MzQzIi8+PHJlY3QgeD0iNjAiIHk9IjYwIiB3aWR0aD0iMTgwIiBoZWlnaHQ9IjgwIiBmaWxsPSIjNmM3NTdkIiBzdHJva2U9IiM0OTUwNTciIHN0cm9rZS13aWR0aD0iMiIvPjwvc3ZnPg==',
                popular: false
            },
            {
                id: 'contemporary-space',
                name: 'Spazio Contemporaneo',
                description: 'Design moderno con illuminazione LED',
                thumbnail: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZGVmcz48bGluZWFyR3JhZGllbnQgaWQ9ImciIHgxPSIwJSIgeTE9IjAlIiB4Mj0iMTAwJSIgeTI9IjEwMCUiPjxzdG9wIG9mZnNldD0iMCUiIHN0eWxlPSJzdG9wLWNvbG9yOiNmOGY5ZmE7Ii8+PHN0b3Agb2Zmc2V0PSIxMDAlIiBzdHlsZT0ic3RvcC1jb2xvcjojZTllY2VmOyIvPjwvbGluZWFyR3JhZGllbnQ+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZykiLz48cmVjdCB4PSI1MCIgeT0iNDAiIHdpZHRoPSIyMDAiIGhlaWdodD0iMTIwIiBmaWxsPSJub25lIiBzdHJva2U9IiNjOWIwNWYiIHN0cm9rZS13aWR0aD0iMyIgc3Ryb2tlLWRhc2hhcnJheT0iMTAsNSIvPjwvc3ZnPg==',
                popular: true
            }
        ];

        return rooms.map(room => `
            <div class="room-card ${room.popular ? 'popular' : ''}" data-room-id="${room.id}">
                ${room.popular ? '<div class="popular-badge"><i class="fas fa-star"></i> Popolare</div>' : ''}
                <div class="room-thumbnail">
                    <img src="${room.thumbnail}" alt="${room.name}">
                    <div class="room-overlay">
                        <button class="btn-select-room">
                            <i class="fas fa-paint-brush"></i>
                            Usa questa stanza
                        </button>
                    </div>
                </div>
                <div class="room-info">
                    <h3>${room.name}</h3>
                    <p>${room.description}</p>
                    <div class="room-features">
                        <span class="feature"><i class="fas fa-palette"></i> Personalizzabile</span>
                        <span class="feature"><i class="fas fa-download"></i> HD Export</span>
                    </div>
                </div>
            </div>
        `).join('');
    }

    create2DGridContainer() {
        // Rimuovi eventuali container esistenti
        const existingGrid = document.querySelector('.canva-style-grid');
        if (existingGrid) existingGrid.remove();

        // Crea il container principale
        const gridContainer = document.createElement('div');
        gridContainer.className = 'canva-style-grid';
        gridContainer.innerHTML = `
            <!-- Header con filtri stile Canva -->
            <div class="canva-header">
                <div class="canva-search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Cerca nelle tue illustrazioni..." id="canva-search">
                </div>
                <div class="canva-filters">
                    <button class="filter-btn active" data-filter="all">
                        <i class="fas fa-th"></i>
                        <span>Tutte</span>
                    </button>
                    <button class="filter-btn" data-filter="recent">
                        <i class="fas fa-clock"></i>
                        <span>Recenti</span>
                    </button>
                    <button class="filter-btn" data-filter="favorites">
                        <i class="fas fa-heart"></i>
                        <span>Preferite</span>
                    </button>
                </div>
                <div class="canva-view-options">
                    <button class="view-option active" data-view="grid">
                        <i class="fas fa-th-large"></i>
                    </button>
                    <button class="view-option" data-view="list">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>

            <!-- Griglia principale -->
            <div class="canva-grid-content">
                <div class="artworks-grid" id="canva-artworks-grid">
                    <!-- Le carte verranno generate dinamicamente -->
                </div>
            </div>

            <!-- Lightbox per preview -->
            <div class="canva-lightbox" id="canva-lightbox">
                <div class="lightbox-overlay"></div>
                <div class="lightbox-content">
                    <button class="lightbox-close">
                        <i class="fas fa-times"></i>
                    </button>
                    <div class="lightbox-image-container">
                        <img src="" alt="" id="lightbox-image">
                    </div>
                    <div class="lightbox-info">
                        <h3 id="lightbox-title"></h3>
                        <p id="lightbox-description"></p>
                        <div class="lightbox-actions">
                            <button class="btn-canva-primary">
                                <i class="fas fa-eye"></i>
                                Visualizza Dettagli
                            </button>
                            <button class="btn-canva-secondary">
                                <i class="fas fa-share-alt"></i>
                                Condividi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Inserisci nel DOM
        const heroContent = document.querySelector('.hero-3d-content');
        if (heroContent) {
            heroContent.appendChild(gridContainer);
        }

        console.log('✅ Container 2D stile Canva creato');
    }

    loadArtworkForDesign() {
        const artworkList = document.getElementById('artwork-list');
        if (!artworkList || !this.galleryData || !this.galleryData.images) return;
        
        artworkList.innerHTML = '';
        
        this.galleryData.images.forEach((artwork, index) => {
            const artworkItem = document.createElement('div');
            artworkItem.className = 'artwork-item';
            artworkItem.draggable = true;
            artworkItem.dataset.artworkId = artwork.id;
            artworkItem.dataset.artworkIndex = index;
            
            artworkItem.innerHTML = `
                <div class="artwork-thumb">
                    <img src="${artwork.medium || artwork.url}" alt="${artwork.title}">
                </div>
                <div class="artwork-info">
                    <h4>${artwork.title}</h4>
                    <span class="artwork-size">Originale</span>
                </div>
                <div class="artwork-actions">
                    <i class="fas fa-grip-vertical drag-handle"></i>
                </div>
            `;
            
            artworkList.appendChild(artworkItem);
        });
        
        console.log(`✅ Caricati ${this.galleryData.images.length} artwork per design`);
    }

    setupArtworkDragDrop() {
        // Event listeners per drag & drop
        document.addEventListener('dragstart', this.handleDragStart.bind(this));
        document.addEventListener('dragover', this.handleDragOver.bind(this));
        document.addEventListener('drop', this.handleDrop.bind(this));
        document.addEventListener('dragend', this.handleDragEnd.bind(this));
        
        console.log('✅ Sistema drag & drop inizializzato');
    }

    updateBreadcrumb(step) {
        const breadcrumbs = document.querySelectorAll('.breadcrumb-item');
        breadcrumbs.forEach((item, index) => {
            item.classList.remove('active');
            if ((step === 'rooms' && index === 0) || (step === 'design' && index === 1)) {
                item.classList.add('active');
            }
        });
    }

    loadRoomMockup(roomId) {
        const mockupContainer = document.getElementById('selected-room-mockup');
        if (!mockupContainer) {
            console.error('❌ Mockup container non trovato');
            return;
        }
        
        // Genera mockup SVG basato sull'ID stanza
        const mockupSVG = this.generateRoomMockupSVG(roomId);
        
        mockupContainer.innerHTML = `
            <div class="room-mockup-canvas" data-room="${roomId}">
                ${mockupSVG}
                <div class="artwork-drop-zones" id="artwork-zones">
                    <!-- Zone di drop per artwork verranno generate qui -->
                </div>
            </div>
        `;
        
        // Genera zone di drop
        this.generateArtworkDropZones(roomId);
    }

    generateRoomMockupSVG(roomId) {
        const mockups = {
            'modern-gallery': `
                <svg viewBox="0 0 800 600" xmlns="http://www.w3.org/2000/svg">
                    <!-- Pavimento -->
                    <rect width="800" height="150" y="450" fill="#f8f9fa"/>
                    
                    <!-- Parete centrale -->
                    <rect width="800" height="450" fill="linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%)"/>
                    
                    <!-- Battiscopa -->
                    <rect width="800" height="10" y="440" fill="#e9ecef"/>
                    
                    <!-- Illuminazione -->
                    <circle cx="200" cy="50" r="5" fill="#fff3cd" opacity="0.8"/>
                    <circle cx="400" cy="50" r="5" fill="#fff3cd" opacity="0.8"/>
                    <circle cx="600" cy="50" r="5" fill="#fff3cd" opacity="0.8"/>
                    
                    <!-- Pareti laterali (prospettiva) -->
                    <polygon points="0,0 100,80 100,520 0,450" fill="#f1f3f4"/>
                    <polygon points="800,0 700,80 700,520 800,450" fill="#f1f3f4"/>
                </svg>
            `,
            'classic-salon': `
                <svg viewBox="0 0 800 600" xmlns="http://www.w3.org/2000/svg">
                    <!-- Pavimento parquet -->
                    <rect width="800" height="150" y="450" fill="url(#parquet)"/>
                    
                    <!-- Parete con boiserie -->
                    <rect width="800" height="450" fill="#f5f3ef"/>
                    <rect width="800" height="150" y="300" fill="#e8dcc0"/>
                    
                    <!-- Cornice decorativa -->
                    <rect width="800" height="20" y="280" fill="#c9b05f"/>
                    
                    <!-- Lampadari -->
                    <g transform="translate(400,40)">
                        <ellipse cx="0" cy="0" rx="30" ry="15" fill="#ffd700"/>
                        <circle cx="0" cy="15" r="8" fill="#ffed4a"/>
                    </g>
                    
                    <defs>
                        <pattern id="parquet" x="0" y="0" width="40" height="20" patternUnits="userSpaceOnUse">
                            <rect width="40" height="20" fill="#8B4513"/>
                            <rect width="38" height="18" x="1" y="1" fill="#A0522D"/>
                        </pattern>
                    </defs>
                </svg>
            `,
            'industrial-loft': `
                <svg viewBox="0 0 800 600" xmlns="http://www.w3.org/2000/svg">
                    <!-- Pavimento cemento -->
                    <rect width="800" height="150" y="450" fill="#6c757d"/>
                    
                    <!-- Parete mattoni -->
                    <rect width="800" height="450" fill="url(#brick)"/>
                    
                    <!-- Travi metalliche -->
                    <rect width="800" height="15" y="40" fill="#495057"/>
                    <rect width="800" height="15" y="120" fill="#495057"/>
                    
                    <!-- Illuminazione industriale -->
                    <g transform="translate(200,45)">
                        <rect x="-15" y="0" width="30" height="40" fill="#343a40"/>
                        <ellipse cx="0" cy="40" rx="25" ry="10" fill="#fff3cd" opacity="0.6"/>
                    </g>
                    
                    <defs>
                        <pattern id="brick" x="0" y="0" width="80" height="40" patternUnits="userSpaceOnUse">
                            <rect width="80" height="40" fill="#8B4513"/>
                            <rect width="78" height="18" x="1" y="1" fill="#A0522D"/>
                            <rect width="78" height="18" x="1" y="21" fill="#CD853F"/>
                        </pattern>
                    </defs>
                </svg>
            `,
            'contemporary-space': `
                <svg viewBox="0 0 800 600" xmlns="http://www.w3.org/2000/svg">
                    <!-- Pavimento resina -->
                    <rect width="800" height="150" y="450" fill="url(#resin)"/>
                    
                    <!-- Parete con illuminazione LED -->
                    <rect width="800" height="450" fill="linear-gradient(90deg, #f8f9fa 0%, #ffffff 50%, #f8f9fa 100%)"/>
                    
                    <!-- Strisce LED -->
                    <rect width="800" height="3" y="100" fill="#00ff88" opacity="0.7"/>
                    <rect width="800" height="3" y="350" fill="#00ff88" opacity="0.7"/>
                    
                    <!-- Elementi geometrici -->
                    <polygon points="0,200 50,180 50,280 0,300" fill="#e9ecef"/>
                    <polygon points="800,200 750,180 750,280 800,300" fill="#e9ecef"/>
                    
                    <defs>
                        <pattern id="resin" x="0" y="0" width="100" height="100" patternUnits="userSpaceOnUse">
                            <rect width="100" height="100" fill="#dee2e6"/>
                            <circle cx="50" cy="50" r="20" fill="#ced4da" opacity="0.3"/>
                        </pattern>
                    </defs>
                </svg>
            `
        };
        
        return mockups[roomId] || mockups['modern-gallery'];
    }

    generateArtworkDropZones(roomId) {
        const zonesContainer = document.getElementById('artwork-zones');
        if (!zonesContainer) return;
        
        // CRITICAL: Pulisci container prima di generare nuove zone
        zonesContainer.innerHTML = '';
        
        // Zone predefinite per ogni tipo di stanza
        const zones = {
            'modern-gallery': [
                { x: 20, y: 25, width: 15, height: 20, id: 'zone-1' },
                { x: 40, y: 25, width: 20, height: 25, id: 'zone-2' },
                { x: 65, y: 25, width: 15, height: 20, id: 'zone-3' }
            ],
            'classic-salon': [
                { x: 15, y: 20, width: 25, height: 30, id: 'zone-1' },
                { x: 55, y: 20, width: 25, height: 30, id: 'zone-2' }
            ],
            'industrial-loft': [
                { x: 25, y: 30, width: 20, height: 25, id: 'zone-1' },
                { x: 55, y: 30, width: 20, height: 25, id: 'zone-2' }
            ],
            'contemporary-space': [
                { x: 20, y: 25, width: 18, height: 22, id: 'zone-1' },
                { x: 42, y: 25, width: 18, height: 22, id: 'zone-2' },
                { x: 64, y: 25, width: 18, height: 22, id: 'zone-3' }
            ]
        };
        
        const roomZones = zones[roomId] || zones['modern-gallery'];
        
        roomZones.forEach(zone => {
            const dropZone = document.createElement('div');
            dropZone.className = 'artwork-drop-zone';
            dropZone.dataset.zoneId = zone.id;
            
            // CRITICAL: Stili CSS fissi per evitare spostamenti
            dropZone.style.cssText = `
                position: absolute !important;
                left: ${zone.x}% !important;
                top: ${zone.y}% !important;
                width: ${zone.width}% !important;
                height: ${zone.height}% !important;
                border: 2px dashed #007bff;
                border-radius: 8px;
                background: rgba(0, 123, 255, 0.1);
                opacity: 0;
                transition: opacity 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                z-index: 2;
                box-sizing: border-box;
            `;
            
            dropZone.innerHTML = `
                <div class="drop-zone-content">
                    <i class="fas fa-plus"></i>
                    <span>Trascina qui</span>
                </div>
            `;
            
            zonesContainer.appendChild(dropZone);
        });
        
        console.log(`✅ Generate ${roomZones.length} zone FISSE per ${roomId}`);
    }

    handleDragStart(e) {
        if (!e.target.classList.contains('artwork-item')) return;
        
        this.draggedArtwork = {
            element: e.target,
            artworkId: e.target.dataset.artworkId,
            artworkIndex: e.target.dataset.artworkIndex
        };
        
        // Mostra zone di drop
        this.showDropZones();
        
        // Effetto visuale MIGLIORATO
        e.target.style.opacity = '0.5';
        e.target.classList.add('dragging');
        
        // CRITICAL: Imposta trasferimento dati per migliore compatibilità
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', this.draggedArtwork.artworkId);
        
        console.log(`🎯 Drag iniziato: ${this.draggedArtwork.artworkId}`);
    }

    handleDragOver(e) {
        if (!this.draggedArtwork) return;
        
        e.preventDefault();
        
        const dropZone = e.target.closest('.artwork-drop-zone');
        if (dropZone) {
            dropZone.classList.add('drag-over');
        }
    }

    handleDrop(e) {
        e.preventDefault();
        
        if (!this.draggedArtwork) return;
        
        const dropZone = e.target.closest('.artwork-drop-zone');
        if (dropZone) {
            this.placeArtworkInZone(dropZone, this.draggedArtwork);
        }
        
        this.hideDropZones();
    }

    placeArtworkInZone(dropZone, artworkData) {
        console.log('🖼️ Posizionamento artwork nella zona:', dropZone.dataset.zoneId);
        
        // Se è un file caricato, usa l'URL del file
        let artworkSrc, artworkTitle;
        
        if (artworkData.element.classList.contains('uploaded-file')) {
            artworkSrc = artworkData.element.dataset.fileUrl;
            artworkTitle = artworkData.element.querySelector('h4').textContent;
        } else {
            // Artwork dalla galleria esistente
            const artworkIndex = parseInt(artworkData.artworkIndex);
            const artwork = this.galleryData.images[artworkIndex];
            
            if (!artwork) {
                console.error('❌ Artwork non trovato');
                return;
            }
            
            artworkSrc = artwork.medium || artwork.url;
            artworkTitle = artwork.title;
        }
        
        // CRITICAL FIX: Pulisci completamente la zona prima di aggiungere nuova immagine
        dropZone.innerHTML = '';
        dropZone.classList.remove('has-artwork');
        
        // Forza il reflow del DOM
        dropZone.offsetHeight;
        
        // Crea elemento artwork nel mockup con gestione immagine robusta
        const artworkElement = document.createElement('div');
        artworkElement.className = 'placed-artwork';
        artworkElement.dataset.artworkId = artworkData.artworkId;
        
        // Crea immagine con load event per evitare problemi di rendering
        const img = document.createElement('img');
        img.src = artworkSrc;
        img.alt = artworkTitle;
        img.style.cssText = `
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            background: #f8f9fa;
        `;
        
        // Controlli artwork
        const controls = document.createElement('div');
        controls.className = 'artwork-controls';
        controls.innerHTML = `
            <button class="control-btn" data-action="resize" title="Ridimensiona">
                <i class="fas fa-expand-arrows-alt"></i>
            </button>
            <button class="control-btn" data-action="remove" title="Rimuovi">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        // Assembla l'elemento
        artworkElement.appendChild(img);
        artworkElement.appendChild(controls);
        
        // CRITICAL: Gestisci il caricamento immagine
        img.onload = () => {
            console.log('✅ Immagine caricata correttamente:', artworkTitle);
            dropZone.classList.add('has-artwork');
        };
        
        img.onerror = () => {
            console.error('❌ Errore caricamento immagine:', artworkSrc);
            img.style.background = '#e5e7eb';
            img.style.border = '2px dashed #9ca3af';
            dropZone.classList.add('has-artwork');
        };
        
        // Aggiungi alla zona
        dropZone.appendChild(artworkElement);
        
        // Setup controlli artwork
        this.setupArtworkControls(artworkElement);
        
        // CRITICAL: Fix z-index e stili della zona
        dropZone.style.cssText += `
            background: transparent !important;
            border: none !important;
            z-index: 1;
        `;
        
        console.log(`✅ Artwork posizionato: ${artworkTitle}`);
    }

    handleDragEnd(e) {
        if (this.draggedArtwork) {
            this.draggedArtwork.element.style.opacity = '1';
            this.draggedArtwork.element.classList.remove('dragging');
            this.draggedArtwork = null;
        }
        
        this.hideDropZones();
        
        // CRITICAL: Pulizia completa degli stati drag
        document.querySelectorAll('.drag-over').forEach(zone => {
            zone.classList.remove('drag-over');
        });
        
        // Force refresh delle zone per evitare stati inconsistenti
        setTimeout(() => {
            this.refreshDropZones();
        }, 100);
    }

    refreshDropZones() {
        // Ripristina stati corretti delle zone di drop SENZA SPOSTARE POSIZIONI
        const zones = document.querySelectorAll('.artwork-drop-zone');
        zones.forEach(zone => {
            const hasArtwork = zone.querySelector('.placed-artwork');
            const zoneId = zone.dataset.zoneId;
            
            if (hasArtwork) {
                zone.classList.add('has-artwork');
                // CRITICAL: Mantieni posizione originale anche con artwork
                zone.style.border = 'none !important';
                zone.style.background = 'transparent !important';
                zone.style.opacity = '1 !important';
            } else {
                zone.classList.remove('has-artwork');
                // CRITICAL: NON modificare position, left, top - solo stili visuali
                zone.style.border = '2px dashed #007bff';
                zone.style.background = 'rgba(0, 123, 255, 0.1)';
                zone.style.opacity = '0';
                
                // Assicurati che il contenuto placeholder sia presente
                if (!zone.querySelector('.drop-zone-content')) {
                    zone.innerHTML = `
                        <div class="drop-zone-content">
                            <i class="fas fa-plus"></i>
                            <span>Trascina qui</span>
                        </div>
                    `;
                }
            }
        });
        
        console.log('🔄 Drop zones refreshed SENZA spostamenti');
    }

    showDropZones() {
        const zones = document.querySelectorAll('.artwork-drop-zone');
        zones.forEach(zone => {
            zone.style.opacity = '1';
            zone.style.transform = 'scale(1.05)';
        });
    }

    hideDropZones() {
        const zones = document.querySelectorAll('.artwork-drop-zone');
        zones.forEach(zone => {
            zone.style.opacity = '0';
            zone.style.transform = 'scale(1)';
        });
    }

    placeArtworkInZone(dropZone, artworkData) {
        const artworkIndex = parseInt(artworkData.artworkIndex);
        const artwork = this.galleryData.images[artworkIndex];
        
        if (!artwork) return;
        
        // Rimuovi contenuto placeholder
        dropZone.innerHTML = '';
        
        // Crea elemento artwork nel mockup
        const artworkElement = document.createElement('div');
        artworkElement.className = 'placed-artwork';
        artworkElement.dataset.artworkId = artwork.id;
        artworkElement.innerHTML = `
            <img src="${artwork.medium || artwork.url}" alt="${artwork.title}">
            <div class="artwork-controls">
                <button class="control-btn" data-action="resize">
                    <i class="fas fa-expand-arrows-alt"></i>
                </button>
                <button class="control-btn" data-action="remove">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        dropZone.appendChild(artworkElement);
        dropZone.classList.add('has-artwork');
        
        // Setup controlli artwork
        this.setupArtworkControls(artworkElement);
        
        console.log(`🖼️ Artwork posizionato: ${artwork.title}`);
    }

    setupArtworkControls(artworkElement) {
        const controls = artworkElement.querySelectorAll('.control-btn');
        
        controls.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const action = btn.dataset.action;
                
                switch(action) {
                    case 'resize':
                        this.resizeArtwork(artworkElement);
                        break;
                    case 'remove':
                        this.removeArtwork(artworkElement);
                        break;
                }
            });
        });
        
        // CRITICAL: Artwork trascinabile nella scena
        this.makeArtworkDraggable(artworkElement);
    }

    makeArtworkDraggable(artworkElement) {
        let isDragging = false;
        let startX, startY, startLeft, startTop;
        
        // Mouse down per iniziare il drag
        artworkElement.addEventListener('mousedown', (e) => {
            // Solo se non si clicca sui controlli
            if (e.target.closest('.control-btn')) return;
            
            isDragging = true;
            
            // Coordinate iniziali
            startX = e.clientX;
            startY = e.clientY;
            
            // Posizione iniziale dell'artwork
            const rect = artworkElement.getBoundingClientRect();
            const mockupRect = artworkElement.closest('.room-mockup-canvas').getBoundingClientRect();
            
            startLeft = ((rect.left - mockupRect.left) / mockupRect.width) * 100;
            startTop = ((rect.top - mockupRect.top) / mockupRect.height) * 100;
            
            // Effetti visuali
            artworkElement.style.cursor = 'grabbing';
            artworkElement.style.zIndex = '1000';
            artworkElement.style.transform = 'scale(1.1)';
            artworkElement.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.3)';
            
            // Mostra zone di drop disponibili
            this.showAvailableDropZones(artworkElement);
            
            console.log('🎯 Inizio drag artwork nella scena');
            
            e.preventDefault();
        });
        
        // Mouse move durante il drag
        document.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            
            const mockup = artworkElement.closest('.room-mockup-canvas');
            const mockupRect = mockup.getBoundingClientRect();
            
            // Calcola nuova posizione in percentuale
            const deltaX = e.clientX - startX;
            const deltaY = e.clientY - startY;
            
            const newLeft = startLeft + (deltaX / mockupRect.width) * 100;
            const newTop = startTop + (deltaY / mockupRect.height) * 100;
            
            // Limiti per non uscire dal mockup
            const boundedLeft = Math.max(0, Math.min(85, newLeft));
            const boundedTop = Math.max(0, Math.min(80, newTop));
            
            // Aggiorna posizione dell'artwork
            const parentZone = artworkElement.parentElement;
            parentZone.style.left = boundedLeft + '%';
            parentZone.style.top = boundedTop + '%';
            
            // Evidenzia zone sotto il cursore
            this.highlightDropZoneUnderMouse(e.clientX, e.clientY);
        });
        
        // Mouse up per terminare il drag
        document.addEventListener('mouseup', () => {
            if (!isDragging) return;
            
            isDragging = false;
            
            // Ripristina effetti visuali
            artworkElement.style.cursor = 'grab';
            artworkElement.style.zIndex = '3';
            artworkElement.style.transform = 'scale(1)';
            artworkElement.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.15)';
            
            // Nascondi zone di drop
            this.hideDropZones();
            
            console.log('✅ Drag artwork completato');
        });
        
        // Cursore grab di default
        artworkElement.style.cursor = 'grab';
    }

    showAvailableDropZones(currentArtwork) {
        const zones = document.querySelectorAll('.artwork-drop-zone');
        zones.forEach(zone => {
            // Mostra solo zone vuote o diverse da quella corrente
            if (!zone.classList.contains('has-artwork') || zone.contains(currentArtwork)) {
                zone.style.opacity = '0.7';
                zone.style.border = '2px dashed #10b981';
                zone.style.background = 'rgba(16, 185, 129, 0.1)';
            }
        });
    }

    highlightDropZoneUnderMouse(mouseX, mouseY) {
        const zones = document.querySelectorAll('.artwork-drop-zone');
        zones.forEach(zone => {
            const rect = zone.getBoundingClientRect();
            
            if (mouseX >= rect.left && mouseX <= rect.right && 
                mouseY >= rect.top && mouseY <= rect.bottom) {
                zone.style.background = 'rgba(16, 185, 129, 0.3)';
                zone.style.transform = 'scale(1.05)';
            } else {
                zone.style.background = 'rgba(16, 185, 129, 0.1)';
                zone.style.transform = 'scale(1)';
            }
        });
    }

    setupDesignToolbar() {
        console.log('🛠️ Toolbar design configurata');
    }

    setupDesignArea(roomId) {
        // Back to rooms
        const backBtn = document.getElementById('btn-back-to-rooms');
        if (backBtn) {
            backBtn.addEventListener('click', () => {
                this.backToRoomsSelection();
            });
        }
        
        // Toolbar buttons
        const toolbarBtns = document.querySelectorAll('.btn-toolbar[data-tool]');
        toolbarBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                toolbarBtns.forEach(b => b.classList.remove('active'));
                e.currentTarget.classList.add('active');
                
                const tool = e.currentTarget.dataset.tool;
                this.setDesignTool(tool);
            });
        });
        
        // Save design
        const saveBtn = document.getElementById('btn-save-design');
        if (saveBtn) {
            saveBtn.addEventListener('click', () => {
                this.saveDesign();
            });
        }
        
        // Room controls
        const roomControls = document.querySelectorAll('.btn-room-control');
        roomControls.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const action = btn.dataset.action;
                this.handleRoomControl(action);
            });
        });
        
        // NUOVO: Inizializza tool di default
        this.setDesignTool('select');
        
        console.log(`✅ Area design configurata per: ${roomId}`);
    }

    backToRoomsSelection() {
        const roomSelection = document.getElementById('room-selection');
        const roomDesign = document.getElementById('room-design');
        
        if (roomDesign) roomDesign.classList.remove('active');
        if (roomSelection) roomSelection.classList.add('active');
        
        this.updateBreadcrumb('rooms');
        
        console.log('🔙 Ritorno alla selezione stanze');
    }

    setDesignTool(tool) {
        this.currentTool = tool;
        
        // Aggiorna cursore e comportamenti
        const mockup = document.querySelector('.room-mockup-canvas');
        if (mockup) {
            mockup.className = `room-mockup-canvas tool-${tool}`;
        }
        
        console.log(`🛠️ Tool attivo: ${tool}`);
    }

    handleRoomControl(action) {
        const mockup = document.querySelector('.room-mockup-canvas');
        if (!mockup) return;
        
        switch(action) {
            case 'zoom-in':
                mockup.style.transform = 'scale(1.2)';
                break;
            case 'zoom-out':
                mockup.style.transform = 'scale(0.8)';
                break;
            case 'center':
                mockup.style.transform = 'scale(1)';
                break;
        }
        
        console.log(`🔍 Controllo stanza: ${action}`);
    }

    saveDesign() {
        const placedArtworks = document.querySelectorAll('.placed-artwork');
        const designData = {
            roomId: document.querySelector('.room-mockup-canvas')?.dataset.room || 'unknown',
            artworks: Array.from(placedArtworks).map(artwork => ({
                artworkId: artwork.dataset.artworkId,
                position: artwork.parentElement.dataset.zoneId,
                // Aggiungere altre proprietà come dimensioni, rotazione etc.
            })),
            timestamp: new Date().toISOString()
        };
        
        console.log('💾 Design salvato:', designData);
        
        // Mostra preview o download
        this.showDesignPreview(designData);
    }

    showDesignPreview(designData) {
        // Crea lightbox per preview del design
        const previewModal = document.createElement('div');
        previewModal.className = 'design-preview-modal';
        previewModal.innerHTML = `
            <div class="modal-overlay"></div>
            <div class="modal-content">
                <div class="modal-header">
                    <h3>🎨 Il tuo design è pronto!</h3>
                    <button class="modal-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="preview-mockup">
                        ${document.querySelector('.room-mockup-canvas')?.innerHTML || ''}
                    </div>
                    <div class="preview-actions">
                        <button class="btn-download-hd">
                            <i class="fas fa-download"></i>
                            Scarica HD
                        </button>
                        <button class="btn-share-design">
                            <i class="fas fa-share-alt"></i>
                            Condividi
                        </button>
                        <button class="btn-new-design">
                            <i class="fas fa-plus"></i>
                            Nuovo Design
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(previewModal);
        
        // Event listeners
        previewModal.querySelector('.modal-close').addEventListener('click', () => {
            previewModal.remove();
        });
        
        previewModal.querySelector('.modal-overlay').addEventListener('click', () => {
            previewModal.remove();
        });
        
        console.log('👁️ Preview design mostrata');
    }

    backToRoomsSelection() {
        const roomSelection = document.getElementById('room-selection');
        const roomDesign = document.getElementById('room-design');
        
        roomDesign.classList.remove('active');
        roomSelection.classList.add('active');
        
        this.updateBreadcrumb('rooms');
        
        console.log('🔙 Ritorno alla selezione stanze');
    }

    setDesignTool(tool) {
        this.currentTool = tool;
        
        // Aggiorna cursore e comportamenti
        const mockup = document.querySelector('.room-mockup-canvas');
        if (mockup) {
            mockup.className = `room-mockup-canvas tool-${tool}`;
        }
        
        console.log(`🛠️ Tool attivo: ${tool}`);
    }

    handleRoomControl(action) {
        const mockup = document.querySelector('.room-mockup-canvas');
        if (!mockup) return;
        
        switch(action) {
            case 'zoom-in':
                mockup.style.transform = 'scale(1.2)';
                break;
            case 'zoom-out':
                mockup.style.transform = 'scale(0.8)';
                break;
            case 'center':
                mockup.style.transform = 'scale(1)';
                break;
        }
        
        console.log(`🔍 Controllo stanza: ${action}`);
    }

    saveDesign() {
        const placedArtworks = document.querySelectorAll('.placed-artwork');
        const designData = {
            roomId: document.querySelector('.room-mockup-canvas').dataset.room,
            artworks: Array.from(placedArtworks).map(artwork => ({
                artworkId: artwork.dataset.artworkId,
                position: artwork.parentElement.dataset.zoneId,
                // Aggiungere altre proprietà come dimensioni, rotazione etc.
            })),
            timestamp: new Date().toISOString()
        };
        
        console.log('💾 Design salvato:', designData);
        
        // Mostra preview o download
        this.showDesignPreview(designData);
    }

    resizeArtwork(artworkElement) {
        // Implementa il ridimensionamento dell'artwork
        console.log('🔄 Resize artwork');
        
        artworkElement.classList.toggle('resizing');
        
        // Cicla tra diverse dimensioni
        const currentScale = artworkElement.style.transform;
        let newScale = 'scale(1)';
        
        if (currentScale.includes('1.2')) {
            newScale = 'scale(0.8)';
        } else if (currentScale.includes('0.8')) {
            newScale = 'scale(1)';
        } else {
            newScale = 'scale(1.2)';
        }
        
        artworkElement.style.transform = newScale;
        
        setTimeout(() => {
            artworkElement.classList.remove('resizing');
        }, 300);
    }

    removeArtwork(artworkElement) {
        const dropZone = artworkElement.parentElement;
        const zoneId = dropZone.dataset.zoneId;
        
        // Animazione di rimozione
        artworkElement.style.opacity = '0';
        artworkElement.style.transform = 'scale(0.5)';
        
        setTimeout(() => {
            // CRITICAL: Pulizia completa e ripristino zona
            dropZone.innerHTML = '';
            dropZone.classList.remove('has-artwork');
            
            // Ripristina contenuto placeholder
            const placeholder = document.createElement('div');
            placeholder.className = 'drop-zone-content';
            placeholder.innerHTML = `
                <i class="fas fa-plus"></i>
                <span>Trascina qui</span>
            `;
            
            dropZone.appendChild(placeholder);
            
            // CRITICAL: Ripristina stili originali della zona
            dropZone.style.cssText = `
                position: absolute;
                border: 2px dashed #007bff;
                border-radius: 8px;
                background: rgba(0, 123, 255, 0.1);
                opacity: 0;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
            `;
            
            // Force refresh del layout
            dropZone.offsetHeight;
            
        }, 300);
        
        console.log(`🗑️ Artwork rimosso dalla zona: ${zoneId}`);
    }

    startArtworkDrag(e, artworkElement) {
        // Implementa il trascinamento per riposizionamento
        e.preventDefault();
        
        const rect = artworkElement.getBoundingClientRect();
        const offsetX = e.clientX - rect.left;
        const offsetY = e.clientY - rect.top;
        
        const onMouseMove = (moveEvent) => {
            artworkElement.style.position = 'absolute';
            artworkElement.style.left = moveEvent.clientX - offsetX + 'px';
            artworkElement.style.top = moveEvent.clientY - offsetY + 'px';
            artworkElement.style.zIndex = '1000';
        };
        
        const onMouseUp = () => {
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup', onMouseUp);
            
            // Reset position
            artworkElement.style.position = '';
            artworkElement.style.left = '';
            artworkElement.style.top = '';
            artworkElement.style.zIndex = '';
        };
        
        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);
    }

    // Override delle funzioni 2D originali per mantenere compatibilità
    create2DGridContainer() {
        // Funzione di compatibilità - il nuovo sistema usa createRoomSelectionInterface
        console.log('⚠️ Usando sistema Rooms invece del vecchio 2D grid');
    }

    generate2DCards() {
        // Funzione di compatibilità 
        console.log('⚠️ Usando sistema artwork nella sidebar');
    }

    create2DCard(artwork, index) {
        // Placeholder per compatibilità
        return document.createElement('div');
    }

    init2DControls() {
        // Placeholder per compatibilità
        console.log('⚠️ Controlli gestiti dal nuovo sistema Rooms');
    }

    setup2DSearch() {
        const searchInput = document.getElementById('artwork-search');
        if (!searchInput) return;
        
        let searchTimeout;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.filterArtwork(e.target.value);
            }, 300);
        });
    }

    filterArtwork(searchTerm) {
        const artworkItems = document.querySelectorAll('.artwork-item');
        const lowerTerm = searchTerm.toLowerCase();
        
        artworkItems.forEach(item => {
            const title = item.querySelector('.artwork-info h4').textContent.toLowerCase();
            
            if (title.includes(lowerTerm) || lowerTerm === '') {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
        
        console.log(`🔍 Filtro applicato: "${searchTerm}"`);
    }

    setup2DFilters() {
        // Placeholder
    }

    setup2DViewOptions() {
        // Placeholder  
    }

    activate2DInteractions() {
        // Le interazioni sono gestite dal nuovo sistema
        console.log('🎯 Interazioni 2D gestite dal sistema Rooms');
    }

    filter2DCards(searchTerm) {
        // Usa la nuova funzione filterArtwork
        this.filterArtwork(searchTerm);
    }

    apply2DFilter(filter) {
        console.log(`🏷️ Filtro applicato: ${filter}`);
    }

    change2DView(view) {
        console.log(`👁️ Vista cambiata: ${view}`);
    }

    create2DCard(artwork, index) {
        const card = document.createElement('div');
        card.className = 'canva-card';
        card.setAttribute('data-artwork-id', artwork.id);
        card.setAttribute('data-index', index);
        
        // Animazione di ingresso scaglionata
        card.style.animationDelay = `${index * 0.1}s`;

        card.innerHTML = `
            <div class="card-inner">
                <div class="card-image-container">
                    <img src="${artwork.medium || artwork.url}" 
                         alt="${artwork.title}" 
                         loading="lazy"
                         class="card-image">
                    <div class="card-overlay">
                        <div class="overlay-actions">
                            <button class="action-btn preview-btn" data-action="preview">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn favorite-btn" data-action="favorite">
                                <i class="far fa-heart"></i>
                            </button>
                            <button class="action-btn share-btn" data-action="share">
                                <i class="fas fa-share-alt"></i>
                            </button>
                        </div>
                        <div class="card-gradient"></div>
                    </div>
                </div>
                <div class="card-content">
                    <h4 class="card-title">${artwork.title}</h4>
                    <div class="card-meta">
                        <span class="card-date">
                            <i class="fas fa-calendar"></i>
                            ${new Date().toLocaleDateString('it-IT')}
                        </span>
                        <span class="card-views">
                            <i class="fas fa-eye"></i>
                            ${Math.floor(Math.random() * 500) + 50}
                        </span>
                    </div>
                </div>
            </div>
        `;

        return card;
    }

    init2DControls() {
        // Inizializza controlli della modalità 2D
        this.setup2DSearch();
        this.setup2DFilters();
        this.setup2DViewOptions();
    }

    setup2DSearch() {
        const searchInput = document.getElementById('canva-search');
        if (!searchInput) return;

        let searchTimeout;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.filter2DCards(e.target.value);
            }, 300);
        });
    }

    setup2DFilters() {
        const filterBtns = document.querySelectorAll('.filter-btn');
        filterBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                // Rimuovi active da tutti
                filterBtns.forEach(b => b.classList.remove('active'));
                // Aggiungi active al corrente
                e.currentTarget.classList.add('active');
                
                const filter = e.currentTarget.dataset.filter;
                this.apply2DFilter(filter);
            });
        });
    }

    setup2DViewOptions() {
        const viewBtns = document.querySelectorAll('.view-option');
        viewBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                viewBtns.forEach(b => b.classList.remove('active'));
                e.currentTarget.classList.add('active');
                
                const view = e.currentTarget.dataset.view;
                this.change2DView(view);
            });
        });
    }

    filter2DCards(searchTerm) {
        const cards = document.querySelectorAll('.canva-card');
        const term = searchTerm.toLowerCase();

        cards.forEach(card => {
            const title = card.querySelector('.card-title').textContent.toLowerCase();
            const match = title.includes(term);
            
            if (match) {
                card.style.display = 'block';
                card.classList.add('fade-in');
            } else {
                card.style.display = 'none';
                card.classList.remove('fade-in');
            }
        });
    }

    apply2DFilter(filter) {
        const cards = document.querySelectorAll('.canva-card');
        
        cards.forEach((card, index) => {
            let show = true;
            
            switch(filter) {
                case 'recent':
                    show = index < 4; // Mostra solo le prime 4
                    break;
                case 'favorites':
                    show = Math.random() > 0.5; // Random per demo
                    break;
                case 'all':
                default:
                    show = true;
                    break;
            }
            
            if (show) {
                card.style.display = 'block';
                card.classList.add('filter-fade-in');
            } else {
                card.style.display = 'none';
                card.classList.remove('filter-fade-in');
            }
        });
    }

    change2DView(view) {
        const grid = document.querySelector('.artworks-grid');
        if (!grid) return;

        grid.classList.remove('grid-view', 'list-view');
        grid.classList.add(`${view}-view`);
    }

    activate2DInteractions() {
        // Attiva hover effects e click handlers
        this.setup2DCardHovers();
        this.setup2DCardClicks();
        this.setup2DLightbox();
    }

    setup2DCardHovers() {
        const cards = document.querySelectorAll('.canva-card');
        
        cards.forEach(card => {
            card.addEventListener('mouseenter', (e) => {
                e.currentTarget.classList.add('card-hover');
                
                // Effetto parallasse delle carte vicine
                this.create2DParallaxEffect(e.currentTarget);
            });
            
            card.addEventListener('mouseleave', (e) => {
                e.currentTarget.classList.remove('card-hover');
            });
        });
    }

    create2DParallaxEffect(targetCard) {
        const cards = document.querySelectorAll('.canva-card');
        const targetRect = targetCard.getBoundingClientRect();
        const targetCenterX = targetRect.left + targetRect.width / 2;
        const targetCenterY = targetRect.top + targetRect.height / 2;

        cards.forEach(card => {
            if (card === targetCard) return;
            
            const cardRect = card.getBoundingClientRect();
            const cardCenterX = cardRect.left + cardRect.width / 2;
            const cardCenterY = cardRect.top + cardRect.height / 2;
            
            const distance = Math.sqrt(
                Math.pow(targetCenterX - cardCenterX, 2) + 
                Math.pow(targetCenterY - cardCenterY, 2)
            );
            
            if (distance < 300) { // Solo carte vicine
                const intensity = (300 - distance) / 300;
                const offsetX = (targetCenterX - cardCenterX) * intensity * 0.1;
                const offsetY = (targetCenterY - cardCenterY) * intensity * 0.1;
                
                card.style.transform = `translate(${offsetX}px, ${offsetY}px) scale(${1 + intensity * 0.05})`;
                card.style.transition = 'transform 0.3s ease-out';
                
                setTimeout(() => {
                    card.style.transform = '';
                }, 300);
            }
        });
    }

    setup2DCardClicks() {
        document.addEventListener('click', (e) => {
            const actionBtn = e.target.closest('.action-btn');
            if (actionBtn) {
                e.preventDefault();
                const action = actionBtn.dataset.action;
                const card = actionBtn.closest('.canva-card');
                this.handle2DCardAction(action, card);
            }
            
            const card = e.target.closest('.canva-card');
            if (card && !actionBtn) {
                this.open2DLightbox(card);
            }
        });
    }

    handle2DCardAction(action, card) {
        const artworkId = card.dataset.artworkId;
        
        switch(action) {
            case 'preview':
                this.open2DLightbox(card);
                break;
            case 'favorite':
                this.toggle2DFavorite(card);
                break;
            case 'share':
                this.share2DArtwork(artworkId);
                break;
        }
    }

    toggle2DFavorite(card) {
        const favoriteBtn = card.querySelector('.favorite-btn i');
        const isFavorite = favoriteBtn.classList.contains('fas');
        
        if (isFavorite) {
            favoriteBtn.classList.remove('fas');
            favoriteBtn.classList.add('far');
            card.classList.remove('is-favorite');
        } else {
            favoriteBtn.classList.remove('far');
            favoriteBtn.classList.add('fas');
            card.classList.add('is-favorite');
        }
        
        // Animazione
        favoriteBtn.parentElement.classList.add('pulse');
        setTimeout(() => {
            favoriteBtn.parentElement.classList.remove('pulse');
        }, 300);
    }

    share2DArtwork(artworkId) {
        if (navigator.share) {
            navigator.share({
                title: 'Scopri questa illustrazione',
                text: 'Guarda questa bellissima illustrazione nella galleria di Marcello Scavo',
                url: window.location.href
            });
        } else {
            // Fallback: copia URL
            navigator.clipboard.writeText(window.location.href);
            this.show2DNotification('Link copiato negli appunti!');
        }
    }

    setup2DLightbox() {
        const lightbox = document.getElementById('canva-lightbox');
        if (!lightbox) return;

        // Chiudi lightbox
        lightbox.querySelector('.lightbox-close').addEventListener('click', () => {
            this.close2DLightbox();
        });
        
        lightbox.querySelector('.lightbox-overlay').addEventListener('click', () => {
            this.close2DLightbox();
        });
        
        // ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && lightbox.classList.contains('active')) {
                this.close2DLightbox();
            }
        });
    }

    open2DLightbox(card) {
        const lightbox = document.getElementById('canva-lightbox');
        const artworkId = card.dataset.artworkId;
        const artwork = this.galleryData.images.find(img => img.id == artworkId);
        
        if (!artwork) return;

        // Popola lightbox
        document.getElementById('lightbox-image').src = artwork.url;
        document.getElementById('lightbox-title').textContent = artwork.title;
        document.getElementById('lightbox-description').textContent = artwork.description || 'Illustrazione digitale';
        
        // Mostra lightbox con animazione
        lightbox.classList.add('active');
        document.body.classList.add('lightbox-open');
    }

    close2DLightbox() {
        const lightbox = document.getElementById('canva-lightbox');
        lightbox.classList.remove('active');
        document.body.classList.remove('lightbox-open');
    }

    show2DNotification(message) {
        // Crea notifica stile Canva
        const notification = document.createElement('div');
        notification.className = 'canva-notification';
        notification.innerHTML = `
            <i class="fas fa-check-circle"></i>
            <span>${message}</span>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    disableParallaxEffects() {
        // Disabilita gli effetti parallax nascondendo gli elementi
        const parallaxElements = document.querySelectorAll('.parallax-layer, .gallery-frame');
        
        parallaxElements.forEach((element) => {
            element.style.display = 'none';
            element.style.transform = 'none';
        });
        
        console.log('🚫 Effetti parallax disabilitati per modalità 3D');
    }

    enableParallaxEffects() {
        // Riabilita gli effetti parallax mostrando gli elementi
        const parallaxElements = document.querySelectorAll('.parallax-layer, .gallery-frame');
        
        parallaxElements.forEach((element) => {
            element.style.display = '';
            element.style.transform = '';
        });
        
        console.log('✅ Effetti parallax riabilitati per modalità 2D');
    }

    initParallaxEffects() {
        if (!this.galleryData || !this.galleryData.settings.enableParallax) return;

        const parallaxElements = document.querySelectorAll('.parallax-layer, .gallery-frame');
        
        const handleScroll = () => {
            const scrollY = window.pageYOffset;
            const windowHeight = window.innerHeight;
            
            parallaxElements.forEach((element) => {
                const speed = parseFloat(element.dataset.parallaxSpeed) || 0.5;
                const yPos = -(scrollY * speed);
                element.style.transform = `translateY(${yPos}px)`;
            });
        };

        const handleMouseMove = (e) => {
            const mouseX = (e.clientX / window.innerWidth) * 2 - 1;
            const mouseY = (e.clientY / window.innerHeight) * 2 - 1;
            
            parallaxElements.forEach((element) => {
                const speed = parseFloat(element.dataset.parallaxSpeed) || 0.1;
                const x = mouseX * speed * 20;
                const y = mouseY * speed * 20;
                
                element.style.transform += ` translate(${x}px, ${y}px)`;
            });
        };

        window.addEventListener('scroll', handleScroll);
        window.addEventListener('mousemove', handleMouseMove);
        
        // Cleanup
        this.cleanupFunctions = this.cleanupFunctions || [];
        this.cleanupFunctions.push(() => {
            window.removeEventListener('scroll', handleScroll);
            window.removeEventListener('mousemove', handleMouseMove);
        });
    }

    destroy() {
        if (this.cleanupFunctions) {
            this.cleanupFunctions.forEach(fn => fn());
        }

        if (this.renderer) {
            this.renderer.dispose();
        }

        if (this.scene) {
            this.scene.clear();
        }
    }

    // FUNZIONI COMPLETE TOOLBAR
    enableArtworkSelection() {
        // Tool SELEZIONA - mostra controlli e permette selezione
        const artworks = document.querySelectorAll('.placed-artwork');
        artworks.forEach(artwork => {
            artwork.style.cursor = 'pointer';
            artwork.onclick = (e) => {
                e.stopPropagation();
                this.selectArtwork(artwork);
            };
        });
    }

    enableArtworkMoving() {
        // Tool SPOSTA - abilita drag su tutti gli artwork
        const artworks = document.querySelectorAll('.placed-artwork');
        artworks.forEach(artwork => {
            artwork.style.cursor = 'grab';
            // Il drag è già implementato in makeArtworkDraggable
        });
    }

    enableArtworkResizing() {
        // Tool RIDIMENSIONA - abilita resize con handles
        const artworks = document.querySelectorAll('.placed-artwork');
        artworks.forEach(artwork => {
            artwork.style.cursor = 'nw-resize';
            this.addResizeHandles(artwork);
        });
    }

    selectArtwork(artwork) {
        // Rimuovi selezioni precedenti
        document.querySelectorAll('.placed-artwork.selected').forEach(art => {
            art.classList.remove('selected');
        });
        
        // Seleziona artwork corrente
        artwork.classList.add('selected');
        
        // Mostra controlli avanzati
        this.showAdvancedControls(artwork);
        
        console.log('🎯 Artwork selezionato');
    }

    addResizeHandles(artwork) {
        // Rimuovi handles esistenti
        const existingHandles = artwork.querySelectorAll('.resize-handle');
        existingHandles.forEach(handle => handle.remove());
        
        // Crea handles di resize
        const handles = ['nw', 'ne', 'sw', 'se', 'n', 's', 'e', 'w'];
        
        handles.forEach(position => {
            const handle = document.createElement('div');
            handle.className = `resize-handle resize-${position}`;
            handle.dataset.position = position;
            
            // Event listeners per resize
            handle.addEventListener('mousedown', (e) => {
                e.stopPropagation();
                this.startResize(e, artwork, position);
            });
            
            artwork.appendChild(handle);
        });
    }

    startResize(e, artwork, position) {
        console.log(`🔄 Inizio resize: ${position}`);
        
        const startX = e.clientX;
        const startY = e.clientY;
        const startRect = artwork.getBoundingClientRect();
        const parentZone = artwork.parentElement;
        const parentRect = parentZone.getBoundingClientRect();
        
        const onMouseMove = (moveEvent) => {
            const deltaX = moveEvent.clientX - startX;
            const deltaY = moveEvent.clientY - startY;
            
            let newWidth = startRect.width;
            let newHeight = startRect.height;
            let newLeft = startRect.left - parentRect.left;
            let newTop = startRect.top - parentRect.top;
            
            // Calcola nuove dimensioni basate sulla posizione del handle
            switch(position) {
                case 'se': // Sud-Est
                    newWidth = startRect.width + deltaX;
                    newHeight = startRect.height + deltaY;
                    break;
                case 'sw': // Sud-Ovest
                    newWidth = startRect.width - deltaX;
                    newHeight = startRect.height + deltaY;
                    newLeft = startRect.left - parentRect.left + deltaX;
                    break;
                case 'ne': // Nord-Est
                    newWidth = startRect.width + deltaX;
                    newHeight = startRect.height - deltaY;
                    newTop = startRect.top - parentRect.top + deltaY;
                    break;
                case 'nw': // Nord-Ovest
                    newWidth = startRect.width - deltaX;
                    newHeight = startRect.height - deltaY;
                    newLeft = startRect.left - parentRect.left + deltaX;
                    newTop = startRect.top - parentRect.top + deltaY;
                    break;
                case 'n': // Nord
                    newHeight = startRect.height - deltaY;
                    newTop = startRect.top - parentRect.top + deltaY;
                    break;
                case 's': // Sud
                    newHeight = startRect.height + deltaY;
                    break;
                case 'e': // Est
                    newWidth = startRect.width + deltaX;
                    break;
                case 'w': // Ovest
                    newWidth = startRect.width - deltaX;
                    newLeft = startRect.left - parentRect.left + deltaX;
                    break;
            }
            
            // Limiti minimi e massimi
            newWidth = Math.max(50, Math.min(300, newWidth));
            newHeight = Math.max(50, Math.min(300, newHeight));
            
            // Applica nuove dimensioni e posizione
            const widthPercent = (newWidth / parentRect.width) * 100;
            const heightPercent = (newHeight / parentRect.height) * 100;
            const leftPercent = (newLeft / parentRect.width) * 100;
            const topPercent = (newTop / parentRect.height) * 100;
            
            parentZone.style.width = widthPercent + '%';
            parentZone.style.height = heightPercent + '%';
            parentZone.style.left = leftPercent + '%';
            parentZone.style.top = topPercent + '%';
        };
        
        const onMouseUp = () => {
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup', onMouseUp);
            console.log('✅ Resize completato');
        };
        
        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);
        
        e.preventDefault();
    }

    updateArtworkToolBehavior(artwork, tool) {
        // Aggiorna comportamento artwork basato sul tool attivo
        switch(tool) {
            case 'select':
                artwork.style.cursor = 'pointer';
                break;
            case 'move':
                artwork.style.cursor = 'grab';
                break;
            case 'resize':
                artwork.style.cursor = 'nw-resize';
                this.addResizeHandles(artwork);
                break;
        }
    }

    showAdvancedControls(artwork) {
        // Mostra pannello controlli avanzati per artwork selezionato
        console.log('🎛️ Controlli avanzati per artwork');
    }
}

// Inizializzazione automatica
document.addEventListener('DOMContentLoaded', () => {
    const galleryContainer = document.querySelector('.hero-3d-gallery');
    if (galleryContainer) {
        window.gallery3D = new Gallery3D();
    }
});

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    if (window.gallery3D) {
        window.gallery3D.destroy();
    }
});