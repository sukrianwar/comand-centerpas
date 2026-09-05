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
        <h2 class="text-headline-lg font-headline-lg text-on-surface">Data Checkpoint</h2>
    </div>
    <div class="flex flex-col sm:flex-row gap-sm items-stretch sm:items-center">
        <form method="GET" action="{{ route('checkpoints.qr.print') }}" class="flex flex-col sm:flex-row gap-sm items-stretch sm:items-center">
            <select name="area_id" required
                    class="bg-surface-container-low border border-outline-variant rounded-xl py-xs px-sm text-body-sm text-on-surface outline-none focus:border-primary focus:ring-2 focus:ring-primary/10">
                <option value="" disabled selected>Pilih Area...</option>
                @foreach($areas as $area)
                    <option value="{{ $area->id }}">{{ $area->area_name }}</option>
                @endforeach
            </select>
            <button type="submit"
                    class="bg-surface-container border border-outline-variant text-on-surface py-xs px-md rounded-xl hover:bg-surface-container-high transition-colors flex items-center justify-center gap-xs text-body-sm font-body-sm font-semibold whitespace-nowrap">
                <span class="material-symbols-outlined text-[18px]">print</span> Cetak QR
            </button>
        </form>
        <a href="{{ route('checkpoints.create') }}"
           class="bg-primary text-on-primary py-sm px-md rounded-xl hover:bg-surface-tint transition-colors flex items-center gap-xs text-body-sm font-body-sm font-semibold shadow-sm">
            <span class="material-symbols-outlined text-[18px]">add</span> Tambah Checkpoint Baru
        </a>
    </div>
</div>

{{-- ══ Filter & Search Bar ═════════════════════════════════════════════════ --}}
<form method="GET" action="{{ route('checkpoints.index') }}"
      class="bg-surface-container-lowest border border-outline-variant rounded-xl p-md mb-md flex flex-col lg:flex-row gap-sm items-stretch lg:items-center">
    
    {{-- Search --}}
    <div class="relative flex-1">
        <span class="material-symbols-outlined absolute left-sm top-1/2 -translate-y-1/2 text-secondary text-[20px]">search</span>
        <input name="search" value="{{ request('search') }}"
               class="w-full bg-surface-container-low border border-outline-variant rounded-xl py-xs pl-[36px] pr-sm text-body-sm font-body-sm focus:border-primary-container focus:ring-2 focus:ring-primary-container/20 transition-all outline-none text-on-surface"
               placeholder="Cari nama atau QR Code..."
               type="text">
    </div>

    {{-- Filter Area --}}
    <div class="w-full lg:w-48">
        <select name="area_id"
                class="w-full bg-surface-container-low border border-outline-variant rounded-xl py-xs px-sm text-body-sm text-on-surface outline-none focus:border-primary focus:ring-2 focus:ring-primary/10">
            <option value="">Semua Area</option>
            @foreach($areas as $area)
                <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>
                    {{ $area->area_name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Filter Status --}}
    <div class="w-full lg:w-36">
        <select name="status"
                class="w-full bg-surface-container-low border border-outline-variant rounded-xl py-xs px-sm text-body-sm text-on-surface outline-none focus:border-primary focus:ring-2 focus:ring-primary/10">
            <option value="">Semua Status</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>

    {{-- Tombol Submit & Reset --}}
    <button type="submit"
            class="bg-primary text-on-primary py-xs px-md rounded-xl text-body-sm font-semibold hover:opacity-90 transition-opacity whitespace-nowrap">
        Filter
    </button>
    @if(request()->filled('search') || request()->filled('area_id') || request()->filled('status'))
    <a href="{{ route('checkpoints.index') }}"
       class="py-xs px-md rounded-xl text-body-sm border border-outline-variant text-secondary hover:bg-surface-container transition-colors whitespace-nowrap text-center">
        Reset
    </a>
    @endif
</form>

{{-- ══ Data Table ══════════════════════════════════════════════════════════ --}}
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-outline-variant text-label-md font-label-md text-secondary bg-surface-bright">
                    <th class="py-sm px-md font-medium uppercase tracking-wider">Nama Checkpoint</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider">Area</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider">QR Code</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider">Koordinat & Radius</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider text-center">Status</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider text-center">Jadwal Aktif</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-body-sm font-body-sm text-on-surface">

                @forelse($checkpoints as $cp)
                <tr class="border-b border-outline-variant {{ $loop->even ? 'bg-[#F9FAFB]' : '' }} hover:bg-surface-container-low transition-colors group">

                    {{-- Nama Checkpoint --}}
                    <td class="py-sm px-md font-semibold text-primary">
                        {{ $cp->checkpoint_name }}
                    </td>

                    {{-- Area --}}
                    <td class="py-sm px-md">
                        <span class="inline-flex items-center gap-[4px] text-secondary">
                            <span class="material-symbols-outlined text-[16px]">location_on</span>
                            {{ $cp->area->area_name }}
                        </span>
                    </td>

                    {{-- QR Code --}}
                    <td class="py-sm px-md">
                        <div class="flex items-center gap-sm">
                            <a href="{{ route('checkpoints.qr', $cp) }}" target="_blank" title="Buka QR ukuran penuh">
                                <img src="{{ route('checkpoints.qr', $cp) }}"
                                     alt="QR {{ $cp->qr_code }}"
                                     class="w-10 h-10 rounded-md border border-outline-variant bg-white">
                            </a>
                            <div class="flex flex-col gap-[2px]">
                                <span class="inline-flex items-center gap-xs px-2 py-1 bg-surface-container text-on-surface rounded border border-outline-variant">
                                    <span class="font-mono font-bold text-label-caps">{{ $cp->qr_code }}</span>
                                    <button type="button"
                                            onclick="navigator.clipboard.writeText('{{ $cp->qr_code }}'); alert('QR Code tersalin!');"
                                            class="text-secondary hover:text-primary transition-colors flex items-center justify-center"
                                            title="Salin QR Code">
                                        <span class="material-symbols-outlined text-[14px]">content_copy</span>
                                    </button>
                                </span>
                                <a href="{{ route('checkpoints.qr', $cp) }}" target="_blank"
                                   class="text-label-sm font-semibold text-primary hover:underline inline-flex items-center gap-[2px]"
                                   title="Unduh / cetak QR">
                                    <span class="material-symbols-outlined text-[12px]">download</span> Unduh QR
                                </a>
                            </div>
                        </div>
                    </td>

                    {{-- Koordinat & Radius --}}
                    <td class="py-sm px-md">
                        <div class="text-[12px] text-secondary font-mono">
                            Lat: {{ $cp->latitude }}<br>
                            Lon: {{ $cp->longitude }}
                        </div>
                        <div class="mt-[2px] text-label-sm font-medium text-primary">
                            Radius: {{ $cp->radius }}m
                        </div>
                    </td>

                    {{-- Status --}}
                    <td class="py-sm px-md text-center">
                        @if($cp->status === 'active')
                            <span class="inline-flex items-center justify-center px-2 py-1 bg-emerald-50 text-emerald-700 text-[11px] uppercase tracking-wider rounded-full border border-emerald-200 font-bold">
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center justify-center px-2 py-1 bg-surface-container-high text-secondary text-[11px] uppercase tracking-wider rounded-full border border-outline-variant font-bold">
                                Inactive
                            </span>
                        @endif
                    </td>

                    {{-- Jumlah Jadwal --}}
                    <td class="py-sm px-md text-center">
                        @if($cp->patrol_schedules_count > 0)
                            <span class="inline-flex items-center justify-center px-2 py-1 bg-blue-50 text-blue-700 text-label-md rounded-full border border-blue-200 font-bold min-w-[30px]">
                                {{ $cp->patrol_schedules_count }}
                            </span>
                        @else
                            <span class="text-secondary">-</span>
                        @endif
                    </td>

                    {{-- Aksi --}}
                    <td class="py-sm px-md text-right whitespace-nowrap">
                        <a href="{{ route('checkpoints.edit', $cp) }}"
                           class="inline-flex p-xs text-on-surface-variant hover:bg-surface-container rounded-full transition-colors" title="Edit">
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </a>

                        <form method="POST"
                              action="{{ route('checkpoints.destroy', $cp) }}"
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
                    <td colspan="7" class="py-xl px-md text-center text-secondary">
                        <div class="flex flex-col items-center gap-sm opacity-60">
                            <span class="material-symbols-outlined text-[48px]">qr_code_scanner</span>
                            <p class="text-body-md font-medium">Belum ada data checkpoint</p>
                            @if(request()->filled('search') || request()->filled('area_id') || request()->filled('status'))
                                <p class="text-body-sm">Tidak ada hasil untuk filter pencarian. <a href="{{ route('checkpoints.index') }}" class="text-primary underline">Reset filter</a></p>
                            @else
                                <p class="text-body-sm">Klik "Tambah Checkpoint Baru" untuk meregistrasi titik poin.</p>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse

            </tbody>
        </table>
    </div>

    {{-- ── Pagination ──────────────────────────────────────────────────── --}}
    @if($checkpoints->hasPages())
    <div class="flex items-center justify-between p-md border-t border-outline-variant bg-[#F9FAFB]">
        <p class="text-body-sm font-body-sm text-secondary">
            Menampilkan {{ $checkpoints->firstItem() }}–{{ $checkpoints->lastItem() }}
            dari {{ $checkpoints->total() }} checkpoint
        </p>
        <div class="flex items-center gap-xs">
            {{-- Previous --}}
            @if($checkpoints->onFirstPage())
                <span class="p-xs px-sm border border-outline-variant rounded-lg text-secondary opacity-40 text-body-sm cursor-not-allowed">
                    <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                </span>
            @else
                <a href="{{ $checkpoints->previousPageUrl() }}"
                   class="p-xs px-sm border border-outline-variant rounded-lg text-secondary hover:bg-surface-container transition-colors text-body-sm">
                    <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                </a>
            @endif

            {{-- Page Numbers --}}
            @foreach($checkpoints->getUrlRange(max(1, $checkpoints->currentPage() - 2), min($checkpoints->lastPage(), $checkpoints->currentPage() + 2)) as $page => $url)
                @if($page == $checkpoints->currentPage())
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
            @if($checkpoints->hasMorePages())
                <a href="{{ $checkpoints->nextPageUrl() }}"
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
