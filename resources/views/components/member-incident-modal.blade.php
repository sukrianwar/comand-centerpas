@props([
    'areas' => [],
    'suggestedAreaId' => null,
])

@php
    $categoryIcons = [
        'kebakaran' => 'local_fire_department',
        'kebocoran' => 'plumbing',
        'orang_mencurigakan' => 'person_alert',
        'kerusakan' => 'build',
        'kecelakaan' => 'emergency',
        'lainnya' => 'help',
    ];
@endphp

<div x-data="memberIncidentForm(@js($areas), @js($suggestedAreaId))"
     x-on:open-incident-modal.window="openModal()"
     x-on:keydown.escape.window="closeModal()"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-[70] flex items-center justify-center p-md"
     role="dialog"
     aria-modal="true"
     aria-labelledby="incident-modal-title">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="closeModal()"></div>

    <div class="relative flex max-h-[90vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl bg-surface shadow-2xl">
        {{-- Header --}}
        <div class="flex items-start justify-between gap-sm border-b border-outline-variant bg-surface-container-low p-md">
            <div>
                <p class="text-label-md font-bold uppercase tracking-widest text-secondary">Member Area</p>
                <h3 id="incident-modal-title" class="text-headline-md font-bold text-on-surface">Laporkan Insiden / Temuan</h3>
            </div>
            <button type="button" @click="closeModal()"
                    class="p-xs text-on-surface-variant transition-colors hover:bg-surface-container rounded-full"
                    aria-label="Tutup modal">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        {{-- Success state --}}
        <div x-show="success" class="flex flex-1 flex-col items-center justify-center p-xl text-center">
            <div class="mb-lg flex h-20 w-20 items-center justify-center rounded-full bg-emerald-100">
                <span class="material-symbols-outlined text-[42px] text-emerald-600">check_circle</span>
            </div>
            <h4 class="mb-2 text-headline-md font-bold text-emerald-800">Laporan berhasil dikirim</h4>
            <p class="mb-sm text-body-md text-secondary">
                Nomor laporan
                <span class="font-bold text-on-surface" x-text="success ? success.code : ''"></span>
            </p>
            <p class="mb-lg max-w-sm text-body-sm text-secondary">
                Laporan Anda sudah masuk ke Command Center dan akan ditinjau oleh admin.
            </p>
            <div class="flex flex-wrap justify-center gap-sm">
                <button type="button" @click="resetForAnother()"
                        class="inline-flex items-center gap-xs rounded-xl bg-primary px-md py-sm text-body-sm font-bold text-on-primary transition-colors hover:bg-surface-tint">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Lapor Lagi
                </button>
                <button type="button" @click="closeModal()"
                        class="rounded-xl border border-outline-variant px-md py-sm text-body-sm font-bold text-on-surface transition-colors hover:bg-surface-container">
                    Tutup
                </button>
            </div>
        </div>

        {{-- Form --}}
        <form x-ref="form" x-show="!success" @submit.prevent="submit()" class="flex min-h-0 flex-1 flex-col">
            <div x-ref="formBody" class="flex-1 space-y-md overflow-y-auto p-md">
                <div x-show="generalError"
                     class="flex items-start gap-sm rounded-xl border border-red-200 bg-red-50 px-sm py-xs text-body-sm text-red-800">
                    <span class="material-symbols-outlined flex-shrink-0 text-[18px] text-red-500">error</span>
                    <span x-text="generalError"></span>
                </div>

                {{-- Kategori --}}
                <div>
                    <label class="mb-xs block text-body-sm font-bold text-on-surface">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-xs sm:grid-cols-3">
                        @foreach(\App\Models\Incident::CATEGORIES as $value => $label)
                        <label class="cursor-pointer">
                            <input type="radio" name="category" value="{{ $value }}" x-model="form.category" class="peer sr-only" required>
                            <div class="flex flex-col items-center gap-xs rounded-xl border border-outline-variant bg-surface-container-lowest px-xs py-sm text-center transition-all hover:border-primary/50 peer-checked:border-primary peer-checked:bg-primary/10 peer-checked:text-primary">
                                <span class="material-symbols-outlined text-[22px]">{{ $categoryIcons[$value] ?? 'report_problem' }}</span>
                                <span class="text-[11px] font-semibold leading-tight">{{ $label }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    <p x-show="errors.category" x-text="errors.category?.[0]" class="mt-1 text-[11px] text-red-600"></p>
                </div>

                {{-- Judul --}}
                <div>
                    <label for="incident-title" class="mb-xs block text-body-sm font-bold text-on-surface">
                        Judul Laporan <span class="text-red-500">*</span>
                    </label>
                    <input id="incident-title" x-ref="titleInput" name="title" type="text" maxlength="200" required
                           x-model="form.title"
                           placeholder="Contoh: Kebocoran pipa di area parkir"
                           class="w-full rounded-xl border border-outline-variant bg-surface-container-lowest px-sm py-xs text-body-sm text-on-surface outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/10">
                    <p x-show="errors.title" x-text="errors.title?.[0]" class="mt-1 text-[11px] text-red-600"></p>
                </div>

                {{-- Area + Prioritas --}}
                <div class="grid grid-cols-1 gap-sm sm:grid-cols-2">
                    <div>
                        <label for="incident-area" class="mb-xs block text-body-sm font-bold text-on-surface">
                            Area <span class="text-red-500">*</span>
                        </label>
                        <select id="incident-area" name="area_id" x-model="form.area_id" required
                                :disabled="areas.length === 0"
                                class="w-full rounded-xl border border-outline-variant bg-surface-container-lowest px-sm py-xs text-body-sm text-on-surface outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/10 disabled:opacity-50">
                            <option value="">Pilih area...</option>
                            @foreach($areas as $area)
                                <option value="{{ $area['id'] }}">{{ $area['name'] }}</option>
                            @endforeach
                        </select>
                        <p x-show="suggestedAreaId && form.area_id === suggestedAreaId"
                           class="mt-1 text-[11px] text-secondary">
                            Area disarankan dari jadwal patroli aktif.
                        </p>
                        <p x-show="areas.length === 0" class="mt-1 text-[11px] text-red-600">
                            Belum ada area. Hubungi admin terlebih dahulu.
                        </p>
                        <p x-show="errors.area_id" x-text="errors.area_id?.[0]" class="mt-1 text-[11px] text-red-600"></p>
                    </div>

                    <div>
                        <span class="mb-xs block text-body-sm font-bold text-on-surface">
                            Prioritas <span class="text-red-500">*</span>
                        </span>
                        <div class="grid grid-cols-4 gap-xs">
                            @foreach(\App\Models\Incident::PRIORITIES as $value => $label)
                            <label class="cursor-pointer">
                                <input type="radio" name="priority" value="{{ $value }}" x-model="form.priority" class="peer sr-only">
                                <span class="block rounded-lg border border-outline-variant bg-surface-container-lowest px-1 py-xs text-center text-[10px] font-bold uppercase tracking-wide text-secondary transition-all peer-checked:border-primary peer-checked:bg-primary peer-checked:text-on-primary">
                                    {{ $label }}
                                </span>
                            </label>
                            @endforeach
                        </div>
                        <p x-show="errors.priority" x-text="errors.priority?.[0]" class="mt-1 text-[11px] text-red-600"></p>
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label for="incident-description" class="mb-xs block text-body-sm font-bold text-on-surface">
                        Deskripsi <span class="text-red-500">*</span>
                    </label>
                    <textarea id="incident-description" name="description" rows="4" maxlength="2000" required
                              x-model="form.description"
                              placeholder="Jelaskan kondisi, lokasi detail, dan hal penting lainnya..."
                              class="w-full resize-none rounded-xl border border-outline-variant bg-surface-container-lowest px-sm py-xs text-body-sm text-on-surface outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/10"></textarea>
                    <p x-show="errors.description" x-text="errors.description?.[0]" class="mt-1 text-[11px] text-red-600"></p>
                </div>

                {{-- Foto --}}
                <div>
                    <span class="mb-xs block text-body-sm font-bold text-on-surface">
                        Foto Bukti <span class="text-red-500">*</span>
                    </span>

                    <template x-if="photoPreview">
                        <div class="relative mb-sm inline-block">
                            <img :src="photoPreview" alt="Preview foto bukti"
                                 class="h-36 w-36 rounded-xl border border-outline-variant object-cover">
                            <button type="button" @click="clearPhoto()"
                                    class="absolute -right-2 -top-2 flex h-7 w-7 items-center justify-center rounded-full bg-red-600 text-white shadow-md"
                                    aria-label="Hapus foto">
                                <span class="material-symbols-outlined text-[16px]">close</span>
                            </button>
                        </div>
                    </template>

                    <label class="inline-flex cursor-pointer items-center gap-sm rounded-xl border border-outline-variant bg-surface-container-lowest px-sm py-xs text-body-sm font-semibold text-on-surface transition-colors hover:bg-surface-container">
                        <span class="material-symbols-outlined text-[20px] text-primary">photo_camera</span>
                        <span x-text="photoName ? 'Ganti Foto' : 'Ambil / Pilih Foto'"></span>
                        <input x-ref="photoInput" type="file" name="photo"
                               accept="image/jpeg,image/png,image/webp"
                               class="hidden" @change="onPhotoSelected($event)">
                    </label>
                    <p class="mt-1 text-[11px] text-secondary">Wajib. Format JPEG/PNG/WebP, maksimal 5 MB.</p>
                    <p x-show="errors.photo" x-text="errors.photo?.[0]" class="mt-1 text-[11px] text-red-600"></p>
                </div>

            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-sm border-t border-outline-variant bg-surface-container-low p-md">
                <button type="button" @click="closeModal()" :disabled="isSubmitting"
                        class="rounded-xl px-md py-sm text-body-sm font-bold text-secondary transition-colors hover:bg-surface-container disabled:opacity-50">
                    Batal
                </button>
                <button type="submit" :disabled="isSubmitting || areas.length === 0"
                        class="inline-flex items-center gap-xs rounded-xl bg-primary px-md py-sm text-body-sm font-bold text-on-primary shadow-sm transition-all hover:bg-surface-tint active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-50">
                    <span class="material-symbols-outlined text-[18px]" :class="isSubmitting ? 'animate-spin' : ''"
                          x-text="isSubmitting ? 'progress_activity' : 'send'"></span>
                    <span x-text="isSubmitting ? 'Mengirim...' : 'Kirim Laporan'"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
window.memberIncidentForm = function (areas, suggestedAreaId) {
    const emptyForm = () => ({
        title: '',
        category: '',
        priority: 'medium',
        area_id: suggestedAreaId ? String(suggestedAreaId) : '',
        description: '',
    });

    return {
        open: false,
        isSubmitting: false,
        areas: areas || [],
        suggestedAreaId: suggestedAreaId ? String(suggestedAreaId) : null,
        form: emptyForm(),
        errors: {},
        generalError: '',
        success: null,
        photoPreview: null,
        photoName: '',
        lastFocused: null,
        mainOverflow: '',

        openModal() {
            if (this.open) {
                return;
            }

            this.lastFocused = document.activeElement;
            this.open = true;
            this.lockScroll();
            this.$nextTick(() => this.$refs.titleInput?.focus());
        },

        closeModal() {
            if (!this.open || this.isSubmitting) {
                return;
            }

            this.open = false;
            this.unlockScroll();
            this.resetForm();

            this.$nextTick(() => this.lastFocused?.focus?.());
        },

        resetForm() {
            this.form = emptyForm();
            this.errors = {};
            this.generalError = '';
            this.success = null;
            this.clearPhoto();
        },

        resetForAnother() {
            this.resetForm();
            this.$nextTick(() => this.$refs.titleInput?.focus());
        },

        clearPhoto() {
            if (this.photoPreview) {
                URL.revokeObjectURL(this.photoPreview);
            }

            this.photoPreview = null;
            this.photoName = '';

            if (this.$refs.photoInput) {
                this.$refs.photoInput.value = '';
            }
        },

        onPhotoSelected(event) {
            const file = event.target.files?.[0];

            if (!file) {
                this.clearPhoto();
                return;
            }

            if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
                this.clearPhoto();
                this.errors = { ...this.errors, photo: ['Format foto harus JPEG, PNG, atau WebP.'] };
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                this.clearPhoto();
                this.errors = { ...this.errors, photo: ['Ukuran foto maksimal 5 MB.'] };
                return;
            }

            if (this.photoPreview) {
                URL.revokeObjectURL(this.photoPreview);
            }

            this.photoPreview = URL.createObjectURL(file);
            this.photoName = file.name;
            this.errors = Object.fromEntries(
                Object.entries(this.errors).filter(([key]) => key !== 'photo')
            );
        },

        async submit() {
            if (this.isSubmitting) {
                return;
            }

            this.generalError = '';
            this.errors = {};

            if (!this.$refs.photoInput?.files?.length) {
                this.errors = { ...this.errors, photo: ['Foto bukti wajib diunggah.'] };
                this.generalError = 'Periksa kembali isian laporan.';
                this.scrollToTop();
                return;
            }

            this.isSubmitting = true;

            try {
                const response = await fetch('{{ route('member.incidents.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: new FormData(this.$refs.form),
                });

                if (response.status === 419) {
                    this.generalError = 'Sesi Anda berakhir. Muat ulang halaman lalu coba lagi.';
                    this.scrollToTop();
                    return;
                }

                const data = await response.json().catch(() => ({}));

                if (response.status === 422) {
                    this.errors = data.errors || {};
                    this.generalError = 'Periksa kembali isian laporan.';
                    this.scrollToTop();
                    return;
                }

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Laporan gagal dikirim. Silakan coba lagi.');
                }

                this.success = data.incident;
                this.clearPhoto();
            } catch (error) {
                this.generalError = error.message || 'Laporan gagal dikirim. Silakan coba lagi.';
                this.scrollToTop();
            } finally {
                this.isSubmitting = false;
            }
        },

        scrollToTop() {
            this.$nextTick(() => this.$refs.formBody?.scrollTo({ top: 0, behavior: 'smooth' }));
        },

        lockScroll() {
            const main = document.querySelector('main');

            if (main) {
                this.mainOverflow = main.style.overflow;
                main.style.overflow = 'hidden';
            }
        },

        unlockScroll() {
            const main = document.querySelector('main');

            if (main) {
                main.style.overflow = this.mainOverflow || '';
            }
        },
    };
};
</script>
