/**
 * Gallery 2D - Sistema Pulito e Funzionante
 * Posizionamento libero di artwork nelle stanze
 */

class Gallery2D {
    constructor(galleryData) {
        this.galleryData = galleryData;
        this.artworks = [];
        this.selectedArtwork = null;
        this.resizeMode = false;
        this.draggedArtwork = null;
        
        this.init();
    }
    
    init() {
        console.log('🎨 Inizializzazione Gallery 2D');
        this.createInterface();
        this.setupEventListeners();
    }
    
    createInterface() {
        const container = document.querySelector('.hero-3d-content');
        if (!container) return;
        
        container.innerHTML = `
            <div class="gallery-2d-container">
                <!-- Room Selection -->
                <div class="room-selection active" id="room-selection">
                    <div class="header">
                        <h2>Scegli la tua galleria</h2>
                        <p>Seleziona l'ambiente perfetto per le tue illustrazioni</p>
                    </div>
                    <div class="rooms-grid" id="rooms-grid">
                        ${this.generateRoomCards()}
                    </div>
                </div>
                
                <!-- Design Area -->
                <div class="design-area" id="design-area">
                    <div class="design-layout">
                        <!-- Canvas principale -->
                        <div class="canvas-container">
                            <div id="room-canvas"></div>
                        </div>
                        
                        <!-- Sidebar artwork -->
                        <div class="artwork-sidebar">
                            <h3>Le tue illustrazioni</h3>
                            <div class="artwork-list" id="artwork-list"></div>
                        </div>
                    </div>
                    
                    <!-- Toolbar -->
                    <div class="toolbar">
                        <button class="btn-back" id="btn-back">
                            <i class="fas fa-arrow-left"></i> Cambia Stanza
                        </button>
                        <button class="btn-tool" id="btn-resize">
                            <i class="fas fa-expand-arrows-alt"></i> Ridimensiona
                        </button>
                        <button class="btn-save" id="btn-save">
                            <i class="fas fa-download"></i> Salva
                        </button>
                    </div>
                </div>
            </div>
        `;
    }
    
    generateRoomCards() {
        const rooms = this.getAvailableRooms();
        if (!rooms || rooms.length === 0) {
            return '<p>Nessuna stanza disponibile</p>';
        }
        
        return rooms.map(room => `
            <div class="room-card" data-room-id="${room.id}">
                <img src="${room.url}" alt="${room.name}">
                <h3>${room.name}</h3>
                <button class="btn-select-room">Usa questa stanza</button>
            </div>
        `).join('');
    }
    
    getAvailableRooms() {
        if (typeof roomsData !== 'undefined' && roomsData.available_rooms) {
            return roomsData.available_rooms;
        }
        return [];
    }
    
    setupEventListeners() {
        console.log('Gallery2D: Setting up event listeners');
        
        // Room selection - usa event delegation invece di setTimeout
        document.addEventListener('click', (e) => {
            // Room card click
            const roomCard = e.target.closest('.room-card');
            if (roomCard) {
                const roomId = roomCard.dataset.roomId;
                console.log('Gallery2D: Room card clicked:', roomId);
                this.selectRoom(roomId);
                return;
            }
            
            // Back button
            if (e.target.closest('#btn-back')) {
                console.log('Gallery2D: Back button clicked');
                this.backToRooms();
                return;
            }
            
            // Resize button
            if (e.target.closest('#btn-resize')) {
                console.log('Gallery2D: Resize button clicked');
                this.toggleResizeMode();
                return;
            }
            
            // Save button - mostra menu opzioni
            if (e.target.closest('#btn-save')) {
                console.log('Gallery2D: Save button clicked');
                this.showSaveMenu(e);
                return;
            }
            
            // Save menu options
            if (e.target.closest('.save-option-png')) {
                this.saveAsPNG();
                this.hideSaveMenu();
                return;
            }
            
            if (e.target.closest('.save-option-pdf')) {
                this.saveAsPDF();
                this.hideSaveMenu();
                return;
            }
        });
        
        // Drag & Drop dalla sidebar
        document.addEventListener('dragstart', (e) => {
            if (e.target.classList.contains('artwork-item')) {
                console.log('Gallery2D: Dragging artwork');
                this.draggedArtwork = {
                    artworkIndex: e.target.dataset.artworkIndex
                };
            }
        });
    }
    
    selectRoom(roomId) {
        const rooms = this.getAvailableRooms();
        const room = rooms.find(r => r.id === roomId);
        if (!room) return;
        
        // Nascondi selezione, mostra design area
        document.getElementById('room-selection').classList.remove('active');
        document.getElementById('design-area').classList.add('active');
        
        // Carica la stanza
        this.loadRoom(room);
        this.loadArtworkList();
    }
    
    loadRoom(room) {
        const canvas = document.getElementById('room-canvas');
        canvas.innerHTML = `
            <div id="drop-canvas" style="
                background-image: url('${room.url}');
                background-size: cover;
                background-position: center;
                position: relative;
                width: 100%;
                min-height: 600px;
                cursor: crosshair;
            "></div>
        `;
        
        // Setup drop zone
        const dropCanvas = document.getElementById('drop-canvas');
        dropCanvas.addEventListener('dragover', (e) => {
            if (!this.resizeMode) {
                e.preventDefault();
            }
        });
        
        dropCanvas.addEventListener('drop', (e) => {
            if (this.resizeMode || !this.draggedArtwork) return;
            e.preventDefault();
            
            const rect = dropCanvas.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width) * 100;
            const y = ((e.clientY - rect.top) / rect.height) * 100;
            
            this.placeArtwork(this.draggedArtwork, x, y);
            this.draggedArtwork = null;
        });
    }
    
    loadArtworkList() {
        const list = document.getElementById('artwork-list');
        if (!this.galleryData || !this.galleryData.images) return;
        
        list.innerHTML = this.galleryData.images.map((artwork, index) => `
            <div class="artwork-item" draggable="true" data-artwork-index="${index}">
                <img src="${artwork.medium || artwork.url}" alt="${artwork.title}">
                <span>${artwork.title}</span>
            </div>
        `).join('');
        
        // Aggiungi supporto touch per ogni artwork item
        setTimeout(() => {
            document.querySelectorAll('.artwork-item').forEach(item => {
                this.addArtworkItemTouchSupport(item);
            });
        }, 100);
    }
    
    addArtworkItemTouchSupport(item) {
        let touchStartX, touchStartY;
        let isDraggingFromSidebar = false;
        
        item.addEventListener('touchstart', (e) => {
            if (this.resizeMode) return;
            
            const touch = e.touches[0];
            touchStartX = touch.clientX;
            touchStartY = touch.clientY;
            isDraggingFromSidebar = true;
            
            this.draggedArtwork = {
                artworkIndex: item.dataset.artworkIndex
            };
            
            item.style.opacity = '0.5';
        });
        
        item.addEventListener('touchmove', (e) => {
            if (!isDraggingFromSidebar) return;
            e.preventDefault();
        }, { passive: false });
        
        item.addEventListener('touchend', (e) => {
            if (!isDraggingFromSidebar || !this.draggedArtwork) return;
            
            const touch = e.changedTouches[0];
            const canvas = document.getElementById('drop-canvas');
            const canvasRect = canvas.getBoundingClientRect();
            
            // Verifica se il touch è finito sul canvas
            if (touch.clientX >= canvasRect.left && touch.clientX <= canvasRect.right &&
                touch.clientY >= canvasRect.top && touch.clientY <= canvasRect.bottom) {
                
                const x = ((touch.clientX - canvasRect.left) / canvasRect.width) * 100;
                const y = ((touch.clientY - canvasRect.top) / canvasRect.height) * 100;
                
                this.placeArtwork(this.draggedArtwork, x, y);
            }
            
            this.draggedArtwork = null;
            isDraggingFromSidebar = false;
            item.style.opacity = '1';
        });
    }
    
    placeArtwork(artworkData, xPercent, yPercent) {
        const canvas = document.getElementById('drop-canvas');
        const artwork = this.galleryData.images[parseInt(artworkData.artworkIndex)];
        if (!canvas || !artwork) return;
        
        const artworkEl = document.createElement('div');
        artworkEl.className = 'placed-artwork';
        artworkEl.style.cssText = `
            position: absolute;
            left: ${xPercent}%;
            top: ${yPercent}%;
            width: 150px;
            height: 200px;
            cursor: move;
            z-index: 10;
        `;
        
        artworkEl.innerHTML = `
            <img src="${artwork.url}" style="
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
                pointer-events: none;
            ">
        `;
        
        // Click per selezionare (solo in resize mode)
        artworkEl.addEventListener('click', (e) => {
            e.stopPropagation();
            if (this.resizeMode) {
                this.selectArtwork(artworkEl);
            }
        });
        
        // Drag per spostare (desktop)
        artworkEl.addEventListener('mousedown', (e) => {
            if (e.target.classList.contains('resize-handle') || this.resizeMode) return;
            this.startDrag(e, artworkEl);
        });
        
        // Touch support per mobile
        this.addTouchSupport(artworkEl);
        
        canvas.appendChild(artworkEl);
        this.artworks.push(artworkEl);
    }
    
    addTouchSupport(artworkEl) {
        let touchStartX, touchStartY, initialLeft, initialTop;
        let isTouchMoving = false;
        let touchStartTime = 0;
        
        artworkEl.addEventListener('touchstart', (e) => {
            touchStartTime = Date.now();
            
            // Se in resize mode, solo selezione
            if (this.resizeMode) {
                this.selectArtwork(artworkEl);
                return;
            }
            
            const touch = e.touches[0];
            touchStartX = touch.clientX;
            touchStartY = touch.clientY;
            
            const rect = artworkEl.getBoundingClientRect();
            const canvas = document.getElementById('drop-canvas');
            const canvasRect = canvas.getBoundingClientRect();
            
            initialLeft = ((rect.left + rect.width/2 - canvasRect.left) / canvasRect.width) * 100;
            initialTop = ((rect.top + rect.height/2 - canvasRect.top) / canvasRect.height) * 100;
            
            isTouchMoving = true;
            artworkEl.style.opacity = '0.7';
            artworkEl.style.zIndex = '999';
        }, { passive: true });
        
        artworkEl.addEventListener('touchmove', (e) => {
            if (!isTouchMoving || this.resizeMode) return;
            
            const touch = e.touches[0];
            const deltaX = touch.clientX - touchStartX;
            const deltaY = touch.clientY - touchStartY;
            
            const canvas = document.getElementById('drop-canvas');
            const canvasRect = canvas.getBoundingClientRect();
            
            const deltaXPercent = (deltaX / canvasRect.width) * 100;
            const deltaYPercent = (deltaY / canvasRect.height) * 100;
            
            const newLeft = Math.max(0, Math.min(100, initialLeft + deltaXPercent));
            const newTop = Math.max(0, Math.min(100, initialTop + deltaYPercent));
            
            artworkEl.style.left = newLeft + '%';
            artworkEl.style.top = newTop + '%';
            
            e.preventDefault();
        }, { passive: false });
        
        artworkEl.addEventListener('touchend', (e) => {
            if (!isTouchMoving) return;
            
            const touchDuration = Date.now() - touchStartTime;
            
            // Se è stato un tap veloce (< 200ms) e non si è mosso molto
            if (touchDuration < 200 && this.resizeMode) {
                e.stopPropagation();
                this.selectArtwork(artworkEl);
            }
            
            isTouchMoving = false;
            artworkEl.style.opacity = '1';
            artworkEl.style.zIndex = '10';
        });
    }
    
    selectArtwork(artwork) {
        // Deseleziona precedente
        if (this.selectedArtwork) {
            this.selectedArtwork.classList.remove('selected');
            this.selectedArtwork.querySelectorAll('.resize-handle').forEach(h => h.remove());
        }
        
        // Seleziona nuovo
        this.selectedArtwork = artwork;
        artwork.classList.add('selected');
        
        // Aggiungi handle
        const positions = ['nw', 'n', 'ne', 'e', 'se', 's', 'sw', 'w'];
        positions.forEach(pos => {
            const handle = document.createElement('div');
            handle.className = `resize-handle resize-handle-${pos}`;
            
            // Mouse events (desktop)
            handle.addEventListener('mousedown', (e) => {
                e.stopPropagation();
                e.preventDefault();
                this.startResize(e, artwork, pos);
            });
            
            // Touch events (mobile)
            handle.addEventListener('touchstart', (e) => {
                e.stopPropagation();
                e.preventDefault();
                const touch = e.touches[0];
                this.startResize(touch, artwork, pos, true);
            }, { passive: false });
            
            artwork.appendChild(handle);
        });
    }
    
    startResize(e, artwork, position, isTouch = false) {
        const canvas = document.getElementById('drop-canvas');
        const canvasRect = canvas.getBoundingClientRect();
        const startRect = artwork.getBoundingClientRect();
        
        const startX = isTouch ? e.clientX : e.clientX;
        const startY = isTouch ? e.clientY : e.clientY;
        const startWidth = startRect.width;
        const startHeight = startRect.height;
        const startLeft = startRect.left - canvasRect.left;
        const startTop = startRect.top - canvasRect.top;
        
        const onMove = (e) => {
            const clientX = isTouch ? e.touches[0].clientX : e.clientX;
            const clientY = isTouch ? e.touches[0].clientY : e.clientY;
            
            const dx = clientX - startX;
            const dy = clientY - startY;
            
            let newWidth = startWidth;
            let newHeight = startHeight;
            let newLeft = startLeft;
            let newTop = startTop;
            
            if (position.includes('e')) newWidth = startWidth + dx;
            if (position.includes('w')) {
                newWidth = startWidth - dx;
                newLeft = startLeft + dx;
            }
            if (position.includes('s')) newHeight = startHeight + dy;
            if (position.includes('n')) {
                newHeight = startHeight - dy;
                newTop = startTop + dy;
            }
            
            // Dimensioni minime
            const minSize = window.innerWidth < 768 ? 80 : 50;
            
            if (newWidth > minSize && newHeight > minSize) {
                artwork.style.width = `${newWidth}px`;
                artwork.style.height = `${newHeight}px`;
                artwork.style.left = `${newLeft}px`;
                artwork.style.top = `${newTop}px`;
            }
        };
        
        const onUp = () => {
            if (isTouch) {
                document.removeEventListener('touchmove', onMove);
                document.removeEventListener('touchend', onUp);
            } else {
                document.removeEventListener('mousemove', onMove);
                document.removeEventListener('mouseup', onUp);
            }
        };
        
        if (isTouch) {
            document.addEventListener('touchmove', onMove, { passive: false });
            document.addEventListener('touchend', onUp);
        } else {
            document.addEventListener('mousemove', onMove);
            document.addEventListener('mouseup', onUp);
        }
    }
    
    startDrag(e, artwork) {
        e.preventDefault();
        e.stopPropagation();
        
        const canvas = document.getElementById('drop-canvas');
        const canvasRect = canvas.getBoundingClientRect();
        const artworkRect = artwork.getBoundingClientRect();
        const offsetX = e.clientX - artworkRect.left;
        const offsetY = e.clientY - artworkRect.top;
        
        const onMove = (e) => {
            const x = e.clientX - canvasRect.left - offsetX;
            const y = e.clientY - canvasRect.top - offsetY;
            artwork.style.left = `${x}px`;
            artwork.style.top = `${y}px`;
        };
        
        const onUp = () => {
            document.removeEventListener('mousemove', onMove);
            document.removeEventListener('mouseup', onUp);
        };
        
        document.addEventListener('mousemove', onMove);
        document.addEventListener('mouseup', onUp);
    }
    
    toggleResizeMode() {
        this.resizeMode = !this.resizeMode;
        const btn = document.getElementById('btn-resize');
        const canvas = document.getElementById('drop-canvas');
        
        if (this.resizeMode) {
            btn.classList.add('active');
            if (canvas) canvas.style.cursor = 'default';
        } else {
            btn.classList.remove('active');
            if (canvas) canvas.style.cursor = 'crosshair';
            if (this.selectedArtwork) {
                this.selectedArtwork.classList.remove('selected');
                this.selectedArtwork.querySelectorAll('.resize-handle').forEach(h => h.remove());
                this.selectedArtwork = null;
            }
        }
    }
    
    showSaveMenu(event) {
        // Rimuovi menu esistente
        this.hideSaveMenu();
        
        const menu = document.createElement('div');
        menu.className = 'save-menu';
        menu.innerHTML = `
            <div class="save-menu-option save-option-png">
                <i class="fas fa-image"></i>
                <span>Salva come PNG</span>
                <small>Immagine ad alta qualità</small>
            </div>
            <div class="save-menu-option save-option-pdf">
                <i class="fas fa-file-pdf"></i>
                <span>Salva come PDF</span>
                <small>Documento stampabile</small>
            </div>
        `;
        
        document.body.appendChild(menu);
        
        // Posiziona il menu vicino al pulsante
        const btn = document.getElementById('btn-save');
        const rect = btn.getBoundingClientRect();
        menu.style.position = 'fixed';
        menu.style.bottom = (window.innerHeight - rect.top + 10) + 'px';
        menu.style.right = (window.innerWidth - rect.right) + 'px';
        
        // Chiudi menu se si clicca fuori
        setTimeout(() => {
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.save-menu') && !e.target.closest('#btn-save')) {
                    this.hideSaveMenu();
                }
            }, { once: true });
        }, 100);
    }
    
    hideSaveMenu() {
        const menu = document.querySelector('.save-menu');
        if (menu) menu.remove();
    }
    
    async saveAsPNG() {
        const dropCanvas = document.getElementById('drop-canvas');
        if (!dropCanvas) return;
        
        console.log('Gallery2D: Saving as PNG...');
        
        try {
            // Crea un canvas HTML5 per disegnare manualmente
            const exportCanvas = document.createElement('canvas');
            const ctx = exportCanvas.getContext('2d');
            
            // Ottieni dimensioni del drop-canvas
            const rect = dropCanvas.getBoundingClientRect();
            const scale = 2; // Alta qualità
            exportCanvas.width = rect.width * scale;
            exportCanvas.height = rect.height * scale;
            ctx.scale(scale, scale);
            
            // Sfondo bianco
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, rect.width, rect.height);
            
            // Carica e disegna l'immagine di sfondo della stanza
            const bgStyle = window.getComputedStyle(dropCanvas);
            const bgImage = bgStyle.backgroundImage;
            if (bgImage && bgImage !== 'none') {
                const bgUrl = bgImage.slice(5, -2); // Rimuove 'url("' e '")'
                await this.drawImageOnCanvas(ctx, bgUrl, 0, 0, rect.width, rect.height);
            }
            
            // Disegna ogni artwork nella posizione corretta
            const artworks = dropCanvas.querySelectorAll('.placed-artwork');
            for (const artwork of artworks) {
                const img = artwork.querySelector('img');
                if (!img) continue;
                
                const artworkRect = artwork.getBoundingClientRect();
                const x = artworkRect.left - rect.left;
                const y = artworkRect.top - rect.top;
                const width = artworkRect.width;
                const height = artworkRect.height;
                
                await this.drawImageOnCanvas(ctx, img.src, x, y, width, height);
            }
            
            // Download
            const link = document.createElement('a');
            link.download = `galleria-${Date.now()}.png`;
            link.href = exportCanvas.toDataURL('image/png', 1.0);
            link.click();
            
            console.log('Gallery2D: PNG saved successfully');
        } catch (error) {
            console.error('Gallery2D: Error saving PNG', error);
            alert('Errore nel salvataggio PNG: ' + error.message);
        }
    }
    
    drawImageOnCanvas(ctx, src, x, y, width, height) {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.crossOrigin = 'anonymous';
            
            img.onload = () => {
                ctx.drawImage(img, x, y, width, height);
                resolve();
            };
            
            img.onerror = (error) => {
                console.warn('Failed to load image:', src, error);
                // Disegna un placeholder se l'immagine fallisce
                ctx.fillStyle = '#cccccc';
                ctx.fillRect(x, y, width, height);
                ctx.fillStyle = '#666666';
                ctx.font = '14px Arial';
                ctx.textAlign = 'center';
                ctx.fillText('Immagine non caricata', x + width/2, y + height/2);
                resolve();
            };
            
            img.src = src;
        });
    }
    
    async saveAsPDF() {
        const dropCanvas = document.getElementById('drop-canvas');
        if (!dropCanvas) return;
        
        console.log('Gallery2D: Saving as PDF...');
        
        try {
            // Carica jsPDF
            const jsPDF = await this.loadJsPDF();
            
            // Crea un canvas HTML5 per disegnare manualmente
            const exportCanvas = document.createElement('canvas');
            const ctx = exportCanvas.getContext('2d');
            
            // Ottieni dimensioni del drop-canvas
            const rect = dropCanvas.getBoundingClientRect();
            const scale = 2; // Alta qualità
            exportCanvas.width = rect.width * scale;
            exportCanvas.height = rect.height * scale;
            ctx.scale(scale, scale);
            
            // Sfondo bianco
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, rect.width, rect.height);
            
            // Carica e disegna l'immagine di sfondo della stanza
            const bgStyle = window.getComputedStyle(dropCanvas);
            const bgImage = bgStyle.backgroundImage;
            if (bgImage && bgImage !== 'none') {
                const bgUrl = bgImage.slice(5, -2);
                await this.drawImageOnCanvas(ctx, bgUrl, 0, 0, rect.width, rect.height);
            }
            
            // Disegna ogni artwork nella posizione corretta
            const artworks = dropCanvas.querySelectorAll('.placed-artwork');
            for (const artwork of artworks) {
                const img = artwork.querySelector('img');
                if (!img) continue;
                
                const artworkRect = artwork.getBoundingClientRect();
                const x = artworkRect.left - rect.left;
                const y = artworkRect.top - rect.top;
                const width = artworkRect.width;
                const height = artworkRect.height;
                
                await this.drawImageOnCanvas(ctx, img.src, x, y, width, height);
            }
            
            // Crea PDF
            const imgWidth = exportCanvas.width / scale;
            const imgHeight = exportCanvas.height / scale;
            const ratio = imgWidth / imgHeight;
            
            let pdfWidth, pdfHeight, orientation;
            if (ratio > 1.4) {
                orientation = 'landscape';
                pdfWidth = 297; // A4 landscape width in mm
                pdfHeight = pdfWidth / ratio;
            } else {
                orientation = 'portrait';
                pdfHeight = 297; // A4 portrait height in mm
                pdfWidth = pdfHeight * ratio;
            }
            
            const pdf = new jsPDF.jsPDF(orientation, 'mm', 'a4');
            const imgData = exportCanvas.toDataURL('image/jpeg', 0.95);
            
            pdf.addImage(imgData, 'JPEG', 0, 0, pdfWidth, pdfHeight);
            pdf.save(`galleria-${Date.now()}.pdf`);
            
            console.log('Gallery2D: PDF saved successfully');
        } catch (error) {
            console.error('Gallery2D: Error saving PDF', error);
            alert('Errore nel salvataggio PDF: ' + error.message);
        }
    }
    
    loadHtml2Canvas() {
        return new Promise((resolve, reject) => {
            if (window.html2canvas) {
                resolve(window.html2canvas);
                return;
            }
            
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
            script.onload = () => resolve(window.html2canvas);
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }
    
    loadJsPDF() {
        return new Promise((resolve, reject) => {
            if (window.jsPDF) {
                resolve(window.jsPDF);
                return;
            }
            
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';
            script.onload = () => resolve(window.jspdf);
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }
    
    backToRooms() {
        document.getElementById('design-area').classList.remove('active');
        document.getElementById('room-selection').classList.add('active');
        this.artworks = [];
        this.selectedArtwork = null;
        this.resizeMode = false;
    }
}

// Inizializzazione
document.addEventListener('DOMContentLoaded', () => {
    console.log('Gallery2D: DOM loaded');
    
    // Leggi galleryData dal JSON nel widget
    const galleryDataElement = document.getElementById('gallery-data');
    let galleryData = null;
    
    if (galleryDataElement) {
        try {
            galleryData = JSON.parse(galleryDataElement.textContent);
            console.log('Gallery2D: galleryData loaded', galleryData);
        } catch (e) {
            console.error('Gallery2D: Error parsing gallery data', e);
        }
    }
    
    // Verifica che roomsData sia disponibile (passato da wp_localize_script)
    if (typeof roomsData === 'undefined') {
        console.error('Gallery2D: roomsData not found!');
        return;
    }
    
    if (!galleryData) {
        console.error('Gallery2D: galleryData not found!');
        return;
    }
    
    console.log('Gallery2D: Initializing with roomsData', roomsData);
    window.gallery2D = new Gallery2D(galleryData);
});
