<x-member-layout>

{{-- ══ Header ══════════════════════════════════════════════════════════════ --}}
<div class="mb-lg border-b border-outline-variant pb-md">
    <h2 class="text-headline-md font-bold text-on-surface mb-1">Patroli</h2>
    <p class="text-body-lg text-secondary">Pilih jadwal aktif, lalu scan QR checkpoint untuk mencatat kehadiran patroli Anda.</p>
</div>

@if($activeSchedules->isNotEmpty())
{{-- ══════════════════════════════════════════════════════════════════════════
     ADA JADWAL AKTIF — Petugas memilih jadwal, lalu alur scan berjalan
     ══════════════════════════════════════════════════════════════════════════ --}}

{{-- Alpine.js state management untuk seluruh alur scan --}}
<div x-data="patrolScan()" x-init="init()">

    {{-- ── STEP 0: Pilih Jadwal (daftar semua jadwal aktif) ─────────────── --}}
    <template x-if="currentSchedule === null">
        <div>
            <div class="bg-surface border border-outline-variant rounded-3xl p-lg mb-lg shadow-sm">
                <h3 class="text-title-lg font-bold text-on-surface mb-md flex items-center gap-sm">
                    <span class="material-symbols-outlined text-primary">event_note</span>
                    Jadwal Patroli Aktif
                </h3>
                <p class="text-body-md text-secondary mb-md">Pilih salah satu jadwal yang sedang berjalan untuk mulai melakukan patroli.</p>

                <div class="space-y-sm">
                    <template x-for="s in schedules" :key="s.id">
                        <button @click="selectSchedule(s.id)"
                                class="w-full text-left flex items-center gap-md p-md rounded-2xl border border-outline-variant bg-surface-container-lowest hover:border-primary hover:bg-primary/5 transition-all">
                            <div class="w-11 h-11 rounded-xl bg-primary-container flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-primary">route</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-body-md font-bold text-on-surface truncate" x-text="s.name"></p>
                                <p class="text-label-md text-secondary truncate flex items-center gap-xs">
                                    <span class="material-symbols-outlined text-[14px]">location_on</span>
                                    <span x-text="s.area_name"></span>
                                </p>
                                <p class="text-label-md text-secondary truncate flex items-center gap-xs">
                                    <span class="material-symbols-outlined text-[14px]">schedule</span>
                                    <span x-text="s.start_time + ' – ' + s.end_time"></span>
                                </p>
                            </div>
                            <div class="flex flex-col items-end gap-xs flex-shrink-0">
                                <span class="inline-flex items-center gap-xs px-2 py-1 bg-emerald-50 text-emerald-700 text-label-sm font-bold rounded-full border border-emerald-200 whitespace-nowrap">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span> Aktif
                                </span>
                                <span class="text-label-md text-secondary whitespace-nowrap" x-text="s.scanned_ids.length + ' / ' + s.checkpoints.length + ' discan'"></span>
                            </div>
                        </button>
                    </template>
                </div>
            </div>

            @include('member.partials.jadwal-hari-ini')
        </div>
    </template>

    {{-- ── Jadwal Terpilih — Info + Scanner ─────────────────────────────── --}}
    <template x-if="currentSchedule !== null">
        <div>

            {{-- Info Jadwal Aktif --}}
            <div class="bg-gradient-to-r from-primary to-blue-700 text-on-primary rounded-3xl p-lg mb-lg shadow-xl shadow-primary/20 relative overflow-hidden">
                <div class="absolute -right-8 -top-8 opacity-10">
                    <span class="material-symbols-outlined text-[180px]">route</span>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between gap-sm mb-sm">
                        <div class="flex items-center gap-sm">
                            <span class="inline-flex items-center gap-xs px-3 py-1 bg-white/20 backdrop-blur rounded-full text-label-md font-bold border border-white/20">
                                <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span> Sedang Berjalan
                            </span>
                            <span class="text-label-md font-bold opacity-80" x-text="currentSchedule.start_time + ' – ' + currentSchedule.end_time"></span>
                        </div>
                        <button @click="changeSchedule()"
                                class="inline-flex items-center gap-xs px-3 py-1 bg-white/15 hover:bg-white/25 backdrop-blur rounded-full text-label-md font-bold border border-white/20 transition-colors">
                            <span class="material-symbols-outlined text-[16px]">swap_horiz</span>
                            Ganti Jadwal
                        </button>
                    </div>
                    <h3 class="text-headline-md font-bold mb-1" x-text="currentSchedule.name"></h3>
                    <p class="text-body-md opacity-90 flex items-center gap-xs">
                        <span class="material-symbols-outlined text-[18px]">location_on</span>
                        <span x-text="currentSchedule.area_name"></span>
                    </p>

                    {{-- Progress Bar --}}
                    <div class="mt-md bg-black/15 rounded-2xl p-sm border border-white/10">
                        <div class="flex justify-between text-body-sm mb-xs font-semibold">
                            <span class="flex items-center gap-xs">
                                <span class="material-symbols-outlined text-[16px]">flag</span> Progress
                            </span>
                            <span x-text="scannedCount + ' / ' + totalCheckpoints"></span>
                        </div>
                        <div class="w-full h-3 bg-black/20 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-400 rounded-full transition-all duration-700 ease-out"
                                 :style="'width:' + progressPercent + '%'"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Daftar Checkpoint (Checklist) --}}
            <div class="bg-surface border border-outline-variant rounded-3xl p-lg mb-lg shadow-sm">
                <h3 class="text-title-lg font-bold text-on-surface mb-md flex items-center gap-sm">
                    <span class="material-symbols-outlined text-primary">checklist</span>
                    Checkpoint Rute
                </h3>
                <div class="space-y-sm">
                    <template x-for="(cp, idx) in currentSchedule.checkpoints" :key="cp.id">
                        <div class="flex items-center gap-md p-sm rounded-xl border transition-all"
                             :class="scannedIds.includes(cp.id)
                                 ? 'bg-emerald-50 border-emerald-200'
                                 : 'bg-surface-container-lowest border-outline-variant'">
                            {{-- Nomor Urut / Checkmark --}}
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 text-body-sm font-bold transition-all"
                                 :class="scannedIds.includes(cp.id)
                                     ? 'bg-emerald-500 text-white'
                                     : 'bg-surface-container-high text-secondary'">
                                <template x-if="scannedIds.includes(cp.id)">
                                    <span class="material-symbols-outlined text-[20px]">check</span>
                                </template>
                                <template x-if="!scannedIds.includes(cp.id)">
                                    <span x-text="idx + 1"></span>
                                </template>
                            </div>
                            {{-- Info Checkpoint --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-body-md font-semibold text-on-surface truncate"
                                   :class="scannedIds.includes(cp.id) ? 'line-through opacity-60' : ''">
                                    <span x-text="cp.name"></span>
                                </p>
                                <p class="text-label-md text-secondary truncate" x-text="cp.qr_code"></p>
                            </div>
                            {{-- Status Badge --}}
                            <template x-if="scannedIds.includes(cp.id)">
                                <span class="text-label-sm font-bold text-emerald-700 bg-emerald-100 px-2 py-1 rounded-full flex-shrink-0">Selesai</span>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            {{-- ══ AREA SCAN — Multi-step flow ══════════════════════════════ --}}
            <div class="bg-surface border border-outline-variant rounded-3xl p-lg shadow-sm">

                {{-- ── STEP 0: Tombol Mulai Scan (state awal) ──────────── --}}
                <template x-if="step === 'idle'">
                    <div class="text-center py-lg">
                        <div class="w-24 h-24 mx-auto mb-lg bg-primary-container rounded-3xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary text-[48px]">qr_code_scanner</span>
                        </div>
                        <h3 class="text-headline-md font-bold text-on-surface mb-2">Scan QR Checkpoint</h3>
                        <p class="text-body-md text-secondary mb-xl max-w-md mx-auto">Arahkan kamera ke QR Code checkpoint untuk mencatat kehadiran.</p>

                        <template x-if="isComplete">
                            <div class="inline-flex items-center gap-sm px-lg py-md bg-emerald-50 text-emerald-800 rounded-2xl border border-emerald-200 font-bold text-title-md">
                                <span class="material-symbols-outlined text-[28px]">celebration</span>
                                Semua checkpoint sudah discan!
                            </div>
                        </template>
                        <template x-if="!isComplete">
                            <button @click="startScanner()"
                                    class="w-full max-w-md mx-auto bg-primary text-on-primary py-lg px-xl rounded-2xl font-bold text-title-lg flex items-center justify-center gap-md transition-all hover:bg-surface-tint hover:shadow-lg active:scale-[0.98]">
                                <span class="material-symbols-outlined text-[28px]">photo_camera</span>
                                Buka Kamera Scanner
                            </button>
                        </template>
                    </div>
                </template>

                {{-- ── STEP 1: QR Scanner aktif ────────────────────────── --}}
                <template x-if="step === 'scanning'">
                    <div>
                        <div class="flex items-center justify-between mb-md">
                            <h3 class="text-title-lg font-bold text-on-surface flex items-center gap-sm">
                                <span class="material-symbols-outlined text-primary">qr_code_scanner</span>
                                Arahkan ke QR Code
                            </h3>
                            <button @click="stopScanner()" class="p-xs text-secondary hover:bg-surface-container rounded-full transition-colors">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <div id="qr-reader" class="rounded-2xl overflow-hidden border-2 border-primary/30 mb-md"></div>
                        <p class="text-body-sm text-secondary text-center">Pastikan QR Code terlihat jelas di area scan.</p>
                    </div>
                </template>

                {{-- ── STEP 2: GPS loading ─────────────────────────────── --}}
                <template x-if="step === 'gps'">
                    <div class="text-center py-xl">
                        <div class="w-20 h-20 mx-auto mb-lg bg-blue-50 rounded-3xl flex items-center justify-center animate-pulse">
                            <span class="material-symbols-outlined text-blue-600 text-[40px]">my_location</span>
                        </div>
                        <h3 class="text-title-lg font-bold text-on-surface mb-2">Mengambil Lokasi GPS...</h3>
                        <p class="text-body-md text-secondary mb-md">Pastikan GPS aktif di perangkat Anda.</p>
                        <p class="text-label-md text-secondary">Checkpoint: <span class="font-bold text-on-surface" x-text="matchedCheckpointName"></span></p>
                    </div>
                </template>

                {{-- ── STEP 3: Ambil Foto ──────────────────────────────── --}}
                <template x-if="step === 'photo'">
                    <div class="text-center py-lg">
                        <div class="w-16 h-16 mx-auto mb-md bg-amber-50 rounded-2xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-amber-600 text-[32px]">add_a_photo</span>
                        </div>
                        <h3 class="text-title-lg font-bold text-on-surface mb-1">Ambil Foto Bukti</h3>
                        <p class="text-body-md text-secondary mb-md">Checkpoint: <span class="font-bold text-on-surface" x-text="matchedCheckpointName"></span></p>

                        {{-- Preview foto --}}
                        <template x-if="photoPreview">
                            <div class="mb-md">
                                <img :src="photoPreview" class="w-48 h-48 object-cover rounded-2xl mx-auto border-2 border-outline-variant shadow-md" alt="Preview">
                            </div>
                        </template>

                        <label class="inline-flex items-center gap-sm bg-surface-container border border-outline-variant text-on-surface py-md px-lg rounded-2xl font-semibold cursor-pointer hover:bg-surface-container-high transition-colors mb-md">
                            <span class="material-symbols-outlined text-[22px]">photo_camera</span>
                            <span x-text="photoPreview ? 'Ambil Ulang' : 'Buka Kamera'"></span>
                            <input type="file" accept="image/*" capture="environment" class="hidden" @change="onPhotoSelected($event)">
                        </label>

                        {{-- Tombol Kirim --}}
                        <template x-if="photoFile">
                            <div class="mt-md">
                                <button @click="submitScan()"
                                        :disabled="isSubmitting"
                                        class="w-full max-w-md mx-auto bg-primary text-on-primary py-lg px-xl rounded-2xl font-bold text-title-lg flex items-center justify-center gap-md transition-all hover:shadow-lg active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed">
                                    <template x-if="isSubmitting">
                                        <span class="material-symbols-outlined animate-spin text-[24px]">progress_activity</span>
                                    </template>
                                    <template x-if="!isSubmitting">
                                        <span class="material-symbols-outlined text-[24px]">send</span>
                                    </template>
                                    <span x-text="isSubmitting ? 'Mengirim...' : 'Kirim Scan'"></span>
                                </button>
                                <button @click="resetToIdle()"
                                        :disabled="isSubmitting"
                                        class="w-full max-w-md mx-auto mt-sm text-secondary py-sm px-xl rounded-2xl font-bold text-title-md flex items-center justify-center gap-sm transition-all hover:bg-surface-container active:scale-[0.98] disabled:opacity-50">
                                    Batal & Ulangi
                                </button>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- ── STEP 4: Sukses ──────────────────────────────────── --}}
                <template x-if="step === 'success'">
                    <div class="text-center py-xl">
                        <div class="w-24 h-24 mx-auto mb-lg bg-emerald-100 rounded-full flex items-center justify-center animate-bounce">
                            <span class="material-symbols-outlined text-emerald-600 text-[48px]">check_circle</span>
                        </div>
                        <h3 class="text-headline-md font-bold text-emerald-800 mb-2" x-text="resultMessage"></h3>
                        <div class="flex items-center justify-center gap-md text-body-md text-secondary mb-lg">
                            <span class="inline-flex items-center gap-xs px-3 py-1 rounded-full text-label-md font-bold"
                                  :class="resultStatus === 'on_time' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'">
                                <span class="material-symbols-outlined text-[16px]" x-text="resultStatus === 'on_time' ? 'schedule' : 'warning'"></span>
                                <span x-text="resultStatus === 'on_time' ? 'Tepat Waktu' : 'Terlambat'"></span>
                            </span>
                            <span>Jarak: <span x-text="resultDistance"></span> m</span>
                        </div>
                        <button @click="resetToIdle()"
                                class="bg-primary text-on-primary py-md px-xl rounded-2xl font-bold text-body-lg transition-all hover:shadow-lg active:scale-[0.98]">
                            <span class="material-symbols-outlined text-[20px] align-middle mr-1">qr_code_scanner</span>
                            Scan Checkpoint Berikutnya
                        </button>
                    </div>
                </template>

                {{-- ── Error state (ditampilkan overlay kalau ada) ──────── --}}
                <template x-if="errorMessage">
                    <div class="mt-md p-md bg-red-50 border border-red-200 rounded-2xl flex items-start gap-sm text-red-800 text-body-sm">
                        <span class="material-symbols-outlined text-red-500 text-[20px] flex-shrink-0 mt-0.5">error</span>
                        <div>
                            <p class="font-semibold mb-1" x-text="errorMessage"></p>
                            <template x-if="isSessionExpired">
                                <button @click="window.location.reload()"
                                        class="text-red-600 underline text-label-md font-bold mt-1 inline-flex items-center gap-xs">
                                    <span class="material-symbols-outlined text-[16px]">refresh</span> Refresh Halaman
                                </button>
                            </template>
                            <template x-if="!isSessionExpired">
                                <button @click="errorMessage = ''; if(step !== 'photo') resetToIdle();"
                                        class="text-red-600 underline text-label-md font-bold">Coba Lagi</button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </template>
</div>

@else
{{-- ══════════════════════════════════════════════════════════════════════════
     TIDAK ADA JADWAL AKTIF — Empty State (tetap tampilkan jadwal hari ini)
     ══════════════════════════════════════════════════════════════════════════ --}}
<div class="bg-surface border border-outline-variant rounded-3xl p-xl text-center shadow-sm mb-lg">
    <div class="w-24 h-24 mx-auto mb-lg bg-surface-container-high rounded-3xl flex items-center justify-center opacity-50">
        <span class="material-symbols-outlined text-[48px] text-secondary">event_busy</span>
    </div>
    <h3 class="text-headline-md font-bold text-on-surface mb-2">Tidak ada jadwal patroli aktif</h3>
    <p class="text-body-lg text-secondary max-w-md mx-auto mb-lg">
        @if($todaySchedules->isNotEmpty())
        Belum ada jadwal yang masuk jam aktifnya. Halaman scan akan aktif otomatis saat jadwal mulai.
        @else
        Saat ini belum ada jadwal yang diinput. Silakan hubungi admin untuk membuat jadwal patroli.
        @endif
    </p>
    <a href="{{ route('member.dashboard') }}"
       class="inline-flex items-center gap-sm bg-surface-container border border-outline-variant text-on-surface py-md px-lg rounded-2xl font-semibold hover:bg-surface-container-high transition-colors">
        <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        Kembali ke Beranda
    </a>
</div>

@include('member.partials.jadwal-hari-ini')
@endif

{{-- ══ JavaScript — Pilih Jadwal & Alur Scan Checkpoint ════════════════════ --}}
@if($activeSchedules->isNotEmpty())
@php
    $schedulesData = $activeSchedules->map(function ($schedule) {
        return [
            'id'          => $schedule->id,
            'name'        => $schedule->schedule_name,
            'area_name'   => $schedule->area?->area_name ?? '-',
            'start_time'  => \Carbon\Carbon::parse($schedule->start_time)->format('H:i'),
            'end_time'    => \Carbon\Carbon::parse($schedule->end_time)->format('H:i'),
            'checkpoints' => $schedule->checkpoints->map(fn ($cp) => [
                'id'      => $cp->id,
                'name'    => $cp->checkpoint_name,
                'qr_code' => $cp->qr_code,
            ])->values()->all(),
            'scanned_ids' => $schedule->scannedCheckpointIds,
        ];
    })->values();
@endphp
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('patrolScan', () => ({
        // ── State ──────────────────────────────────────────────────────────
        schedules: @json($schedulesData),
        currentSchedule: null,  // jadwal yang dipilih petugas (objek dari schedules)
        step: 'idle',           // idle → scanning → gps → photo → success
        errorMessage: '',
        isSubmitting: false,
        isSessionExpired: false,

        // ── Data scan saat ini ─────────────────────────────────────────────
        scannedIds: [],
        matchedCheckpointId: null,
        matchedCheckpointName: '',
        gpsLatitude: null,
        gpsLongitude: null,
        photoFile: null,
        photoPreview: null,

        // ── Hasil ──────────────────────────────────────────────────────────
        resultMessage: '',
        resultStatus: '',
        resultDistance: 0,

        // ── QR Scanner instance ────────────────────────────────────────────
        scanner: null,

        // ── Tahap development: lewati langkah GPS ──────────────────────────
        skipGps: @json(config('app.skip_gps_validation', false)),

        // ── Computed ───────────────────────────────────────────────────────
        get totalCheckpoints() { return this.currentSchedule ? this.currentSchedule.checkpoints.length : 0; },
        get scannedCount() { return this.scannedIds.length; },
        get progressPercent() { return this.totalCheckpoints > 0 ? Math.round((this.scannedIds.length / this.totalCheckpoints) * 100) : 0; },
        get isComplete() { return this.totalCheckpoints > 0 && this.scannedIds.length >= this.totalCheckpoints; },

        init() {
            // Cleanup scanner saat navigasi
            window.addEventListener('beforeunload', () => this.stopScanner());
        },

        // ═══ STEP PICK: Petugas memilih jadwal aktif ══════════════════════
        selectSchedule(id) {
            const s = this.schedules.find(x => x.id === id);
            if (!s) return;
            this.currentSchedule = s;
            this.scannedIds = s.scanned_ids;
            this.step = 'idle';
            this.errorMessage = '';
        },

        changeSchedule() {
            this.stopScanner();
            this.currentSchedule = null;
            this.scannedIds = [];
            this.step = 'idle';
            this.errorMessage = '';
        },

        // ═══ STEP 1: Buka QR Scanner ══════════════════════════════════════
        async startScanner() {
            this.step = 'scanning';
            this.errorMessage = '';

            await this.$nextTick();

            const readerEl = document.getElementById('qr-reader');
            if (!readerEl) return;

            try {
                this.scanner = new window.Html5Qrcode('qr-reader');

                await this.scanner.start(
                    { facingMode: 'environment' },
                    {
                        fps: 10,
                        qrbox: { width: 250, height: 250 },
                        aspectRatio: 1.0,
                    },
                    (decodedText) => this.onQrScanned(decodedText),
                    () => {} // ignore errors (frame tanpa QR)
                );
            } catch (err) {
                this.errorMessage = 'Tidak dapat mengakses kamera. Pastikan izin kamera diaktifkan dan coba lagi.';
                this.step = 'idle';
            }
        },

        async stopScanner() {
            if (this.scanner) {
                try {
                    await this.scanner.stop();
                } catch (e) { /* ignore */ }
                this.scanner = null;
            }
            if (this.step === 'scanning') {
                this.step = 'idle';
            }
        },

        // ═══ STEP 2: QR Terbaca — validasi checkpoint ════════════════════
        async onQrScanned(qrCode) {
            // Stop scanner dulu supaya tidak scan berulang
            await this.stopScanner();

            // Cocokkan QR code dengan daftar checkpoint di rute yang dipilih
            const matched = this.currentSchedule.checkpoints.find(cp => cp.qr_code === qrCode);

            if (!matched) {
                this.errorMessage = `Checkpoint dengan kode "${qrCode}" bukan bagian dari rute ini.`;
                return;
            }

            // Cek apakah sudah discan
            if (this.scannedIds.includes(matched.id)) {
                this.errorMessage = `Checkpoint "${matched.name}" sudah discan sebelumnya.`;
                return;
            }

            // Valid — simpan info dan lanjut ke foto
            this.matchedCheckpointId = matched.id;
            this.matchedCheckpointName = matched.name;

            // Tahap development: lewati permintaan GPS, langsung ke foto
            if (this.skipGps) {
                this.gpsLatitude = 0;
                this.gpsLongitude = 0;
                this.step = 'photo';
                this.photoFile = null;
                this.photoPreview = null;
                return;
            }

            this.requestGps();
        },

        // ═══ STEP 3: Minta GPS ═══════════════════════════════════════════
        requestGps() {
            this.step = 'gps';
            this.errorMessage = '';

            if (!navigator.geolocation) {
                this.errorMessage = 'Browser Anda tidak mendukung GPS. Gunakan browser Chrome/Safari terbaru.';
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.gpsLatitude = pos.coords.latitude;
                    this.gpsLongitude = pos.coords.longitude;
                    // Lanjut ke step foto
                    this.step = 'photo';
                    this.photoFile = null;
                    this.photoPreview = null;
                },
                (err) => {
                    let msg = 'Gagal mendapatkan lokasi GPS.';
                    if (err.code === 1) msg = 'Izin lokasi ditolak. Aktifkan GPS dan izinkan akses lokasi di pengaturan browser, lalu coba lagi.';
                    if (err.code === 2) msg = 'Lokasi tidak tersedia. Pastikan GPS perangkat aktif.';
                    if (err.code === 3) msg = 'Waktu permintaan lokasi habis. Pastikan GPS aktif dan coba lagi.';
                    this.errorMessage = msg;
                },
                {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 0,
                }
            );
        },

        // ═══ STEP 4: Foto dipilih ════════════════════════════════════════
        onPhotoSelected(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.photoFile = file;

            // Buat preview
            const reader = new FileReader();
            reader.onload = (e) => { this.photoPreview = e.target.result; };
            reader.readAsDataURL(file);
        },

        // ═══ STEP 5: Submit ke server ════════════════════════════════════
        async submitScan() {
            if (this.isSubmitting) return;
            this.isSubmitting = true;
            this.errorMessage = '';
            this.isSessionExpired = false;

            const formData = new FormData();
            formData.append('patrol_schedule_id', this.currentSchedule.id);
            formData.append('checkpoint_id', this.matchedCheckpointId);
            formData.append('latitude', this.gpsLatitude);
            formData.append('longitude', this.gpsLongitude);
            formData.append('photo', this.photoFile);

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const res = await fetch('{{ route("member.patrol.scan") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                // 1. Cek Error 419 (CSRF/Session Expired)
                if (res.status === 419) {
                    this.errorMessage = 'Sesi Anda telah berakhir. Silakan refresh halaman untuk melanjutkan.';
                    this.isSessionExpired = true;
                    this.isSubmitting = false;
                    return;
                }

                const data = await res.json();

                // 2. Cek Error 422 Validasi Form Request (bukan validasi logic custom)
                if (res.status === 422 && data.errors) {
                    let errorList = [];
                    for (const field in data.errors) {
                        errorList.push(data.errors[field][0]); // Ambil pesan pertama dari tiap field
                    }
                    this.errorMessage = errorList.length > 1
                        ? errorList.join(' • ')
                        : errorList[0];
                    this.isSubmitting = false;
                    // JANGAN resetToIdle(), biarkan user tetap di step saat ini (misal: step 'photo') untuk ambil ulang foto.
                    return;
                }

                // 3. Cek Error/Ditolak oleh Custom Logic di Controller
                if (!res.ok || !data.success) {
                    this.errorMessage = data.message || 'Terjadi kesalahan saat mengirim data.';
                    this.isSubmitting = false;
                    // Jika ditolak karena aturan radius/duplikat, biarkan user tetap di step foto.
                    return;
                }

                // ── Sukses ──
                this.resultMessage = data.message;
                this.resultStatus = data.status;
                this.resultDistance = data.distance;

                // Update UI progress tanpa reload
                this.scannedIds = data.scanned_ids;
                this.currentSchedule.scanned_ids = data.scanned_ids;
                this.step = 'success';

            } catch (err) {
                this.errorMessage = 'Gagal menghubungi server. Periksa koneksi internet Anda.';
            } finally {
                this.isSubmitting = false;
            }
        },

        // ═══ Reset ke idle ═══════════════════════════════════════════════
        resetToIdle() {
            this.step = 'idle';
            this.errorMessage = '';
            this.matchedCheckpointId = null;
            this.matchedCheckpointName = '';
            this.gpsLatitude = null;
            this.gpsLongitude = null;
            this.photoFile = null;
            this.photoPreview = null;
            this.resultMessage = '';
        },
    }));
});
</script>
@endif

</x-member-layout>
