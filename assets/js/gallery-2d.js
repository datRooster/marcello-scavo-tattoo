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
        
        // Drag per spostare
        artworkEl.addEventListener('mousedown', (e) => {
            if (e.target.classList.contains('resize-handle') || this.resizeMode) return;
            this.startDrag(e, artworkEl);
        });
        
        canvas.appendChild(artworkEl);
        this.artworks.push(artworkEl);
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
            handle.addEventListener('mousedown', (e) => {
                e.stopPropagation();
                e.preventDefault();
                this.startResize(e, artwork, pos);
            });
            artwork.appendChild(handle);
        });
    }
    
    startResize(e, artwork, position) {
        const canvas = document.getElementById('drop-canvas');
        const canvasRect = canvas.getBoundingClientRect();
        const startRect = artwork.getBoundingClientRect();
        
        const startX = e.clientX;
        const startY = e.clientY;
        const startWidth = startRect.width;
        const startHeight = startRect.height;
        const startLeft = startRect.left - canvasRect.left;
        const startTop = startRect.top - canvasRect.top;
        
        const onMove = (e) => {
            const dx = e.clientX - startX;
            const dy = e.clientY - startY;
            
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
            
            if (newWidth > 50 && newHeight > 50) {
                artwork.style.width = `${newWidth}px`;
                artwork.style.height = `${newHeight}px`;
                artwork.style.left = `${newLeft}px`;
                artwork.style.top = `${newTop}px`;
            }
        };
        
        const onUp = () => {
            document.removeEventListener('mousemove', onMove);
            document.removeEventListener('mouseup', onUp);
        };
        
        document.addEventListener('mousemove', onMove);
        document.addEventListener('mouseup', onUp);
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
        const canvas = document.getElementById('drop-canvas');
        if (!canvas) return;
        
        console.log('Gallery2D: Saving as PNG...');
        
        // Nascondi temporaneamente i resize handles
        const handles = canvas.querySelectorAll('.resize-handle');
        handles.forEach(h => h.style.display = 'none');
        
        try {
            // Usa html2canvas per convertire il canvas in immagine
            const html2canvas = await this.loadHtml2Canvas();
            const canvasImg = await html2canvas(canvas, {
                backgroundColor: null,
                scale: 2, // Qualità alta
                useCORS: true,
                allowTaint: true
            });
            
            // Download
            const link = document.createElement('a');
            link.download = `galleria-${Date.now()}.png`;
            link.href = canvasImg.toDataURL('image/png');
            link.click();
            
            console.log('Gallery2D: PNG saved successfully');
        } catch (error) {
            console.error('Gallery2D: Error saving PNG', error);
            alert('Errore nel salvataggio PNG. Riprova.');
        } finally {
            // Ripristina i handles
            handles.forEach(h => h.style.display = '');
        }
    }
    
    async saveAsPDF() {
        const canvas = document.getElementById('drop-canvas');
        if (!canvas) return;
        
        console.log('Gallery2D: Saving as PDF...');
        
        // Nascondi temporaneamente i resize handles
        const handles = canvas.querySelectorAll('.resize-handle');
        handles.forEach(h => h.style.display = 'none');
        
        try {
            // Carica le librerie necessarie
            const [html2canvas, jsPDF] = await Promise.all([
                this.loadHtml2Canvas(),
                this.loadJsPDF()
            ]);
            
            // Converti in canvas
            const canvasImg = await html2canvas(canvas, {
                backgroundColor: '#ffffff',
                scale: 2,
                useCORS: true,
                allowTaint: true
            });
            
            // Crea PDF in formato landscape per gallerie larghe
            const imgWidth = canvasImg.width;
            const imgHeight = canvasImg.height;
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
            const imgData = canvasImg.toDataURL('image/jpeg', 0.95);
            
            pdf.addImage(imgData, 'JPEG', 0, 0, pdfWidth, pdfHeight);
            pdf.save(`galleria-${Date.now()}.pdf`);
            
            console.log('Gallery2D: PDF saved successfully');
        } catch (error) {
            console.error('Gallery2D: Error saving PDF', error);
            alert('Errore nel salvataggio PDF. Riprova.');
        } finally {
            // Ripristina i handles
            handles.forEach(h => h.style.display = '');
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
