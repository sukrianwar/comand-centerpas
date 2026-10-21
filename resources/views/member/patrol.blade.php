<x-member-layout>

{{-- ══ Header ══════════════════════════════════════════════════════════════ --}}
<div class="mb-lg border-b border-outline-variant pb-md">
    <h2 class="text-headline-md font-bold text-on-surface mb-1">Patroli</h2>
    <p class="text-body-lg text-secondary">Pilih jadwal aktif, lalu pilih checkpoint untuk mencatat kehadiran patroli Anda.</p>
</div>

@if($activeSchedules->isNotEmpty())
{{-- ══════════════════════════════════════════════════════════════════════════
     ADA JADWAL AKTIF — Petugas memilih jadwal, lalu alur scan berjalan
     ══════════════════════════════════════════════════════════════════════════ --}}

{{-- Alpine.js state management untuk seluruh alur patroli --}}
<div x-data="patrolScan()">

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

    {{-- ── Jadwal Terpilih — Info + Checkpoint ──────────────────────────── --}}
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
                        <button type="button"
                                @click="selectCheckpoint(cp)"
                                :disabled="scannedIds.includes(cp.id)"
                                class="w-full text-left flex items-center gap-md p-sm rounded-xl border transition-all"
                                :class="scannedIds.includes(cp.id)
                                    ? 'bg-emerald-50 border-emerald-200 cursor-default opacity-90'
                                    : 'bg-surface-container-lowest border-outline-variant hover:border-primary hover:bg-primary/5 active:scale-[0.99] cursor-pointer'">
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
                                <p class="text-label-md truncate"
                                   :class="scannedIds.includes(cp.id) ? 'text-emerald-700' : 'text-secondary'"
                                   x-text="scannedIds.includes(cp.id) ? 'Sudah dicatat' : 'Ketuk untuk mencatat kehadiran'"></p>
                            </div>
                            {{-- Status / Aksi --}}
                            <template x-if="scannedIds.includes(cp.id)">
                                <span class="text-label-sm font-bold text-emerald-700 bg-emerald-100 px-2 py-1 rounded-full flex-shrink-0">Selesai</span>
                            </template>
                            <template x-if="!scannedIds.includes(cp.id)">
                                <span class="material-symbols-outlined text-primary flex-shrink-0">chevron_right</span>
                            </template>
                        </button>
                    </template>
                </div>
            </div>

            {{-- ══ AREA CHECKPOINT — Alur pilih → foto ═════════════════════ --}}
            <div class="bg-surface border border-outline-variant rounded-3xl p-lg shadow-sm">

                {{-- ── STEP 0: Petunjuk pilih checkpoint (state awal) ──── --}}
                <template x-if="step === 'idle'">
                    <div class="text-center py-lg">
                        <div class="w-24 h-24 mx-auto mb-lg bg-primary-container rounded-3xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary text-[48px]">checklist</span>
                        </div>
                        <template x-if="isComplete">
                            <div>
                                <h3 class="text-headline-md font-bold text-emerald-800 mb-2">Semua checkpoint sudah dicatat!</h3>
                                <div class="inline-flex items-center gap-sm px-lg py-md bg-emerald-50 text-emerald-800 rounded-2xl border border-emerald-200 font-bold text-title-md">
                                    <span class="material-symbols-outlined text-[28px]">celebration</span>
                                    Patroli rute ini selesai
                                </div>
                            </div>
                        </template>
                        <template x-if="!isComplete">
                            <div>
                                <h3 class="text-headline-md font-bold text-on-surface mb-2">Pilih Checkpoint</h3>
                                <p class="text-body-md text-secondary max-w-md mx-auto">Ketuk salah satu checkpoint yang belum dicatat pada daftar di atas untuk mulai mencatat kehadiran.</p>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- ── STEP 1: Ambil Foto ──────────────────────────────── --}}
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
                                    <span x-text="isSubmitting ? 'Mengirim...' : 'Simpan Kehadiran'"></span>
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

                {{-- ── STEP 2: Sukses ──────────────────────────────────── --}}
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
                        </div>
                        <button @click="resetToIdle()"
                                class="bg-primary text-on-primary py-md px-xl rounded-2xl font-bold text-body-lg transition-all hover:shadow-lg active:scale-[0.98]">
                            <span class="material-symbols-outlined text-[20px] align-middle mr-1">checklist</span>
                            Pilih Checkpoint Berikutnya
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

{{-- ══ JavaScript — Pilih Jadwal & Pencatatan Checkpoint ══════════════════ --}}
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
        step: 'idle',           // idle → photo → success
        errorMessage: '',
        isSubmitting: false,
        isSessionExpired: false,

        // ── Data pencatatan saat ini ───────────────────────────────────────
        scannedIds: [],
        matchedCheckpointId: null,
        matchedCheckpointName: '',
        photoFile: null,
        photoPreview: null,

        // ── Hasil ──────────────────────────────────────────────────────────
        resultMessage: '',
        resultStatus: '',

        // ── Computed ───────────────────────────────────────────────────────
        get totalCheckpoints() { return this.currentSchedule ? this.currentSchedule.checkpoints.length : 0; },
        get scannedCount() { return this.scannedIds.length; },
        get progressPercent() { return this.totalCheckpoints > 0 ? Math.round((this.scannedIds.length / this.totalCheckpoints) * 100) : 0; },
        get isComplete() { return this.totalCheckpoints > 0 && this.scannedIds.length >= this.totalCheckpoints; },

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
            this.currentSchedule = null;
            this.scannedIds = [];
            this.step = 'idle';
            this.errorMessage = '';
        },

        // ═══ STEP 1: Petugas memilih checkpoint ═══════════════════════════
        selectCheckpoint(cp) {
            // Abaikan jika checkpoint sudah dicatat
            if (this.scannedIds.includes(cp.id)) {
                return;
            }

            this.errorMessage = '';
            this.matchedCheckpointId = cp.id;
            this.matchedCheckpointName = cp.name;

            // Langsung ke langkah foto
            this.step = 'photo';
            this.photoFile = null;
            this.photoPreview = null;
        },

        // ═══ STEP 2: Foto dipilih ════════════════════════════════════════
        onPhotoSelected(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.photoFile = file;

            // Buat preview
            const reader = new FileReader();
            reader.onload = (e) => { this.photoPreview = e.target.result; };
            reader.readAsDataURL(file);
        },

        // ═══ STEP 3: Submit ke server ════════════════════════════════════
        async submitScan() {
            if (this.isSubmitting) return;
            this.isSubmitting = true;
            this.errorMessage = '';
            this.isSessionExpired = false;

            const formData = new FormData();
            formData.append('patrol_schedule_id', this.currentSchedule.id);
            formData.append('checkpoint_id', this.matchedCheckpointId);
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
                    // Jika ditolak karena duplikat, biarkan user tetap di step foto.
                    return;
                }

                // ── Sukses ──
                this.resultMessage = data.message;
                this.resultStatus = data.status;

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
            this.photoFile = null;
            this.photoPreview = null;
            this.resultMessage = '';
        },
    }));
});
</script>
@endif

</x-member-layout>
