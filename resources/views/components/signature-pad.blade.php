@props(['id' => 'signature', 'placeholder' => 'Tanda Tangan di sini'])

<div x-data="{
    value: @entangle($attributes->wire('model')),
    isDrawing: false,
    ctx: null,
    lastPos: null,
    
    init() {
        const canvas = this.$refs.canvas;
        this.ctx = canvas.getContext('2d');
        
        const resizeCanvas = () => {
            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;
            
            this.ctx.lineWidth = 3;
            this.ctx.lineCap = 'round';
            this.ctx.lineJoin = 'round';
            
            const isDark = document.documentElement.classList.contains('dark');
            this.ctx.strokeStyle = isDark ? '#FFFFFF' : '#0F172A';
        };
        
        setTimeout(resizeCanvas, 100);
        window.addEventListener('resize', resizeCanvas);
        
        this.$watch('value', (val) => {
            if (!val && this.ctx) {
                const canvas = this.$refs.canvas;
                this.ctx.clearRect(0, 0, canvas.width, canvas.height);
            }
        });
    },
    
    getMousePos(e) {
        const rect = this.$refs.canvas.getBoundingClientRect();
        return {
            x: e.clientX - rect.left,
            y: e.clientY - rect.top
        };
    },
    
    getTouchPos(e) {
        const rect = this.$refs.canvas.getBoundingClientRect();
        return {
            x: e.touches[0].clientX - rect.left,
            y: e.touches[0].clientY - rect.top
        };
    },
    
    startDrawing(e) {
        this.isDrawing = true;
        this.lastPos = this.getMousePos(e);
        const isDark = document.documentElement.classList.contains('dark');
        this.ctx.strokeStyle = isDark ? '#FFFFFF' : '#0F172A';
    },
    
    startTouch(e) {
        this.isDrawing = true;
        this.lastPos = this.getTouchPos(e);
        const isDark = document.documentElement.classList.contains('dark');
        this.ctx.strokeStyle = isDark ? '#FFFFFF' : '#0F172A';
    },
    
    draw(e) {
        if (!this.isDrawing) return;
        const currentPos = this.getMousePos(e);
        
        this.ctx.beginPath();
        this.ctx.moveTo(this.lastPos.x, this.lastPos.y);
        this.ctx.lineTo(currentPos.x, currentPos.y);
        this.ctx.stroke();
        
        this.lastPos = currentPos;
    },
    
    drawTouch(e) {
        if (!this.isDrawing) return;
        const currentPos = this.getTouchPos(e);
        
        this.ctx.beginPath();
        this.ctx.moveTo(this.lastPos.x, this.lastPos.y);
        this.ctx.lineTo(currentPos.x, currentPos.y);
        this.ctx.stroke();
        
        this.lastPos = currentPos;
    },
    
    stopDrawing() {
        if (!this.isDrawing) return;
        this.isDrawing = false;
        
        const tempCanvas = document.createElement('canvas');
        tempCanvas.width = this.$refs.canvas.width;
        tempCanvas.height = this.$refs.canvas.height;
        const tempCtx = tempCanvas.getContext('2d');
        
        const isDark = document.documentElement.classList.contains('dark');
        
        // Fill background: black for dark mode (so it inverts to white), white for light mode
        tempCtx.fillStyle = isDark ? '#000000' : '#FFFFFF';
        tempCtx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);
        
        // Draw the strokes
        tempCtx.drawImage(this.$refs.canvas, 0, 0);
        
        if (isDark) {
            const imgData = tempCtx.getImageData(0, 0, tempCanvas.width, tempCanvas.height);
            const data = imgData.data;
            for (let i = 0; i < data.length; i += 4) {
                data[i] = 255 - data[i];
                data[i+1] = 255 - data[i+1];
                data[i+2] = 255 - data[i+2];
            }
            tempCtx.putImageData(imgData, 0, 0);
        }
        
        this.value = tempCanvas.toDataURL('image/png');
    },
    
    clear() {
        const canvas = this.$refs.canvas;
        this.ctx.clearRect(0, 0, canvas.width, canvas.height);
        this.value = null;
    }
}" class="relative w-full border border-slate-200 dark:border-white/10 rounded-2xl bg-white dark:bg-slate-900 overflow-hidden shadow-sm group/sig">
    <canvas id="{{ $id }}" x-ref="canvas" 
        class="w-full h-48 cursor-crosshair touch-none" style="touch-action: none;"
        @mousedown="startDrawing($event)"
        @mousemove="draw($event)"
        @mouseup="stopDrawing()"
        @mouseleave="stopDrawing()"
        @touchstart.prevent="startTouch($event)"
        @touchmove.prevent="drawTouch($event)"
        @touchend.prevent="stopDrawing()"></canvas>
    
    <div class="absolute bottom-3 left-3 pointer-events-none" x-show="!value">
        <span class="text-xs text-slate-400 font-medium">{{ $placeholder }}</span>
    </div>
    
    <button type="button" @click="clear()" x-show="value" x-transition class="absolute top-3 right-3 p-1.5 bg-red-50 hover:bg-red-100 text-red-500 rounded-lg transition-colors border border-red-100" title="Hapus Tanda Tangan">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
    </button>
</div>
