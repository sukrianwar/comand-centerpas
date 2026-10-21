<x-app-layout>

{{-- ══ Header ════════════════════════════════════════════════════════════ --}}
<div class="flex flex-col md:flex-row md:items-end justify-between mb-lg gap-md">
    <div>
        <p class="text-body-md text-secondary mb-xs">
            <a href="{{ route('patrol-schedules.index') }}" class="hover:text-primary transition-colors">Rute &amp; Jadwal Patroli</a>
            <span class="mx-1 opacity-40">/</span>
            <span>Edit Jadwal</span>
        </p>
        <h2 class="text-headline-lg font-headline-lg text-on-surface">Edit Jadwal Patroli</h2>
        <p class="text-body-sm text-secondary mt-xs">Mengedit: <strong class="text-on-surface">{{ $patrolSchedule->schedule_name }}</strong></p>
    </div>
</div>

{{-- ══ Form ════════════════════════════════════════════════════════════════ --}}
<form method="POST" action="{{ route('patrol-schedules.update', $patrolSchedule) }}"
      x-data="patrolFormEdit()"
      @submit.prevent="handleSubmit">

    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-lg">

        {{-- ─── Kolom Kiri: Informasi Jadwal ─────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-md">

            {{-- Card: Informasi Dasar --}}
            <div class="soft-card p-lg">
                <h3 class="text-headline-md font-headline-md text-on-surface mb-lg flex items-center gap-sm">
                    <span class="material-symbols-outlined text-[22px] text-primary">event_note</span>
                    Informasi Dasar
                </h3>

                <div class="space-y-md">

                    {{-- Nama Jadwal (otomatis) --}}
                    <div>
                        <span class="block text-body-sm font-semibold text-on-surface mb-xs">
                            Nama Jadwal / Rute <span class="text-secondary font-normal">(otomatis)</span>
                        </span>
                        <div class="flex items-start gap-sm bg-primary/5 border border-primary/20 rounded-xl py-xs px-sm text-body-sm">
                            <span class="material-symbols-outlined text-[18px] text-primary mt-0.5">auto_awesome</span>
                            <div>
                                <p class="font-semibold text-on-surface" x-text="generatedName || 'Lengkapi area, shift, dan putaran'"></p>
                                <p class="text-label-md text-secondary mt-0.5">Dibuat otomatis dari area, shift, dan putaran.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Area --}}
                    <div>
                        <label for="area_id" class="block text-body-sm font-semibold text-on-surface mb-xs">
                            Area <span class="text-red-500">*</span>
                        </label>
                        <select id="area_id" name="area_id"
                                x-model="selectedAreaId"
                                class="w-full bg-surface-container-low border rounded-xl py-xs px-sm text-body-sm text-on-surface outline-none transition-all
                                       @error('area_id') border-red-400 focus:border-red-500
                                       @else border-outline-variant focus:border-primary @enderror">
                            <option value="">-- Pilih Area --</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}"
                                        @selected(old('area_id', $patrolSchedule->area_id) == $area->id)>
                                    {{ $area->area_name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('area_id')" class="mt-xs" />
                    </div>

                    {{-- Shift (dari master shift) --}}
                    <div>
                        <label for="shift_id" class="block text-body-sm font-semibold text-on-surface mb-xs">
                            Shift <span class="text-red-500">*</span>
                        </label>
                        <select id="shift_id" name="shift_id"
                                x-model="selectedShiftId"
                                @change="applyShiftTime(true)"
                                class="w-full bg-surface-container-low border rounded-xl py-xs px-sm text-body-sm text-on-surface outline-none transition-all
                                       @error('shift_id') border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-200
                                       @else border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/10 @enderror">
                            <option value="" disabled>-- Pilih Shift --</option>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}">
                                    {{ $shift->shift_name }} ({{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}){{ $shift->status === 'inactive' ? ' — Nonaktif' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-label-md text-secondary mt-xs">Mengubah shift akan mengisi jam mulai &amp; selesai otomatis (masih bisa diubah).</p>
                        <x-input-error :messages="$errors->get('shift_id')" class="mt-xs" />
                    </div>

                    {{-- Urutan Putaran --}}
                    <div>
                        <label class="block text-body-sm font-semibold text-on-surface mb-xs">
                            Urutan Putaran dalam Shift <span class="text-red-500">*</span>
                        </label>
                        <p class="text-label-md text-secondary mb-sm">Dalam 1 shift terdapat 3 kali putaran patroli.</p>
                        <div class="flex gap-sm">
                            @foreach([1, 2, 3] as $val)
                            <label class="cursor-pointer">
                                <input type="radio" name="round_number" value="{{ $val }}" class="sr-only peer"
                                       x-model="selectedRound"
                                       @if(old('round_number', $patrolSchedule->round_number) == $val) checked @endif>
                                <div class="flex flex-col items-center gap-xs px-md py-sm rounded-xl border-2 transition-all select-none min-w-[80px]
                                            peer-checked:bg-primary-container peer-checked:border-primary peer-checked:text-on-primary
                                            border-outline-variant text-secondary hover:border-outline hover:bg-surface-container">
                                    <span class="text-headline-md font-bold">{{ $val }}</span>
                                    <span class="text-label-md whitespace-nowrap">Putaran ke-{{ $val }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        <x-input-error :messages="$errors->get('round_number')" class="mt-xs" />
                    </div>

                </div>
            </div>

            {{-- Card: Waktu Patroli --}}
            <div class="soft-card p-lg">
                <h3 class="text-headline-md font-headline-md text-on-surface mb-lg flex items-center gap-sm">
                    <span class="material-symbols-outlined text-[22px] text-primary">schedule</span>
                    Waktu Patroli
                </h3>

                <div class="space-y-md">
                    <div class="grid grid-cols-2 gap-md">
                        <div>
                            <label for="start_time" class="block text-body-sm font-semibold text-on-surface mb-xs">
                                Jam Mulai <span class="text-red-500">*</span>
                            </label>
                            <input type="time" id="start_time" name="start_time"
                                   value="{{ old('start_time', $patrolSchedule->start_time) }}"
                                   class="w-full bg-surface-container-low border rounded-xl py-xs px-sm text-body-sm text-on-surface outline-none transition-all
                                          @error('start_time') border-red-400 focus:border-red-500
                                          @else border-outline-variant focus:border-primary @enderror">
                            <x-input-error :messages="$errors->get('start_time')" class="mt-xs" />
                        </div>
                        <div>
                            <label for="end_time" class="block text-body-sm font-semibold text-on-surface mb-xs">
                                Jam Selesai <span class="text-red-500">*</span>
                            </label>
                            <input type="time" id="end_time" name="end_time"
                                   value="{{ old('end_time', $patrolSchedule->end_time) }}"
                                   class="w-full bg-surface-container-low border rounded-xl py-xs px-sm text-body-sm text-on-surface outline-none transition-all
                                          @error('end_time') border-red-400 focus:border-red-500
                                          @else border-outline-variant focus:border-primary @enderror">
                            <x-input-error :messages="$errors->get('end_time')" class="mt-xs" />
                        </div>
                    </div>

                    {{-- Status (editable di form edit) --}}
                    <div>
                        <label for="status" class="block text-body-sm font-semibold text-on-surface mb-xs">
                            Status Jadwal <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status"
                                class="w-full bg-surface-container-low border rounded-xl py-xs px-sm text-body-sm text-on-surface outline-none transition-all
                                       @error('status') border-red-400 focus:border-red-500
                                       @else border-outline-variant focus:border-primary @enderror">
                            @foreach(['scheduled' => 'Scheduled (Terjadwal)', 'ongoing' => 'Ongoing (Berlangsung)', 'completed' => 'Completed (Selesai)', 'missed' => 'Missed (Terlewat)'] as $val => $label)
                                <option value="{{ $val }}" @selected(old('status', $patrolSchedule->status) === $val)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-xs" />
                    </div>
                </div>
            </div>

        </div>

        {{-- ─── Kolom Kanan: Pemilihan Checkpoint ────────────────────────── --}}
        <div class="lg:col-span-1">
            <div class="soft-card overflow-hidden sticky top-[80px]">

                <div class="p-md border-b border-outline-variant bg-surface-bright flex items-center justify-between">
                    <div>
                        <h3 class="text-headline-md font-headline-md text-on-surface">Pilih Checkpoint</h3>
                        <p class="text-label-md text-secondary mt-xs">
                            <span x-text="selectedCheckpoints.length">0</span> dipilih &bull; Drag untuk atur urutan
                        </p>
                    </div>
                    <span class="material-symbols-outlined text-[22px] text-primary">pin_drop</span>
                </div>

                @error('checkpoints')
                <div class="mx-md mt-md p-sm bg-red-50 border border-red-200 rounded-xl text-red-700 text-body-sm flex items-center gap-xs">
                    <span class="material-symbols-outlined text-[16px]">error</span>
                    {{ $message }}
                </div>
                @enderror

                {{-- Panel Urutan Rute --}}
                <div class="p-md border-b border-outline-variant" x-show="selectedCheckpoints.length > 0">
                    <p class="text-label-md font-semibold text-on-surface mb-sm uppercase tracking-wide">Urutan Rute</p>
                    <div class="space-y-xs">
                        <template x-for="(cp, index) in selectedCheckpoints" :key="cp.id">
                            <div class="flex items-center gap-sm bg-surface-container-low border border-outline-variant rounded-xl px-sm py-xs group">
                                <input type="hidden" :name="`checkpoints[${index}]`" :value="cp.id">
                                <span class="w-5 h-5 rounded-full bg-primary text-on-primary text-[10px] font-bold flex items-center justify-center flex-shrink-0"
                                      x-text="index + 1"></span>
                                <span class="text-body-sm font-medium text-on-surface flex-1 truncate" x-text="cp.name"></span>
                                <div class="flex flex-col gap-[2px] opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button type="button" @click="moveUp(index)"
                                            :disabled="index === 0"
                                            class="p-[2px] rounded text-secondary hover:text-primary disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                                        <span class="material-symbols-outlined text-[14px]">arrow_drop_up</span>
                                    </button>
                                    <button type="button" @click="moveDown(index)"
                                            :disabled="index === selectedCheckpoints.length - 1"
                                            class="p-[2px] rounded text-secondary hover:text-primary disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                                        <span class="material-symbols-outlined text-[14px]">arrow_drop_down</span>
                                    </button>
                                </div>
                                <button type="button" @click="removeCheckpoint(cp.id)"
                                        class="p-xs text-red-400 hover:text-red-600 hover:bg-red-50 rounded-full transition-colors flex-shrink-0">
                                    <span class="material-symbols-outlined text-[14px]">close</span>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Daftar checkpoint tersedia --}}
                <div class="p-md">
                    <p class="text-label-md font-semibold text-on-surface mb-sm uppercase tracking-wide">Semua Checkpoint Aktif</p>

                    <div class="relative mb-sm">
                        <span class="material-symbols-outlined absolute left-sm top-1/2 -translate-y-1/2 text-secondary text-[16px]">search</span>
                        <input type="text" x-model="checkpointSearch"
                               placeholder="Cari checkpoint..."
                               class="w-full bg-surface-container-low border border-outline-variant rounded-xl py-xs pl-[30px] pr-sm text-body-sm text-on-surface outline-none focus:border-primary transition-all">
                    </div>

                    <div class="space-y-xs max-h-[340px] overflow-y-auto pr-xs">
                        @forelse($checkpoints as $checkpoint)
                        <div data-checkpoint-id="{{ $checkpoint->id }}"
                             data-checkpoint-name="{{ $checkpoint->checkpoint_name }}"
                             x-show="checkpointFilter('{{ addslashes($checkpoint->checkpoint_name) }}')"
                             class="flex items-center gap-sm p-sm rounded-xl border transition-all cursor-pointer"
                             :class="isSelected({{ $checkpoint->id }})
                                ? 'bg-primary/8 border-primary/30 opacity-60'
                                : 'border-outline-variant hover:bg-surface-container-low hover:border-outline'"
                             @click="!isSelected({{ $checkpoint->id }}) && addCheckpoint({{ $checkpoint->id }}, '{{ addslashes($checkpoint->checkpoint_name) }}')">
                            <div class="w-4 h-4 rounded border-2 flex-shrink-0 flex items-center justify-center transition-all"
                                 :class="isSelected({{ $checkpoint->id }}) ? 'bg-primary border-primary' : 'border-outline'">
                                <span class="material-symbols-outlined text-[12px] text-white" x-show="isSelected({{ $checkpoint->id }})">check</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-body-sm font-medium text-on-surface truncate">{{ $checkpoint->checkpoint_name }}</p>
                                @if($checkpoint->area)
                                <p class="text-label-md text-secondary truncate">{{ $checkpoint->area->area_name }}</p>
                                @endif
                            </div>
                            <template x-if="isSelected({{ $checkpoint->id }})">
                                <span class="text-label-md text-primary font-bold flex-shrink-0"
                                      x-text="'#' + (selectedCheckpoints.findIndex(c => c.id === {{ $checkpoint->id }}) + 1)"></span>
                            </template>
                        </div>
                        @empty
                        <div class="text-center py-lg text-secondary text-body-sm opacity-60">
                            <span class="material-symbols-outlined text-[32px] block mb-xs">location_off</span>
                            Belum ada checkpoint aktif
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- Tombol Submit & Batal --}}
                <div class="p-md border-t border-outline-variant bg-surface-bright flex flex-col gap-sm">
                    <button type="submit"
                            :disabled="selectedCheckpoints.length === 0"
                            class="w-full flex items-center justify-center gap-sm py-sm px-md rounded-xl text-body-sm font-bold transition-all
                                   bg-primary text-on-primary hover:opacity-90 active:scale-95
                                   disabled:opacity-40 disabled:cursor-not-allowed disabled:active:scale-100">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        Update Jadwal
                    </button>
                    <a href="{{ route('patrol-schedules.index') }}"
                       class="w-full flex items-center justify-center gap-sm py-sm px-md rounded-xl text-body-sm font-medium border border-outline-variant text-secondary hover:bg-surface-container transition-colors text-center">
                        Batal
                    </a>
                    <p class="text-label-md text-secondary text-center" x-show="selectedCheckpoints.length === 0">
                        ⚠️ Pilih minimal 1 checkpoint untuk melanjutkan.
                    </p>
                </div>
            </div>
        </div>

    </div>
</form>

{{-- ══ Alpine.js Logic (Edit — pre-populate dari $patrolSchedule->checkpoints) ══ --}}
@php
    $shiftOptions = $shifts->map(fn ($s) => [
        'id'         => $s->id,
        'name'       => $s->shift_name,
        'start_time' => \Carbon\Carbon::parse($s->start_time)->format('H:i'),
        'end_time'   => \Carbon\Carbon::parse($s->end_time)->format('H:i'),
    ])->values();

    $areaOptions = $areas->map(fn ($a) => [
        'id'   => $a->id,
        'name' => $a->area_name,
    ])->values();

    $savedCheckpoints = $patrolSchedule->checkpoints->map(fn ($cp) => [
        'id'   => $cp->id,
        'name' => $cp->checkpoint_name,
    ])->values();

    $oldCheckpoints = collect(array_values(old('checkpoints', [])));
    $selectedCheckpointsData = $oldCheckpoints->isNotEmpty()
        ? $oldCheckpoints->map(fn ($id) => ['id' => (int) $id, 'name' => ''])->values()
        : $savedCheckpoints;
@endphp
<script>
function patrolFormEdit() {
    return {
        selectedShiftId: '{{ old('shift_id', $patrolSchedule->shift_id) }}',
        selectedAreaId: '{{ old('area_id', $patrolSchedule->area_id) }}',
        selectedRound: '{{ old('round_number', $patrolSchedule->round_number) }}',
        shifts: @json($shiftOptions),
        areas: @json($areaOptions),
        selectedCheckpoints: @json($selectedCheckpointsData),
        checkpointSearch: '',

        get generatedName() {
            const area  = this.areas.find(a => String(a.id) === String(this.selectedAreaId));
            const shift = this.shifts.find(s => String(s.id) === String(this.selectedShiftId));

            if (!area || !shift || !this.selectedRound) {
                return '';
            }

            return `${area.name} - Shift ${shift.name} - Putaran ${this.selectedRound}`;
        },

        init() {
            // Jika ada old checkpoints, restore nama dari DOM
            this.selectedCheckpoints = this.selectedCheckpoints.map(cp => {
                if (cp.name) return cp;
                const el = document.querySelector(`[data-checkpoint-id="${cp.id}"]`);
                return { id: cp.id, name: el ? el.dataset.checkpointName : `Checkpoint ${cp.id}` };
            });
        },

        applyShiftTime(force = false) {
            const shift = this.shifts.find(s => String(s.id) === String(this.selectedShiftId));
            if (!shift) return;

            const startEl = document.getElementById('start_time');
            const endEl   = document.getElementById('end_time');
            if (!startEl || !endEl) return;

            if (force || !startEl.value) startEl.value = shift.start_time;
            if (force || !endEl.value)   endEl.value   = shift.end_time;
        },

        addCheckpoint(id, name) {
            if (!this.isSelected(id)) {
                this.selectedCheckpoints.push({ id, name });
            }
        },

        removeCheckpoint(id) {
            this.selectedCheckpoints = this.selectedCheckpoints.filter(cp => cp.id !== id);
        },

        isSelected(id) {
            return this.selectedCheckpoints.some(cp => cp.id === id);
        },

        moveUp(index) {
            if (index > 0) {
                [this.selectedCheckpoints[index - 1], this.selectedCheckpoints[index]] =
                [this.selectedCheckpoints[index], this.selectedCheckpoints[index - 1]];
                this.selectedCheckpoints = [...this.selectedCheckpoints];
            }
        },

        moveDown(index) {
            if (index < this.selectedCheckpoints.length - 1) {
                [this.selectedCheckpoints[index], this.selectedCheckpoints[index + 1]] =
                [this.selectedCheckpoints[index + 1], this.selectedCheckpoints[index]];
                this.selectedCheckpoints = [...this.selectedCheckpoints];
            }
        },

        checkpointFilter(name) {
            if (!this.checkpointSearch) return true;
            return name.toLowerCase().includes(this.checkpointSearch.toLowerCase());
        },

        handleSubmit(e) {
            if (this.selectedCheckpoints.length === 0) {
                alert('Minimal 1 checkpoint harus dipilih.');
                return;
            }
            e.target.submit();
        }
    }
}
</script>

</x-app-layout>
