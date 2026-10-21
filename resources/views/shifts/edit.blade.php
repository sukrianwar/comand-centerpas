<x-app-layout>

{{-- ══ Header ════════════════════════════════════════════════════════════ --}}
<div class="flex flex-col md:flex-row md:items-end justify-between mb-lg gap-md">
    <div>
        <p class="text-body-md text-secondary mb-xs">
            <a href="{{ route('shifts.index') }}" class="hover:text-primary transition-colors">Data Master Shift</a>
            <span class="mx-1 opacity-40">/</span>
            <span>Edit Shift</span>
        </p>
        <h2 class="text-headline-lg font-headline-lg text-on-surface">Edit Shift</h2>
    </div>
</div>

{{-- ══ Form ════════════════════════════════════════════════════════════════ --}}
<form method="POST" action="{{ route('shifts.update', $shift) }}">
    @csrf
    @method('PUT')

    <div class="max-w-2xl soft-card p-lg">

        <div class="space-y-md">

            {{-- Nama Shift --}}
            <div>
                <label for="shift_name" class="block text-body-sm font-semibold text-on-surface mb-xs">
                    Nama Shift <span class="text-red-500">*</span>
                </label>
                <input type="text" id="shift_name" name="shift_name"
                       value="{{ old('shift_name', $shift->shift_name) }}"
                       placeholder="Contoh: Pagi"
                       class="w-full bg-surface-container-low border rounded-xl py-xs px-sm text-body-sm text-on-surface outline-none transition-all
                              @error('shift_name') border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-200
                              @else border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/10 @enderror">
                <x-input-error :messages="$errors->get('shift_name')" class="mt-xs" />
            </div>

            {{-- Kode Shift --}}
            <div>
                <label for="shift_code" class="block text-body-sm font-semibold text-on-surface mb-xs">
                    Kode Shift <span class="text-secondary font-normal">(opsional)</span>
                </label>
                <input type="text" id="shift_code" name="shift_code"
                       value="{{ old('shift_code', $shift->shift_code) }}"
                       placeholder="Contoh: SFT-01"
                       class="w-full bg-surface-container-low border rounded-xl py-xs px-sm text-body-sm text-on-surface outline-none transition-all font-mono uppercase
                              @error('shift_code') border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-200
                              @else border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/10 @enderror">
                <p class="text-label-md text-secondary mt-xs">Kosongkan untuk mempertahankan / membuat kode otomatis.</p>
                <x-input-error :messages="$errors->get('shift_code')" class="mt-xs" />
            </div>

            {{-- Jam Mulai & Selesai --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-md">
                <div>
                    <label for="start_time" class="block text-body-sm font-semibold text-on-surface mb-xs">
                        Jam Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="time" id="start_time" name="start_time"
                           value="{{ old('start_time', \Carbon\Carbon::parse($shift->start_time)->format('H:i')) }}"
                           class="w-full bg-surface-container-low border rounded-xl py-xs px-sm text-body-sm text-on-surface outline-none transition-all
                                  @error('start_time') border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-200
                                  @else border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/10 @enderror">
                    <x-input-error :messages="$errors->get('start_time')" class="mt-xs" />
                </div>
                <div>
                    <label for="end_time" class="block text-body-sm font-semibold text-on-surface mb-xs">
                        Jam Selesai <span class="text-red-500">*</span>
                    </label>
                    <input type="time" id="end_time" name="end_time"
                           value="{{ old('end_time', \Carbon\Carbon::parse($shift->end_time)->format('H:i')) }}"
                           class="w-full bg-surface-container-low border rounded-xl py-xs px-sm text-body-sm text-on-surface outline-none transition-all
                                  @error('end_time') border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-200
                                  @else border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/10 @enderror">
                    <x-input-error :messages="$errors->get('end_time')" class="mt-xs" />
                </div>
            </div>

            <p class="text-label-md text-secondary">
                <span class="material-symbols-outlined text-[14px] align-middle">info</span>
                Jika jam selesai lebih awal dari jam mulai, shift dianggap melewati tengah malam (contoh: 23:00 → 07:00).
            </p>

            {{-- Status --}}
            <div>
                <label for="status" class="block text-body-sm font-semibold text-on-surface mb-xs">
                    Status <span class="text-red-500">*</span>
                </label>
                <select id="status" name="status"
                        class="w-full bg-surface-container-low border rounded-xl py-xs px-sm text-body-sm text-on-surface outline-none transition-all
                               @error('status') border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-200
                               @else border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/10 @enderror">
                    <option value="active" {{ old('status', $shift->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ old('status', $shift->status) === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <x-input-error :messages="$errors->get('status')" class="mt-xs" />
            </div>

            {{-- Deskripsi --}}
            <div>
                <label for="description" class="block text-body-sm font-semibold text-on-surface mb-xs">
                    Deskripsi <span class="text-secondary font-normal">(opsional)</span>
                </label>
                <textarea id="description" name="description" rows="3"
                          placeholder="Penjelasan singkat mengenai shift ini..."
                          class="w-full bg-surface-container-low border rounded-xl py-xs px-sm text-body-sm text-on-surface outline-none transition-all
                                 @error('description') border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-200
                                 @else border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/10 @enderror">{{ old('description', $shift->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-xs" />
            </div>

        </div>

        {{-- Tombol Aksi --}}
        <div class="mt-lg pt-md border-t border-outline-variant flex gap-sm justify-end">
            <a href="{{ route('shifts.index') }}"
               class="flex items-center gap-sm py-sm px-md rounded-xl text-body-sm font-medium border border-outline-variant text-secondary hover:bg-surface-container transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="flex items-center gap-sm py-sm px-md rounded-xl text-body-sm font-bold bg-primary text-on-primary hover:opacity-90 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-[18px]">save</span>
                Update Shift
            </button>
        </div>

    </div>
</form>

</x-app-layout>
