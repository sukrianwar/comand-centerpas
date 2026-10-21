<x-app-layout>

{{-- ══ Flash Message ══════════════════════════════════════════════════════ --}}
@if(session('success'))
<div class="mb-md flex items-center gap-sm px-md py-sm bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-body-sm font-medium shadow-sm">
    <span class="material-symbols-outlined text-emerald-600 text-[20px]">check_circle</span>
    {{ session('success') }}
</div>
@endif

{{-- ══ Header Section ══════════════════════════════════════════════════════ --}}
<div class="flex flex-col md:flex-row md:items-end justify-between mb-lg gap-md">
    <div>
        <p class="text-body-md font-body-md text-secondary mb-xs">Guard Management</p>
        <h2 class="text-headline-lg font-headline-lg text-on-surface">Area &amp; Checkpoint</h2>
    </div>
    <div class="flex gap-sm">
        <a href="{{ route('areas.create') }}"
           class="bg-primary text-on-primary py-sm px-md rounded-xl hover:bg-surface-tint transition-colors flex items-center gap-xs text-body-sm font-body-sm font-semibold shadow-sm">
            <span class="material-symbols-outlined text-[18px]">add</span> Tambah Area Baru
        </a>
    </div>
</div>

{{-- ══ Filter & Search Bar ═════════════════════════════════════════════════ --}}
<form method="GET" action="{{ route('areas.index') }}"
      class="soft-card soft-card-hover p-md mb-md flex flex-col md:flex-row gap-sm items-stretch md:items-center">
    {{-- Search --}}
    <div class="relative flex-1 max-w-md">
        <span class="material-symbols-outlined absolute left-sm top-1/2 -translate-y-1/2 text-secondary text-[20px]">search</span>
        <input name="search" value="{{ request('search') }}"
               class="w-full bg-surface-container-low border border-outline-variant rounded-xl py-xs pl-[36px] pr-sm text-body-sm font-body-sm focus:border-primary-container focus:ring-2 focus:ring-primary-container/20 transition-all outline-none text-on-surface"
               placeholder="Cari nama area atau kode area..."
               type="text">
    </div>

    {{-- Tombol Submit & Reset --}}
    <button type="submit"
            class="bg-primary text-on-primary py-xs px-md rounded-xl text-body-sm font-semibold hover:opacity-90 transition-opacity whitespace-nowrap">
        Cari
    </button>
    @if(request()->filled('search'))
    <a href="{{ route('areas.index') }}"
       class="py-xs px-md rounded-xl text-body-sm border border-outline-variant text-secondary hover:bg-surface-container transition-colors whitespace-nowrap text-center">
        Reset
    </a>
    @endif
</form>

{{-- ══ Data Table ══════════════════════════════════════════════════════════ --}}
<div class="soft-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="table-modern w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-outline-variant/60 text-label-md font-label-md text-secondary">
                    <th class="py-sm px-md font-medium uppercase tracking-wider">Nama Area</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider">Kode Area</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider">Deskripsi</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider text-center">Jumlah Checkpoint</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider text-center">Jadwal Aktif</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-body-sm font-body-sm text-on-surface">

                @forelse($areas as $area)
                <tr class="border-b border-outline-variant {{ $loop->even ? 'bg-surface-container-low' : '' }} hover:bg-surface-container-low transition-colors group">

                    {{-- Nama Area --}}
                    <td class="py-sm px-md font-semibold text-primary">
                        {{ $area->area_name }}
                    </td>

                    {{-- Kode Area --}}
                    <td class="py-sm px-md">
                        <span class="inline-block px-2 py-1 bg-surface-container text-on-surface text-label-caps rounded border border-outline-variant font-bold">
                            {{ $area->area_code }}
                        </span>
                    </td>

                    {{-- Deskripsi --}}
                    <td class="py-sm px-md text-secondary">
                        {{ $area->description ?: '-' }}
                    </td>

                    {{-- Jumlah Checkpoint --}}
                    <td class="py-sm px-md text-center">
                        @if($area->checkpoints_count > 0)
                            <span class="inline-flex items-center justify-center px-2 py-1 bg-blue-50 text-blue-700 text-label-md rounded-full border border-blue-200 font-bold min-w-[30px]">
                                {{ $area->checkpoints_count }}
                            </span>
                        @else
                            <span class="text-secondary">-</span>
                        @endif
                    </td>

                    {{-- Jumlah Jadwal --}}
                    <td class="py-sm px-md text-center">
                        @if($area->patrol_schedules_count > 0)
                            <span class="inline-flex items-center justify-center px-2 py-1 bg-emerald-50 text-emerald-700 text-label-md rounded-full border border-emerald-200 font-bold min-w-[30px]">
                                {{ $area->patrol_schedules_count }}
                            </span>
                        @else
                            <span class="text-secondary">-</span>
                        @endif
                    </td>

                    {{-- Aksi --}}
                    <td class="py-sm px-md text-right whitespace-nowrap">
                        <a href="{{ route('areas.show', $area) }}"
                           class="inline-flex items-center gap-xs px-2 py-1 rounded-lg text-primary hover:bg-primary/10 transition-colors text-label-md font-semibold"
                           title="Kelola area & checkpoint">
                            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                            Kelola
                        </a>

                        <form method="POST"
                              action="{{ route('areas.destroy', $area) }}"
                              class="inline"
                              onsubmit="
                                @if($area->checkpoints_count > 0 || $area->patrol_schedules_count > 0)
                                    return confirm('Area ini masih memiliki {{ $area->checkpoints_count }} checkpoint dan {{ $area->patrol_schedules_count }} jadwal. Data tersebut TIDAK akan terhapus, hanya area ini yang akan disembunyikan. Lanjutkan?');
                                @else
                                    return confirm('Hapus area \'{{ addslashes($area->area_name) }}\'?');
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
                    <td colspan="6" class="py-xl px-md text-center text-secondary">
                        <div class="flex flex-col items-center gap-sm opacity-60">
                            <span class="material-symbols-outlined text-[48px]">location_on</span>
                            <p class="text-body-md font-medium">Belum ada data area</p>
                            @if(request()->filled('search'))
                                <p class="text-body-sm">Tidak ada hasil untuk pencarian. <a href="{{ route('areas.index') }}" class="text-primary underline">Reset pencarian</a></p>
                            @else
                                <p class="text-body-sm">Klik "Tambah Area Baru" untuk menambahkan data master area.</p>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse

            </tbody>
        </table>
    </div>

    {{-- ── Pagination ──────────────────────────────────────────────────── --}}
    @if($areas->hasPages())
    <div class="flex items-center justify-between p-md border-t border-outline-variant bg-surface-container-low">
        <p class="text-body-sm font-body-sm text-secondary">
            Menampilkan {{ $areas->firstItem() }}–{{ $areas->lastItem() }}
            dari {{ $areas->total() }} area
        </p>
        <div class="flex items-center gap-xs">
            {{-- Previous --}}
            @if($areas->onFirstPage())
                <span class="p-xs px-sm border border-outline-variant rounded-lg text-secondary opacity-40 text-body-sm cursor-not-allowed">
                    <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                </span>
            @else
                <a href="{{ $areas->previousPageUrl() }}"
                   class="p-xs px-sm border border-outline-variant rounded-lg text-secondary hover:bg-surface-container transition-colors text-body-sm">
                    <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                </a>
            @endif

            {{-- Page Numbers --}}
            @foreach($areas->getUrlRange(1, $areas->lastPage()) as $page => $url)
                @if($page === $areas->currentPage())
                    <span class="p-xs px-sm border border-primary bg-primary text-on-primary rounded-lg text-body-sm font-semibold">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $url }}"
                       class="p-xs px-sm border border-outline-variant rounded-lg text-secondary hover:bg-surface-container transition-colors text-body-sm">
                        {{ $page }}
                    </a>
                @endif
            @endforeach

            {{-- Next --}}
            @if($areas->hasMorePages())
                <a href="{{ $areas->nextPageUrl() }}"
                   class="p-xs px-sm border border-outline-variant rounded-lg text-secondary hover:bg-surface-container transition-colors text-body-sm">
                    <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                </a>
            @else
                <span class="p-xs px-sm border border-outline-variant rounded-lg text-secondary opacity-40 text-body-sm cursor-not-allowed">
                    <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                </span>
            @endif
        </div>
    </div>
    @endif
</div>

</x-app-layout>
