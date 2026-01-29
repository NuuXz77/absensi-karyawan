<div x-data="{
    stream: null,
    videoElement: null,
    canvasElement: null,
    validationInterval: null,
    faceDetectionInterval: null,
    detectionStartTime: null,
    detectionDuration: 0,
    minDetectionTime: 5,
    faceApiReady: false,
    loadingStatus: 'Memuat kamera...',
    init() {
        this.startCamera();
        // Delay loading face-api to ensure library is loaded
        setTimeout(() => this.loadFaceAPI(), 500);
        
        // Listen for Livewire event to restart camera
        Livewire.on('restartCamera', () => {
            console.log('🔄 Restarting camera...');
            this.restartCamera();
        });
    },
    restartCamera() {
        // Reset state
        this.detectionStartTime = null;
        this.detectionDuration = 0;
        this.loadingStatus = 'Memuat kamera...';
        
        // Stop existing camera and intervals
        this.stopCamera();
        this.stopValidation();
        
        // Restart camera
        this.startCamera();
        
        // Restart face detection and validation if face API is ready
        if (this.faceApiReady) {
            setTimeout(() => {
                this.startRealTimeFaceDetection();
                this.startValidation();
            }, 1000);
        }
    },
    async loadFaceAPI() {
        try {
            // Check if faceapi is available
            if (typeof faceapi === 'undefined') {
                console.error('face-api library not loaded yet, retrying...');
                this.loadingStatus = 'Menunggu library...';
                
                // Retry after 1 second
                setTimeout(() => this.loadFaceAPI(), 1000);
                return;
            }
            
            this.loadingStatus = 'Mengunduh model AI...';
            const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model/';
            
            console.log('🔄 Loading Face-API models...');
            
            // Load models with timeout
            const loadPromise = Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
                faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
            ]);
            
            const timeoutPromise = new Promise((_, reject) => 
                setTimeout(() => reject(new Error('Timeout loading models')), 30000)
            );
            
            await Promise.race([loadPromise, timeoutPromise]);
            
            this.faceApiReady = true;
            this.loadingStatus = 'Siap!';
            console.log('✓ Face-API models loaded');
            
            // Start real-time detection and validation
            this.startRealTimeFaceDetection();
            this.startValidation();
        } catch (error) {
            console.error('Error loading Face-API:', error);
            this.loadingStatus = 'Gagal memuat model';
            
            // Show more specific error
            if (error.message.includes('Timeout')) {
                alert('Timeout saat mengunduh model Face Recognition. Periksa koneksi internet Anda dan refresh halaman.');
            } else {
                alert('Gagal memuat model Face Recognition: ' + error.message + '. Silakan refresh halaman.');
            }
        }
    },
    async startCamera() {
        try {
            this.stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'user',
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                }
            });
            this.videoElement = this.$refs.video;
            this.canvasElement = this.$refs.canvas;
            this.videoElement.srcObject = this.stream;
            await this.videoElement.play();
            
            // Wait for video to be ready and set canvas size
            const setupCanvas = () => {
                if (this.videoElement.videoWidth > 0 && this.videoElement.videoHeight > 0) {
                    // Set canvas internal resolution to match video
                    this.canvasElement.width = this.videoElement.videoWidth;
                    this.canvasElement.height = this.videoElement.videoHeight;
                    
                    console.log('✓ Canvas ready:', {
                        videoSize: `${this.videoElement.videoWidth}x${this.videoElement.videoHeight}`,
                        canvasSize: `${this.canvasElement.width}x${this.canvasElement.height}`,
                        canvasStyle: `${this.canvasElement.style.width}x${this.canvasElement.style.height}`
                    });
                    
                    // Start face detection if face API is already ready
                    if (this.faceApiReady) {
                        this.startRealTimeFaceDetection();
                        this.startValidation();
                    }
                } else {
                    // Retry if video not ready
                    console.log('Video not ready, retrying...');
                    setTimeout(setupCanvas, 100);
                }
            };
            
            setTimeout(setupCanvas, 500);
        } catch (error) {
            alert('Tidak dapat mengakses kamera: ' + error.message);
        }
    },
    stopCamera() {
        if (this.stream) {
            this.stream.getTracks().forEach(track => track.stop());
            this.stream = null;
        }
        if (this.videoElement) {
            this.videoElement.srcObject = null;
        }
    },
    startRealTimeFaceDetection() {
        if (!this.faceApiReady) {
            console.warn('Face API not ready yet');
            return;
        }
        
        console.log('🎬 Starting real-time face detection...');
        
        const detectFace = async () => {
            if (!this.videoElement || this.videoElement.readyState !== 4) {
                return;
            }
            
            if (!this.canvasElement || this.canvasElement.width === 0) {
                console.warn('Canvas not ready yet');
                return;
            }
            
            try {
                const detection = await faceapi
                    .detectSingleFace(this.videoElement, new faceapi.TinyFaceDetectorOptions({
                        inputSize: 416,
                        scoreThreshold: 0.5
                    }))
                    .withFaceLandmarks()
                    .withFaceDescriptor();
                
                // Always clear canvas first
                const ctx = this.canvasElement.getContext('2d');
                if (ctx) {
                    ctx.clearRect(0, 0, this.canvasElement.width, this.canvasElement.height);
                }
                
                if (detection) {
                    console.log('✓ Face detected!', detection.detection.box);
                    
                    // Draw bounding box
                    this.drawFaceBox(detection);
                    
                    // Start/update detection timer
                    if (!this.detectionStartTime) {
                        this.detectionStartTime = Date.now();
                        console.log('⏱️ Timer started');
                    }
                    this.detectionDuration = Math.floor((Date.now() - this.detectionStartTime) / 1000);
                    
                    // Get embedding for validation
                    const embedding = Array.from(detection.descriptor);
                    console.log('📊 Embedding length:', embedding.length);
                    @this.call('validateFace', embedding);
                } else {
                    // Reset timer if no face detected
                    if (this.detectionStartTime) {
                        console.log('⚠️ Face lost, resetting timer');
                    }
                    this.detectionStartTime = null;
                    this.detectionDuration = 0;
                    @this.set('isFaceDetected', false);
                    @this.set('faceConfidence', 0);
                }
            } catch (error) {
                console.error('❌ Face detection error:', error);
            }
        };
        
        // Run detection every 100ms for smooth real-time tracking
        this.faceDetectionInterval = setInterval(detectFace, 100);
        
        // Run initial detection immediately
        detectFace();
    },
    drawFaceBox(detection) {
        if (!this.canvasElement) {
            console.warn('⚠️ Canvas element not found');
            return;
        }
        
        if (this.canvasElement.width === 0 || this.canvasElement.height === 0) {
            console.warn('⚠️ Canvas size is 0');
            return;
        }
        
        const ctx = this.canvasElement.getContext('2d');
        if (!ctx) {
            console.warn('⚠️ Cannot get canvas context');
            return;
        }
        
        const box = detection.detection.box;
        
        // Use coordinates directly (canvas is mirrored same as video)
        const x = box.x;
        const y = box.y;
        const width = box.width;
        const height = box.height;
        
        console.log('📦 Drawing box:', { 
            canvasSize: `${this.canvasElement.width}x${this.canvasElement.height}`,
            boxPosition: `(${Math.round(x)}, ${Math.round(y)})`,
            boxSize: `${Math.round(width)}x${Math.round(height)}`,
            videoSize: `${this.videoElement.videoWidth}x${this.videoElement.videoHeight}`
        });
        
        // Save context state
        ctx.save();
        
        // Draw semi-transparent background
        ctx.fillStyle = 'rgba(0, 255, 0, 0.2)';
        ctx.fillRect(x, y, width, height);
        
        // Draw main box with glow effect
        ctx.shadowColor = '#00ff00';
        ctx.shadowBlur = 20;
        ctx.strokeStyle = '#00ff00';
        ctx.lineWidth = 4;
        ctx.strokeRect(x, y, width, height);
        
        // Reset shadow
        ctx.shadowColor = 'transparent';
        ctx.shadowBlur = 0;
        
        // Draw corners (bigger and more visible)
        const cornerLength = 30;
        ctx.strokeStyle = '#00ff00';
        ctx.lineWidth = 6;
        ctx.lineCap = 'round';
        
        // Top-left
        ctx.beginPath();
        ctx.moveTo(x, y + cornerLength);
        ctx.lineTo(x, y);
        ctx.lineTo(x + cornerLength, y);
        ctx.stroke();
        
        // Top-right
        ctx.beginPath();
        ctx.moveTo(x + width - cornerLength, y);
        ctx.lineTo(x + width, y);
        ctx.lineTo(x + width, y + cornerLength);
        ctx.stroke();
        
        // Bottom-left
        ctx.beginPath();
        ctx.moveTo(x, y + height - cornerLength);
        ctx.lineTo(x, y + height);
        ctx.lineTo(x + cornerLength, y + height);
        ctx.stroke();
        
        // Bottom-right
        ctx.beginPath();
        ctx.moveTo(x + width - cornerLength, y + height);
        ctx.lineTo(x + width, y + height);
        ctx.lineTo(x + width, y + height - cornerLength);
        ctx.stroke();
        
        // Draw label
        ctx.font = 'bold 16px Arial';
        ctx.fillStyle = '#00ff00';
        ctx.fillText('✓ Wajah Terdeteksi', x + 5, y - 10);
        
        // Restore context state
        ctx.restore();
        
        console.log('✅ Box drawn successfully at', `(${Math.round(x)}, ${Math.round(y)})`);
    },
    startValidation() {
        this.validationInterval = setInterval(() => {
            // Validasi lokasi
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        @this.call('validateLocation', position.coords.latitude, position.coords.longitude);
                    },
                    (error) => {
                        console.error('Geolocation error:', error);
                    }
                );
            }
            
            // Validasi waktu
            @this.call('validateTime');
        }, 2000);
    },
    stopValidation() {
        if (this.validationInterval) {
            clearInterval(this.validationInterval);
            this.validationInterval = null;
        }
        if (this.faceDetectionInterval) {
            clearInterval(this.faceDetectionInterval);
            this.faceDetectionInterval = null;
        }
    },
    canCapture() {
        const canCapture = @this.isLocationValid && 
                          @this.isFaceDetected && 
                          @this.faceConfidence >= 60 && 
                          this.detectionDuration >= this.minDetectionTime;
        
        if (canCapture) {
            console.log('✅ Can capture! All conditions met');
        }
        
        return canCapture;
    },
    capturePhoto() {
        if (!this.canCapture()) {
            alert('Tunggu hingga semua validasi terpenuhi dan minimal ' + this.minDetectionTime + ' detik deteksi wajah');
            return;
        }
        
        const canvas = document.createElement('canvas');
        canvas.width = this.videoElement.videoWidth;
        canvas.height = this.videoElement.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(this.videoElement, 0, 0);
        const imageDataUrl = canvas.toDataURL('image/jpeg', 0.95);
        
        this.stopCamera();
        this.stopValidation();
        
        @this.call('capturePhoto', imageDataUrl);
    }
}" class="min-h-screen bg-base-200 pb-6">

    {{-- Step 1: Camera & Validation --}}
    @if($step === 'camera')
        <div class="space-y-4">
            {{-- 1. Header Informasi --}}
            <div class="card bg-base-100 border border-base-300 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-base-content/70">{{ $today->isoFormat('dddd, D MMMM YYYY') }}</div>
                        <div class="text-2xl font-bold text-primary font-mono" x-data="{ time: '{{ $now->format('H:i:s') }}' }"
                            x-init="setInterval(() => {
                                let date = new Date();
                                let hours = String(date.getHours()).padStart(2, '0');
                                let minutes = String(date.getMinutes()).padStart(2, '0');
                                let seconds = String(date.getSeconds()).padStart(2, '0');
                                time = `${hours}:${minutes}:${seconds}`;
                            }, 1000);" x-text="time">
                        </div>
                    </div>
                    @if($jadwal)
                        <div class="text-right">
                            <div class="badge badge-primary badge-lg">{{ $jadwal->shift->nama_shift ?? 'N/A' }}</div>
                            <div class="text-sm text-base-content/70 mt-1">
                                {{ $jadwal->shift->jam_masuk ?? '-' }} – {{ $jadwal->shift->jam_pulang ?? '-' }}
                            </div>
                        </div>
                    @endif
                </div>
                <div class="mt-2 text-center">
                    <div class="badge badge-lg {{ $type === 'masuk' ? 'badge-success' : 'badge-error' }}">
                        Absen {{ ucfirst($type) }}
                    </div>
                </div>
            </div>

            {{-- 2. Preview Kamera --}}
            <div class="card bg-base-100 border border-base-300 relative">
                <div class="relative aspect-[4/3] bg-gray-900 overflow-hidden">
                    <video x-ref="video" autoplay playsinline muted
                        class="absolute inset-0 w-full h-full object-cover transform scale-x-[-1]"></video>
                    
                    {{-- Canvas overlay untuk face detection box --}}
                    <canvas x-ref="canvas" 
                        class="absolute inset-0 w-full h-full pointer-events-none transform scale-x-[-1]" 
                        style="z-index: 10;">
                    </canvas>
                    
                    {{-- Face Detection Indicator --}}
                    @if($isFaceDetected)
                        <div class="absolute top-4 left-4 badge badge-success gap-2">
                            <x-heroicon-o-check-circle class="w-4 h-4" />
                            Wajah Terdeteksi
                        </div>
                    @else
                        <div class="absolute top-4 left-4 badge badge-warning gap-2">
                            <x-heroicon-o-exclamation-triangle class="w-4 h-4" />
                            Posisikan Wajah
                        </div>
                    @endif
                    
                    {{-- Timer Indicator --}}
                    <div class="absolute top-4 right-4">
                        <div class="badge badge-lg gap-2" 
                            :class="detectionDuration >= minDetectionTime ? 'badge-success' : 'badge-warning'">
                            <x-heroicon-o-clock class="w-4 h-4" />
                            <span x-text="detectionDuration + 's / ' + minDetectionTime + 's'"></span>
                        </div>
                    </div>
                    
                    {{-- Loading Indicator for Face API --}}
                    <div x-show="!faceApiReady" class="absolute inset-0 flex items-center justify-center bg-black/70 backdrop-blur-sm">
                        <div class="text-center text-white p-6 bg-base-100/10 rounded-lg border border-white/20">
                            <span class="loading loading-spinner loading-lg text-primary"></span>
                            <p class="mt-3 font-semibold" x-text="loadingStatus"></p>
                            <p class="mt-2 text-sm text-white/70">Harap tunggu...</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Status Validasi Realtime --}}
            <div class="card bg-base-100 border border-base-300 p-4">
                <div class="grid grid-cols-2 gap-3">
                    {{-- Lokasi --}}
                    <div class="flex items-center gap-2 p-3 bg-base-200 rounded-lg">
                        @if($isLocationValid)
                            <x-heroicon-o-check-circle class="w-5 h-5 text-success flex-shrink-0" />
                            <div class="flex-1 min-w-0">
                                <div class="text-xs text-base-content/60">Lokasi</div>
                                <div class="text-sm font-semibold text-success truncate">Valid</div>
                                @if($locationDistance !== null)
                                    <div class="text-xs text-base-content/60">{{ $locationDistance }}m</div>
                                @endif
                            </div>
                        @else
                            <x-heroicon-o-x-circle class="w-5 h-5 text-error flex-shrink-0" />
                            <div class="flex-1 min-w-0">
                                <div class="text-xs text-base-content/60">Lokasi</div>
                                <div class="text-sm font-semibold text-error truncate">Di Luar Radius</div>
                                @if($locationDistance !== null)
                                    <div class="text-xs text-base-content/60">{{ $locationDistance }}m</div>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Waktu --}}
                    <div class="flex items-center gap-2 p-3 bg-base-200 rounded-lg">
                        @if($isTimeValid)
                            <x-heroicon-o-check-circle class="w-5 h-5 text-success flex-shrink-0" />
                            <div class="flex-1 min-w-0">
                                <div class="text-xs text-base-content/60">Waktu</div>
                                <div class="text-sm font-semibold text-success truncate">Sesuai</div>
                            </div>
                        @else
                            <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-warning flex-shrink-0" />
                            <div class="flex-1 min-w-0">
                                <div class="text-xs text-base-content/60">Waktu</div>
                                <div class="text-sm font-semibold text-warning truncate">Terlambat</div>
                            </div>
                        @endif
                    </div>

                    {{-- Wajah --}}
                    <div class="flex items-center gap-2 p-3 bg-base-200 rounded-lg">
                        @if($isFaceDetected && $faceConfidence >= 60)
                            <x-heroicon-o-check-circle class="w-5 h-5 text-success flex-shrink-0" />
                            <div class="flex-1 min-w-0">
                                <div class="text-xs text-base-content/60">Wajah</div>
                                <div class="text-sm font-semibold text-success truncate">Cocok</div>
                                <div class="text-xs text-base-content/60">{{ round($faceConfidence) }}%</div>
                            </div>
                        @else
                            <x-heroicon-o-x-circle class="w-5 h-5 text-error flex-shrink-0" />
                            <div class="flex-1 min-w-0">
                                <div class="text-xs text-base-content/60">Wajah</div>
                                <div class="text-sm font-semibold text-error truncate">Tidak Cocok</div>
                                <div class="text-xs text-base-content/60">{{ round($faceConfidence) }}%</div>
                            </div>
                        @endif
                    </div>

                    {{-- Radius Info --}}
                    @if($jadwal && $jadwal->lokasi)
                        <div class="flex items-center gap-2 p-3 bg-base-200 rounded-lg">
                            <x-heroicon-o-map-pin class="w-5 h-5 text-info flex-shrink-0" />
                            <div class="flex-1 min-w-0">
                                <div class="text-xs text-base-content/60">Radius</div>
                                <div class="text-sm font-semibold truncate">{{ $jadwal->lokasi->radius ?? 100 }}m</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 4. Tombol Aksi Utama --}}
            <div class="card bg-base-100 p-6">
                <button 
                    @click="capturePhoto()"
                    class="btn btn-lg w-full gap-2"
                    :class="canCapture() ? 'btn-primary' : 'btn-disabled'"
                    :disabled="!canCapture()">
                    <x-heroicon-o-camera class="w-6 h-6" />
                    <span class="text-lg">Ambil Foto & Absen</span>
                </button>
                
                {{-- Alert Messages --}}
                <div class="space-y-2 mt-4">
                    {{-- Timer Alert --}}
                    <div x-show="detectionDuration < minDetectionTime && @this.isFaceDetected" 
                        class="alert alert-info py-2">
                        <x-heroicon-o-clock class="w-5 h-5" />
                        <span class="text-sm">
                            Tahan wajah Anda: <strong x-text="detectionDuration"></strong>/<strong x-text="minDetectionTime"></strong> detik
                        </span>
                    </div>
                    
                    {{-- Location Alert --}}
                    @if(!$isLocationValid)
                        <div class="alert alert-warning py-2">
                            <x-heroicon-o-map-pin class="w-5 h-5" />
                            <span class="text-sm">Pastikan Anda berada di lokasi yang valid</span>
                        </div>
                    @endif
                    
                    {{-- Face Alert --}}
                    @if(!$isFaceDetected || $faceConfidence < 60)
                        <div class="alert alert-warning py-2">
                            <x-heroicon-o-face-smile class="w-5 h-5" />
                            <span class="text-sm">Posisikan wajah Anda dengan benar dan tahan selama 5 detik (confidence: {{ round($faceConfidence) }}%)</span>
                        </div>
                    @endif
                    
                    {{-- Success Alert --}}
                    <div x-show="canCapture()" x-cloak class="alert alert-success py-2">
                        <x-heroicon-o-check-circle class="w-5 h-5" />
                        <span class="text-sm">Semua validasi terpenuhi! Klik tombol untuk absen</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Step 2: Preview & Confirm --}}
    @if($step === 'preview')
        <div class="space-y-4">
            {{-- Header --}}
            <div class="card bg-base-100 border border-base-300 p-6">
                <h2 class="text-xl font-bold text-center">Preview Foto</h2>
                <p class="text-sm text-center text-base-content/70">Periksa hasil foto sebelum konfirmasi</p>
            </div>

            {{-- 5. Preview Hasil Foto --}}
            <div class="card bg-base-100 border border-base-300 p-6">
                <div class="relative aspect-[4/3] bg-gray-900 rounded-lg overflow-hidden">
                    @if($photoData)
                        <img src="{{ $photoData }}" alt="Captured Photo" class="w-full h-full object-cover transform scale-x-[-1]">
                    @endif
                </div>

                {{-- Info Hasil --}}
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div class="bg-base-200 p-4 rounded-lg text-center">
                        <div class="text-sm text-base-content/60">Confidence</div>
                        <div class="text-2xl font-bold text-primary">{{ round($faceConfidence) }}%</div>
                    </div>
                    <div class="bg-base-200 p-4 rounded-lg text-center">
                        <div class="text-sm text-base-content/60">Waktu</div>
                        <div class="text-lg font-bold font-mono">{{ $now->format('H:i:s') }}</div>
                    </div>
                </div>
            </div>

            {{-- Aksi --}}
            <div class="card bg-base-100 border border-base-300 p-6">
                <div class="flex gap-3">
                    <button wire:click="retakePhoto" class="btn btn-outline btn-sm flex-1 gap-2">
                        <x-heroicon-o-arrow-path class="w-5 h-5" />
                        Ambil Ulang
                    </button>
                    <button wire:click="confirmAbsen" class="btn btn-success btn-sm flex-1 gap-2"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="confirmAbsen" class="flex items-center gap-2">
                            <x-heroicon-o-check class="w-5 h-5" />
                            Konfirmasi Absen
                        </span>
                        <span wire:loading wire:target="confirmAbsen" class="loading loading-spinner"></span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Step 3: Success --}}
    @if($step === 'success')
        <div class="space-y-4">
            {{-- 6. Status Hasil Absen --}}
            <div class="card bg-base-100 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-success/20 mb-4">
                    <x-heroicon-o-check-circle class="w-12 h-12 text-success" />
                </div>
                
                <h2 class="text-2xl font-bold text-success mb-2">Absen Berhasil!</h2>
                <p class="text-base-content/70 mb-6">{{ $statusMessage }}</p>

                @if($absensiResult)
                    <div class="card bg-base-200 border border-base-300 mb-6">
                        <div class="card-body">
                            <div class="grid grid-cols-1 gap-4 text-left">
                                <div class="flex justify-between items-center">
                                    <span class="text-base-content/70">Status:</span>
                                    <span class="badge {{ $absensiResult['status'] === 'terlambat' ? 'badge-warning' : 'badge-success' }} badge-lg">
                                        {{ $absensiResult['status'] === 'terlambat' ? 'Terlambat' : 'Tepat Waktu' }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-base-content/70">Jam Tercatat:</span>
                                    <span class="font-bold font-mono">{{ $absensiResult['jam'] }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-base-content/70">Lokasi:</span>
                                    <span class="font-semibold">{{ $absensiResult['lokasi'] }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-base-content/70">Confidence:</span>
                                    <span class="font-semibold">{{ round($absensiResult['confidence']) }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <a href="{{ route('karyawan.absen.index') }}" wire:navigate class="btn btn-primary btn-lg w-full gap-2">
                    <x-heroicon-o-arrow-left class="w-5 h-5" />
                    Kembali ke Halaman Absen
                </a>
            </div>
        </div>
    @endif

    
    {{-- CSS for Alpine.js x-cloak --}}
    <style>
        [x-cloak] { 
            display: none !important; 
        }
    </style>
    
    <script>
        // Wait for face-api to be loaded before Alpine initializes
        document.addEventListener('DOMContentLoaded', function() {
            let attempts = 0;
            const maxAttempts = 100; // 10 seconds
            
            // Check if face-api is loaded
            const checkFaceAPI = setInterval(() => {
                attempts++;
                
                if (typeof faceapi !== 'undefined') {
                    clearInterval(checkFaceAPI);
                    console.log('✓ face-api library loaded successfully');
                } else if (attempts >= maxAttempts) {
                    clearInterval(checkFaceAPI);
                    console.error('✗ face-api library failed to load after ' + (maxAttempts / 10) + ' seconds');
                }
            }, 100);
        });
    </script>

</div>    
