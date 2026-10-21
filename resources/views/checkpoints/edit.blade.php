<x-app-layout>

{{-- ══ Header ════════════════════════════════════════════════════════════ --}}
<div class="flex flex-col md:flex-row md:items-end justify-between mb-lg gap-md">
    <div>
        <p class="text-body-md text-secondary mb-xs">
            <a href="{{ route('areas.index') }}" class="hover:text-primary transition-colors">Area &amp; Checkpoint</a>
            <span class="mx-1 opacity-40">/</span>
            <a href="{{ route('areas.show', $area) }}" class="hover:text-primary transition-colors">{{ $area->area_name }}</a>
            <span class="mx-1 opacity-40">/</span>
            <span>Edit Checkpoint</span>
        </p>
        <h2 class="text-headline-lg font-headline-lg text-on-surface">Edit Checkpoint</h2>
    </div>
</div>

{{-- ══ Form ════════════════════════════════════════════════════════════════ --}}
<form method="POST" action="{{ route('areas.checkpoints.update', [$area, $checkpoint]) }}">
    @csrf
    @method('PUT')

    <div class="max-w-3xl soft-card p-lg">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-md">

            {{-- Area (read-only, dari route) --}}
            <div class="md:col-span-2">
                <span class="block text-body-sm font-semibold text-on-surface mb-xs">Area Lokasi</span>
                <div class="flex items-center gap-sm bg-surface-container-low border border-outline-variant rounded-xl py-xs px-sm text-body-sm text-on-surface">
                    <span class="material-symbols-outlined text-[18px] text-primary">location_on</span>
                    <span class="font-semibold">{{ $area->area_name }}</span>
                    <span class="ml-auto inline-block px-2 py-0.5 bg-surface-container text-secondary text-label-caps rounded border border-outline-variant font-bold font-mono">{{ $area->area_code }}</span>
                </div>
            </div>

            {{-- Nama Checkpoint --}}
            <div class="md:col-span-2">
                <label for="checkpoint_name" class="block text-body-sm font-semibold text-on-surface mb-xs">
                    Nama Checkpoint <span class="text-red-500">*</span>
                </label>
                <input type="text" id="checkpoint_name" name="checkpoint_name"
                       value="{{ old('checkpoint_name', $checkpoint->checkpoint_name) }}"
                       placeholder="Contoh: Pintu Utama Barat"
                       class="w-full bg-surface-container-low border rounded-xl py-xs px-sm text-body-sm text-on-surface outline-none transition-all
                              @error('checkpoint_name') border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-200
                              @else border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/10 @enderror">
                <x-input-error :messages="$errors->get('checkpoint_name')" class="mt-xs" />
            </div>

            {{-- Status --}}
            <div class="md:col-span-2">
                <label for="status" class="block text-body-sm font-semibold text-on-surface mb-xs">
                    Status <span class="text-red-500">*</span>
                </label>
                <select id="status" name="status"
                        class="w-full bg-surface-container-low border rounded-xl py-xs px-sm text-body-sm text-on-surface outline-none transition-all
                               @error('status') border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-200
                               @else border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/10 @enderror">
                    <option value="active" {{ old('status', $checkpoint->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $checkpoint->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                                 @else border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/10 @enderror">{{ old('description', $checkpoint->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-xs" />
            </div>

        </div>

        {{-- Tombol Aksi --}}
        <div class="mt-lg pt-md border-t border-outline-variant flex gap-sm justify-end">
            <a href="{{ route('areas.show', $area) }}"
               class="flex items-center gap-sm py-sm px-md rounded-xl text-body-sm font-medium border border-outline-variant text-secondary hover:bg-surface-container transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="flex items-center gap-sm py-sm px-md rounded-xl text-body-sm font-bold bg-primary text-on-primary hover:opacity-90 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-[18px]">save</span>
                Update Checkpoint
            </button>
        </div>

    </div>
</form>

</x-app-layout>
