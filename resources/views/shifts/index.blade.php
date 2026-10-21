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

{{-- ══ Header Section ══════════════════════════════════════════════════════ --}}
<div class="flex flex-col md:flex-row md:items-end justify-between mb-lg gap-md">
    <div>
        <p class="text-body-md font-body-md text-secondary mb-xs">Data Master</p>
        <h2 class="text-headline-lg font-headline-lg text-on-surface">Data Master Shift</h2>
    </div>
    @can('create', App\Models\Shift::class)
    <div class="flex gap-sm">
        <a href="{{ route('shifts.create') }}"
           class="bg-primary text-on-primary py-sm px-md rounded-xl hover:bg-surface-tint transition-colors flex items-center gap-xs text-body-sm font-body-sm font-semibold shadow-sm">
            <span class="material-symbols-outlined text-[18px]">add_alarm</span> Tambah Shift Baru
        </a>
    </div>
    @endcan
</div>

{{-- ══ Stats Summary Cards ═════════════════════════════════════════════════ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-md mb-xl">
    <div class="soft-card soft-card-hover p-md flex flex-col">
        <div class="flex items-center gap-xs mb-sm">
            <span class="material-symbols-outlined text-[18px] text-primary">schedule</span>
            <p class="text-body-sm font-body-sm text-secondary">Total Shift</p>
        </div>
        <p class="text-headline-lg font-headline-lg text-on-surface">{{ $stats['total'] }}</p>
    </div>
    <div class="soft-card soft-card-hover p-md flex flex-col">
        <div class="flex items-center gap-xs mb-sm">
            <span class="material-symbols-outlined text-[18px] text-secondary">update</span>
            <p class="text-body-sm font-body-sm text-secondary">Durasi Rata-rata</p>
        </div>
        <p class="text-headline-lg font-headline-lg text-on-surface">
            {{ $stats['avg_duration'] }} <span class="text-title-md font-normal text-secondary">Jam</span>
        </p>
    </div>
    <div class="soft-card soft-card-hover p-md flex flex-col">
        <div class="flex items-center gap-xs mb-sm">
            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
            <p class="text-body-sm font-body-sm text-secondary">Shift Aktif</p>
        </div>
        <p class="text-headline-lg font-headline-lg text-on-surface">{{ $stats['active'] }}</p>
    </div>
    <div class="soft-card soft-card-hover p-md flex flex-col">
        <div class="flex items-center gap-xs mb-sm">
            <div class="w-2 h-2 rounded-full bg-slate-400"></div>
            <p class="text-body-sm font-body-sm text-secondary">Nonaktif</p>
        </div>
        <p class="text-headline-lg font-headline-lg text-on-surface">{{ $stats['inactive'] }}</p>
    </div>
</div>

{{-- ══ Filter & Search Bar ═════════════════════════════════════════════════ --}}
<form method="GET" action="{{ route('shifts.index') }}"
      class="soft-card soft-card-hover p-md mb-md flex flex-col md:flex-row gap-sm items-stretch md:items-center">
    <div class="relative flex-1 max-w-md">
        <span class="material-symbols-outlined absolute left-sm top-1/2 -translate-y-1/2 text-secondary text-[20px]">search</span>
        <input name="search" value="{{ request('search') }}"
               class="w-full bg-surface-container-low border border-outline-variant rounded-xl py-xs pl-[36px] pr-sm text-body-sm font-body-sm focus:border-primary-container focus:ring-2 focus:ring-primary-container/20 transition-all outline-none text-on-surface"
               placeholder="Cari nama atau kode shift..."
               type="text">
    </div>

    <div class="w-full md:w-44">
        <select name="status"
                class="w-full bg-surface-container-low border border-outline-variant rounded-xl py-xs px-sm text-body-sm text-on-surface outline-none focus:border-primary focus:ring-2 focus:ring-primary/10">
            <option value="">Semua Status</option>
            <option value="active" @selected(request('status') === 'active')>Aktif</option>
            <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
        </select>
    </div>

    <button type="submit"
            class="bg-primary text-on-primary py-xs px-md rounded-xl text-body-sm font-semibold hover:opacity-90 transition-opacity whitespace-nowrap">
        Filter
    </button>
    @if(request()->hasAny(['search', 'status']))
    <a href="{{ route('shifts.index') }}"
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
                    <th class="py-sm px-md font-medium uppercase tracking-wider">Nama Shift</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider">Jam Mulai</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider">Jam Selesai</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider">Durasi</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider text-center">Status</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider text-center">Jadwal</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-body-sm font-body-sm text-on-surface">

                @forelse($shifts as $shift)
                <tr class="border-b border-outline-variant {{ $loop->even ? 'bg-surface-container-low' : '' }} hover:bg-surface-container-low transition-colors group">

                    {{-- Nama Shift --}}
                    <td class="py-sm px-md">
                        <div class="flex items-center gap-sm">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-primary/10 text-primary border border-primary/20">
                                <span class="material-symbols-outlined text-[16px]">schedule</span>
                            </span>
                            <div class="flex flex-col">
                                <span class="font-semibold text-primary">{{ $shift->shift_name }}</span>
                                <span class="text-secondary text-label-md font-label-md font-mono">{{ $shift->shift_code ?? '-' }}</span>
                            </div>
                        </div>
                    </td>

                    {{-- Jam Mulai --}}
                    <td class="py-sm px-md font-semibold text-on-surface whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}
                    </td>

                    {{-- Jam Selesai --}}
                    <td class="py-sm px-md font-semibold text-on-surface whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}
                        @if($shift->isOvernight())
                            <span class="ml-1 inline-flex items-center gap-[2px] px-1.5 py-0.5 bg-blue-50 text-blue-700 text-[10px] font-bold rounded border border-blue-200 align-middle" title="Melewati tengah malam">
                                +1 hari
                            </span>
                        @endif
                    </td>

                    {{-- Durasi --}}
                    <td class="py-sm px-md">
                        <span class="inline-block px-2 py-1 bg-surface-container-high text-on-surface text-label-md font-bold rounded border border-outline-variant">
                            {{ rtrim(rtrim(number_format($shift->duration_hours, 1, ',', ''), '0'), ',') }} Jam
                        </span>
                    </td>

                    {{-- Status --}}
                    <td class="py-sm px-md text-center">
                        @if($shift->status === 'active')
                            <span class="inline-flex items-center justify-center px-2 py-1 bg-emerald-50 text-emerald-700 text-[11px] uppercase tracking-wider rounded-full border border-emerald-200 font-bold">
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center justify-center px-2 py-1 bg-surface-container-high text-secondary text-[11px] uppercase tracking-wider rounded-full border border-outline-variant font-bold">
                                Nonaktif
                            </span>
                        @endif
                    </td>

                    {{-- Jumlah Jadwal --}}
                    <td class="py-sm px-md text-center">
                        @if($shift->patrol_schedules_count > 0)
                            <span class="inline-flex items-center justify-center px-2 py-1 bg-blue-50 text-blue-700 text-label-md rounded-full border border-blue-200 font-bold min-w-[30px]">
                                {{ $shift->patrol_schedules_count }}
                            </span>
                        @else
                            <span class="text-secondary">-</span>
                        @endif
                    </td>

                    {{-- Aksi --}}
                    <td class="py-sm px-md text-right whitespace-nowrap">
                        @can('update', $shift)
                        <a href="{{ route('shifts.edit', $shift) }}"
                           class="inline-flex p-xs text-on-surface-variant hover:bg-surface-container rounded-full transition-colors" title="Edit">
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </a>

                        <form method="POST" action="{{ route('shifts.toggle-status', $shift) }}" class="inline"
                              title="{{ $shift->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="p-xs {{ $shift->status === 'active' ? 'text-amber-600 hover:bg-amber-50' : 'text-emerald-600 hover:bg-emerald-50' }} rounded-full transition-colors">
                                <span class="material-symbols-outlined text-[20px]">{{ $shift->status === 'active' ? 'toggle_on' : 'toggle_off' }}</span>
                            </button>
                        </form>
                        @endcan

                        @can('delete', $shift)
                            @if($shift->patrol_schedules_count > 0)
                                <span class="inline-flex p-xs text-secondary/50 cursor-not-allowed"
                                      title="Tidak dapat dihapus: masih dipakai {{ $shift->patrol_schedules_count }} jadwal">
                                    <span class="material-symbols-outlined text-[20px]">lock</span>
                                </span>
                            @else
                                <form method="POST" action="{{ route('shifts.destroy', $shift) }}" class="inline"
                                      onsubmit="return confirm('Hapus shift \'{{ addslashes($shift->shift_name) }}\'?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="p-xs text-red-500 hover:bg-red-50 rounded-full transition-colors" title="Hapus">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </form>
                            @endif
                        @endcan
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-xl px-md text-center text-secondary">
                        <div class="flex flex-col items-center gap-sm opacity-60">
                            <span class="material-symbols-outlined text-[48px]">schedule</span>
                            <p class="text-body-md font-medium">Belum ada data shift</p>
                            @if(request()->hasAny(['search', 'status']))
                                <p class="text-body-sm">Tidak ada hasil untuk filter yang dipilih. <a href="{{ route('shifts.index') }}" class="text-primary underline">Reset filter</a></p>
                            @else
                                <p class="text-body-sm">Klik "Tambah Shift Baru" untuk membuat shift pertama.</p>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse

            </tbody>
        </table>
    </div>

    {{-- ── Pagination ──────────────────────────────────────────────────── --}}
    @if($shifts->hasPages())
    <div class="flex items-center justify-between p-md border-t border-outline-variant bg-surface-container-low">
        <p class="text-body-sm font-body-sm text-secondary">
            Menampilkan {{ $shifts->firstItem() }}–{{ $shifts->lastItem() }}
            dari {{ $shifts->total() }} shift
        </p>
        <div class="flex items-center gap-xs">
            @if($shifts->onFirstPage())
                <span class="p-xs px-sm border border-outline-variant rounded-lg text-secondary opacity-40 text-body-sm cursor-not-allowed">
                    <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                </span>
            @else
                <a href="{{ $shifts->previousPageUrl() }}"
                   class="p-xs px-sm border border-outline-variant rounded-lg text-secondary hover:bg-surface-container transition-colors text-body-sm">
                    <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                </a>
            @endif

            @foreach($shifts->getUrlRange(max(1, $shifts->currentPage() - 2), min($shifts->lastPage(), $shifts->currentPage() + 2)) as $page => $url)
                @if($page == $shifts->currentPage())
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

            @if($shifts->hasMorePages())
                <a href="{{ $shifts->nextPageUrl() }}"
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
