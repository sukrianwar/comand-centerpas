<x-app-layout>

{{-- ══ Header ════════════════════════════════════════════════════════════ --}}
<div class="flex flex-col md:flex-row md:items-end justify-between mb-lg gap-md">
    <div>
        <p class="text-body-md text-secondary mb-xs">
            <a href="{{ route('areas.index') }}" class="hover:text-primary transition-colors">Data Master Area</a>
            <span class="mx-1 opacity-40">/</span>
            <span>Edit Area</span>
        </p>
        <h2 class="text-headline-lg font-headline-lg text-on-surface">Edit Area</h2>
    </div>
</div>

{{-- ══ Form ════════════════════════════════════════════════════════════════ --}}
<form method="POST" action="{{ route('areas.update', $area) }}">
    @csrf
    @method('PUT')

    <div class="max-w-2xl bg-surface-container-lowest border border-outline-variant rounded-xl p-lg">
        
        <div class="space-y-md">
            
            {{-- Nama Area --}}
            <div>
                <label for="area_name" class="block text-body-sm font-semibold text-on-surface mb-xs">
                    Nama Area <span class="text-red-500">*</span>
                </label>
                <input type="text" id="area_name" name="area_name"
                       value="{{ old('area_name', $area->area_name) }}"
                       placeholder="Contoh: Gedung Utama"
                       class="w-full bg-surface-container-low border rounded-xl py-xs px-sm text-body-sm text-on-surface outline-none transition-all
                              @error('area_name') border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-200
                              @else border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/10 @enderror">
                <x-input-error :messages="$errors->get('area_name')" class="mt-xs" />
            </div>

            {{-- Kode Area --}}
            <div>
                <label for="area_code" class="block text-body-sm font-semibold text-on-surface mb-xs">
                    Kode Area <span class="text-red-500">*</span>
                </label>
                <input type="text" id="area_code" name="area_code"
                       value="{{ old('area_code', $area->area_code) }}"
                       placeholder="Contoh: GD-UTAMA"
                       class="w-full bg-surface-container-low border rounded-xl py-xs px-sm text-body-sm text-on-surface outline-none transition-all font-mono uppercase
                              @error('area_code') border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-200
                              @else border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/10 @enderror">
                <p class="text-label-md text-secondary mt-xs">Kode unik untuk identifikasi area. Maksimal 30 karakter.</p>
                <x-input-error :messages="$errors->get('area_code')" class="mt-xs" />
            </div>

            {{-- Deskripsi --}}
            <div>
                <label for="description" class="block text-body-sm font-semibold text-on-surface mb-xs">
                    Deskripsi <span class="text-secondary font-normal">(opsional)</span>
                </label>
                <textarea id="description" name="description" rows="3"
                          placeholder="Penjelasan singkat mengenai area ini..."
                          class="w-full bg-surface-container-low border rounded-xl py-xs px-sm text-body-sm text-on-surface outline-none transition-all
                                 @error('description') border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-200
                                 @else border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/10 @enderror">{{ old('description', $area->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-xs" />
            </div>

        </div>

        {{-- Tombol Aksi --}}
        <div class="mt-lg pt-md border-t border-outline-variant flex gap-sm justify-end">
            <a href="{{ route('areas.index') }}"
               class="flex items-center gap-sm py-sm px-md rounded-xl text-body-sm font-medium border border-outline-variant text-secondary hover:bg-surface-container transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="flex items-center gap-sm py-sm px-md rounded-xl text-body-sm font-bold bg-primary text-on-primary hover:opacity-90 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-[18px]">save</span>
                Update Area
            </button>
        </div>

    </div>
</form>

</x-app-layout>
