<x-app-layout>

{{-- ══ Header ════════════════════════════════════════════════════════════ --}}
<div class="flex flex-col md:flex-row md:items-end justify-between mb-lg gap-md">
    <div>
        <p class="text-body-md text-secondary mb-xs">
            <a href="{{ route('checkpoints.index') }}" class="hover:text-primary transition-colors">Data Checkpoint</a>
            <span class="mx-1 opacity-40">/</span>
            <span>Tambah Checkpoint Baru</span>
        </p>
        <h2 class="text-headline-lg font-headline-lg text-on-surface">Tambah Checkpoint Baru</h2>
    </div>
</div>

{{-- ══ Form ════════════════════════════════════════════════════════════════ --}}
<form method="POST" action="{{ route('checkpoints.store') }}">
    @csrf

    <div class="max-w-3xl bg-surface-container-lowest border border-outline-variant rounded-xl p-lg">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-md">
            
            {{-- Area --}}
            <div class="md:col-span-2">
                <label for="area_id" class="block text-body-sm font-semibold text-on-surface mb-xs">
                    Area Lokasi <span class="text-red-500">*</span>
                </label>
                <select id="area_id" name="area_id"
                        class="w-full bg-surface-container-low border rounded-xl py-xs px-sm text-body-sm text-on-surface outline-none transition-all
                               @error('area_id') border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-200
                               @else border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/10 @enderror">
                    <option value="" disabled {{ old('area_id') ? '' : 'selected' }}>-- Pilih Area --</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" {{ old('area_id') == $area->id ? 'selected' : '' }}>
                            {{ $area->area_name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('area_id')" class="mt-xs" />
            </div>

            {{-- Nama Checkpoint --}}
            <div class="md:col-span-2">
                <label for="checkpoint_name" class="block text-body-sm font-semibold text-on-surface mb-xs">
                    Nama Checkpoint <span class="text-red-500">*</span>
                </label>
                <input type="text" id="checkpoint_name" name="checkpoint_name"
                       value="{{ old('checkpoint_name') }}"
                       placeholder="Contoh: Pintu Utama Barat"
                       class="w-full bg-surface-container-low border rounded-xl py-xs px-sm text-body-sm text-on-surface outline-none transition-all
                              @error('checkpoint_name') border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-200
                              @else border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/10 @enderror">
                <x-input-error :messages="$errors->get('checkpoint_name')" class="mt-xs" />
            </div>

            {{-- Latitude --}}
            <div>
                <label for="latitude" class="block text-body-sm font-semibold text-on-surface mb-xs">
                    Latitude <span class="text-red-500">*</span>
                </label>
                <input type="text" id="latitude" name="latitude"
                       value="{{ old('latitude') }}"
                       placeholder="Contoh: -6.200000"
                       class="w-full bg-surface-container-low border rounded-xl py-xs px-sm text-body-sm text-on-surface outline-none transition-all font-mono
                              @error('latitude') border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-200
                              @else border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/10 @enderror">
                <x-input-error :messages="$errors->get('latitude')" class="mt-xs" />
            </div>

            {{-- Longitude --}}
            <div>
                <label for="longitude" class="block text-body-sm font-semibold text-on-surface mb-xs">
                    Longitude <span class="text-red-500">*</span>
                </label>
                <input type="text" id="longitude" name="longitude"
                       value="{{ old('longitude') }}"
                       placeholder="Contoh: 106.816666"
                       class="w-full bg-surface-container-low border rounded-xl py-xs px-sm text-body-sm text-on-surface outline-none transition-all font-mono
                              @error('longitude') border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-200
                              @else border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/10 @enderror">
                <x-input-error :messages="$errors->get('longitude')" class="mt-xs" />
            </div>

            <div class="md:col-span-2">
                <p class="text-label-md text-secondary">
                    <span class="material-symbols-outlined text-[14px] align-middle">info</span> 
                    Tips: Gunakan Google Maps untuk mencari koordinat. Klik kanan pada lokasi yang diinginkan > salin koordinat.
                </p>
            </div>

            {{-- Radius --}}
            <div>
                <label for="radius" class="block text-body-sm font-semibold text-on-surface mb-xs">
                    Radius <span class="text-secondary font-normal">(opsional)</span>
                </label>
                <div class="relative">
                    <input type="number" id="radius" name="radius"
                           value="{{ old('radius', 20) }}"
                           min="5" max="500"
                           class="w-full bg-surface-container-low border rounded-xl py-xs pl-sm pr-[50px] text-body-sm text-on-surface outline-none transition-all
                                  @error('radius') border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-200
                                  @else border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/10 @enderror">
                    <span class="absolute right-sm top-1/2 -translate-y-1/2 text-secondary text-body-sm">meter</span>
                </div>
                <x-input-error :messages="$errors->get('radius')" class="mt-xs" />
            </div>

            {{-- Status --}}
            <div>
                <label for="status" class="block text-body-sm font-semibold text-on-surface mb-xs">
                    Status <span class="text-red-500">*</span>
                </label>
                <select id="status" name="status"
                        class="w-full bg-surface-container-low border rounded-xl py-xs px-sm text-body-sm text-on-surface outline-none transition-all
                               @error('status') border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-200
                               @else border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/10 @enderror">
                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                <x-input-error :messages="$errors->get('status')" class="mt-xs" />
            </div>

            {{-- Deskripsi --}}
            <div class="md:col-span-2">
                <label for="description" class="block text-body-sm font-semibold text-on-surface mb-xs">
                    Deskripsi <span class="text-secondary font-normal">(opsional)</span>
                </label>
                <textarea id="description" name="description" rows="3"
                          placeholder="Penjelasan singkat lokasi checkpoint..."
                          class="w-full bg-surface-container-low border rounded-xl py-xs px-sm text-body-sm text-on-surface outline-none transition-all
                                 @error('description') border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-200
                                 @else border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/10 @enderror">{{ old('description') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-xs" />
            </div>

        </div>

        {{-- Tombol Aksi --}}
        <div class="mt-lg pt-md border-t border-outline-variant flex gap-sm justify-end">
            <a href="{{ route('checkpoints.index') }}"
               class="flex items-center gap-sm py-sm px-md rounded-xl text-body-sm font-medium border border-outline-variant text-secondary hover:bg-surface-container transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="flex items-center gap-sm py-sm px-md rounded-xl text-body-sm font-bold bg-primary text-on-primary hover:opacity-90 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-[18px]">save</span>
                Simpan Checkpoint
            </button>
        </div>

    </div>
</form>

</x-app-layout>
