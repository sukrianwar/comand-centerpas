<x-app-layout>

{{-- ══ Flash Message ══════════════════════════════════════════════════════ --}}
@if(session('success'))
<div class="mb-md flex items-center gap-sm px-md py-sm bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-body-sm font-medium shadow-sm">
    <span class="material-symbols-outlined text-emerald-600 text-[20px]">check_circle</span>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mb-md flex items-center gap-sm px-md py-sm bg-red-50 border border-red-200 rounded-xl text-red-800 text-body-sm font-medium shadow-sm">
    <span class="material-symbols-outlined text-red-600 text-[20px]">error</span>
    {{ session('error') }}
</div>
@endif

{{-- ══ Header ════════════════════════════════════════════════════════════ --}}
<div class="flex flex-col md:flex-row md:items-end justify-between mb-lg gap-md">
    <div>
        <p class="text-body-md text-secondary mb-xs">
            <a href="{{ route('areas.index') }}" class="hover:text-primary transition-colors">Area &amp; Checkpoint</a>
            <span class="mx-1 opacity-40">/</span>
            <span>Detail Area</span>
        </p>
        <h2 class="text-headline-lg font-headline-lg text-on-surface">{{ $area->area_name }}</h2>
        <p class="text-body-md text-secondary mt-xs flex items-center gap-sm">
            <span class="inline-block px-2 py-0.5 bg-surface-container text-on-surface text-label-caps rounded border border-outline-variant font-bold font-mono">{{ $area->area_code }}</span>
            <span>{{ $area->checkpoints_count }} checkpoint &bull; {{ $area->patrol_schedules_count }} jadwal</span>
        </p>
    </div>
    <a href="{{ route('areas.index') }}"
       class="inline-flex items-center gap-sm py-sm px-md rounded-xl text-body-sm font-medium border border-outline-variant text-secondary hover:bg-surface-container transition-colors">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        Kembali
    </a>
</div>

{{-- ══ Card: Informasi Area (Form Edit) ═══════════════════════════════════ --}}
<div class="soft-card p-lg mb-lg">
    <h3 class="text-headline-md font-headline-md text-on-surface mb-md flex items-center gap-sm">
        <span class="material-symbols-outlined text-[22px] text-primary">location_on</span>
        Informasi Area
    </h3>

    <form method="POST" action="{{ route('areas.update', $area) }}">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-md">
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
                <x-input-error :messages="$errors->get('area_code')" class="mt-xs" />
            </div>

            {{-- Deskripsi --}}
            <div class="md:col-span-2">
                <label for="description" class="block text-body-sm font-semibold text-on-surface mb-xs">
                    Deskripsi <span class="text-secondary font-normal">(opsional)</span>
                </label>
                <textarea id="description" name="description" rows="2"
                          placeholder="Penjelasan singkat mengenai area ini..."
                          class="w-full bg-surface-container-low border rounded-xl py-xs px-sm text-body-sm text-on-surface outline-none transition-all
                                 @error('description') border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-200
                                 @else border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/10 @enderror">{{ old('description', $area->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-xs" />
            </div>
        </div>

        <div class="mt-md flex justify-end">
            <button type="submit"
                    class="flex items-center gap-sm py-sm px-md rounded-xl text-body-sm font-bold bg-primary text-on-primary hover:opacity-90 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-[18px]">save</span>
                Update Area
            </button>
        </div>
    </form>
</div>

{{-- ══ Card: Checkpoint Area ══════════════════════════════════════════════ --}}
<div class="soft-card overflow-hidden">
    <div class="p-md border-b border-outline-variant bg-surface-bright flex flex-col sm:flex-row sm:items-center justify-between gap-sm">
        <div>
            <h3 class="text-headline-md font-headline-md text-on-surface flex items-center gap-sm">
                <span class="material-symbols-outlined text-[22px] text-primary">pin_drop</span>
                Checkpoint Area
            </h3>
            <p class="text-label-md text-secondary mt-xs">Titik-titik checkpoint yang berada di area ini.</p>
        </div>
        <a href="{{ route('areas.checkpoints.create', $area) }}"
           class="bg-primary text-on-primary py-sm px-md rounded-xl hover:bg-surface-tint transition-colors flex items-center justify-center gap-xs text-body-sm font-semibold shadow-sm whitespace-nowrap">
            <span class="material-symbols-outlined text-[18px]">add</span> Tambah Checkpoint
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="table-modern w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-outline-variant/60 text-label-md font-label-md text-secondary">
                    <th class="py-sm px-md font-medium uppercase tracking-wider">Nama Checkpoint</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider">Deskripsi</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider text-center">Status</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider text-center">Jadwal</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-body-sm font-body-sm text-on-surface">
                @forelse($checkpoints as $cp)
                <tr class="border-b border-outline-variant {{ $loop->even ? 'bg-surface-container-low' : '' }} hover:bg-surface-container-low transition-colors group">
                    <td class="py-sm px-md font-semibold text-primary">{{ $cp->checkpoint_name }}</td>

                    <td class="py-sm px-md text-secondary">{{ $cp->description ?: '-' }}</td>

                    <td class="py-sm px-md text-center">
                        @if($cp->status === 'active')
                            <span class="inline-flex items-center justify-center px-2 py-1 bg-emerald-50 text-emerald-700 text-[11px] uppercase tracking-wider rounded-full border border-emerald-200 font-bold">
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center justify-center px-2 py-1 bg-surface-container-high text-secondary text-[11px] uppercase tracking-wider rounded-full border border-outline-variant font-bold">
                                Nonaktif
                            </span>
                        @endif
                    </td>

                    <td class="py-sm px-md text-center">
                        @if($cp->patrol_schedules_count > 0)
                            <span class="inline-flex items-center justify-center px-2 py-1 bg-blue-50 text-blue-700 text-label-md rounded-full border border-blue-200 font-bold min-w-[30px]">
                                {{ $cp->patrol_schedules_count }}
                            </span>
                        @else
                            <span class="text-secondary">-</span>
                        @endif
                    </td>

                    <td class="py-sm px-md text-right whitespace-nowrap">
                        <a href="{{ route('areas.checkpoints.edit', [$area, $cp]) }}"
                           class="inline-flex p-xs text-on-surface-variant hover:bg-surface-container rounded-full transition-colors" title="Edit">
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </a>

                        <form method="POST"
                              action="{{ route('areas.checkpoints.destroy', [$area, $cp]) }}"
                              class="inline"
                              onsubmit="
                                @if($cp->patrol_schedules_count > 0)
                                    return confirm('Checkpoint ini masih terkait dengan {{ $cp->patrol_schedules_count }} jadwal patroli. Data riwayat TIDAK akan terhapus, hanya checkpoint ini yang akan dinonaktifkan/disembunyikan. Lanjutkan hapus?');
                                @else
                                    return confirm('Hapus checkpoint \'{{ addslashes($cp->checkpoint_name) }}\'?');
                                @endif
                              ">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="p-xs text-red-500 hover:bg-red-50 rounded-full transition-colors" title="Hapus">
                                <span class="material-symbols-outlined text-[20px]">delete</span>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-xl px-md text-center text-secondary">
                        <div class="flex flex-col items-center gap-sm opacity-60">
                            <span class="material-symbols-outlined text-[48px]">pin_drop</span>
                            <p class="text-body-md font-medium">Belum ada checkpoint di area ini</p>
                            <p class="text-body-sm">Klik "Tambah Checkpoint" untuk menambahkan titik pertama.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</x-app-layout>
