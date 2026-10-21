<x-app-layout>

{{-- ══ Flash Message ══════════════════════════════════════════════════════ --}}
@if(session('success'))
<div class="mb-md flex items-center gap-sm px-md py-sm bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-body-sm font-medium">
    <span class="material-symbols-outlined text-emerald-600 text-[20px]">check_circle</span>
    {{ session('success') }}
</div>
@endif

{{-- ══ Header Section ══════════════════════════════════════════════════════ --}}
<div class="flex flex-col md:flex-row md:items-end justify-between mb-lg gap-md">
    <div>
        <p class="text-body-md font-body-md text-secondary mb-xs">Guard Management</p>
        <h2 class="text-headline-lg font-headline-lg text-on-surface">Rute &amp; Jadwal Patroli</h2>
    </div>
    <div class="flex gap-sm">
        @can('create', App\Models\PatrolSchedule::class)
        <a href="{{ route('patrol-schedules.create') }}"
           class="bg-primary text-on-primary py-sm px-md rounded-xl hover:bg-surface-tint transition-colors flex items-center gap-xs text-body-sm font-body-sm font-semibold">
            <span class="material-symbols-outlined text-[18px]">add</span> Buat Jadwal Baru
        </a>
        @endcan
    </div>
</div>

{{-- ══ Stats Summary Cards (Total + per shift) ═════════════════════════════ --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-md mb-xl">
    {{-- Total Jadwal --}}
    <div class="soft-card soft-card-hover p-md flex flex-col">
        <p class="text-body-sm font-body-sm text-secondary mb-sm">Total Rute Jadwal</p>
        <p class="text-headline-lg font-headline-lg text-on-surface">{{ $stats['total'] }}</p>
    </div>
    {{-- Jumlah jadwal per shift --}}
    @foreach($shiftStats as $shift)
    <div class="soft-card soft-card-hover p-md flex flex-col">
        <div class="flex items-center gap-xs mb-sm">
            <span class="material-symbols-outlined text-[18px] text-primary">schedule</span>
            <p class="text-body-sm font-body-sm text-secondary truncate">Shift {{ $shift->shift_name }}</p>
        </div>
        <p class="text-headline-lg font-headline-lg text-on-surface">{{ $shift->patrol_schedules_count }}</p>
    </div>
    @endforeach
</div>

{{-- ══ Filter & Search Bar ═════════════════════════════════════════════════ --}}
<form method="GET" action="{{ route('patrol-schedules.index') }}"
      class="soft-card p-md mb-md flex flex-col md:flex-row gap-sm items-stretch md:items-center">
    {{-- Search --}}
    <div class="relative flex-1 max-w-md">
        <span class="material-symbols-outlined absolute left-sm top-1/2 -translate-y-1/2 text-secondary text-[20px]">search</span>
        <input name="search" value="{{ request('search') }}"
               class="w-full bg-surface-container-low border border-outline-variant rounded-xl py-xs pl-[36px] pr-sm text-body-sm font-body-sm focus:border-primary-container focus:ring-2 focus:ring-primary-container/20 transition-all outline-none text-on-surface"
               placeholder="Cari nama jadwal, area..."
               type="text">
    </div>
    {{-- Filter Area --}}
    <select name="area_id"
            class="bg-surface-container-low border border-outline-variant rounded-xl py-xs px-sm text-body-sm font-body-sm text-on-surface outline-none focus:border-primary-container">
        <option value="">Semua Area</option>
        @foreach($areas as $area)
            <option value="{{ $area->id }}" @selected(request('area_id') == $area->id)>
                {{ $area->area_name }}
            </option>
        @endforeach
    </select>
    {{-- Filter Shift (dinamis dari master shift) --}}
    <select name="shift_id"
            class="bg-surface-container-low border border-outline-variant rounded-xl py-xs px-sm text-body-sm font-body-sm text-on-surface outline-none focus:border-primary-container">
        <option value="">Semua Shift</option>
        @foreach($shifts as $shift)
            <option value="{{ $shift->id }}" @selected(request('shift_id') == $shift->id)>
                {{ $shift->shift_name }}
            </option>
        @endforeach
    </select>
    {{-- Tombol Submit & Reset --}}
    <button type="submit"
            class="bg-primary text-on-primary py-xs px-md rounded-xl text-body-sm font-semibold hover:opacity-90 transition-opacity whitespace-nowrap">
        Filter
    </button>
    @if(request()->hasAny(['search','area_id','shift_id']))
    <a href="{{ route('patrol-schedules.index') }}"
       class="py-xs px-md rounded-xl text-body-sm border border-outline-variant text-secondary hover:bg-surface-container transition-colors whitespace-nowrap text-center">
        Reset
    </a>
    @endif
</form>

{{-- ══ Schedule Table ══════════════════════════════════════════════════════ --}}
<div class="soft-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="table-modern w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-outline-variant/60 text-label-md font-label-md text-secondary">
                    <th class="py-sm px-md font-medium uppercase tracking-wider">Nama Jadwal / Rute</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider">Area</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider">Shift</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider">Waktu Patroli</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider">Checkpoints</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-body-sm font-body-sm text-on-surface">

                @forelse($schedules as $schedule)
                <tr class="border-b border-outline-variant/50 {{ $loop->even ? 'bg-surface-container-low/40' : '' }} transition-colors group">

                    {{-- Nama Jadwal --}}
                    <td class="py-sm px-md">
                        <div class="flex flex-col">
                            <span class="font-semibold text-primary">{{ $schedule->schedule_name }}</span>
                            <span class="text-secondary text-label-md">
                                Putaran ke-{{ $schedule->round_number ?? '-' }}
                            </span>
                        </div>
                    </td>

                    {{-- Area --}}
                    <td class="py-sm px-md text-secondary">
                        {{ $schedule->area?->area_name ?? '-' }}
                    </td>

                    {{-- Shift Badge --}}
                    <td class="py-sm px-md">
                        @if($schedule->shift)
                            <span class="inline-flex items-center gap-xs px-2 py-1 bg-primary/10 text-primary text-label-md font-label-md rounded-full border border-primary/20">
                                <span class="material-symbols-outlined text-[14px]">schedule</span> {{ $schedule->shift->shift_name }}
                            </span>
                        @else
                            <span class="text-secondary">-</span>
                        @endif
                    </td>

                    {{-- Waktu Patroli --}}
                    <td class="py-sm px-md text-secondary whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}
                        &ndash;
                        {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                    </td>

                    {{-- Jumlah Checkpoint --}}
                    <td class="py-sm px-md">
                        <div class="flex items-center gap-xs">
                            <span class="inline-block px-2 py-1 bg-surface-container text-on-surface text-label-md font-label-md rounded border border-outline-variant">
                                {{ $schedule->checkpoints->count() }} Titik
                            </span>
                        </div>
                    </td>

                    {{-- Aksi: Detail, Edit & Hapus --}}
                    <td class="py-sm px-md text-right whitespace-nowrap">
                        {{-- Detail --}}
                        <button type="button"
                                onclick="openScheduleDetail({{ $schedule->id }})"
                                class="p-xs text-primary hover:bg-primary/10 rounded-full transition-colors" title="Detail">
                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                        </button>

                        @can('update', $schedule)
                        {{-- Edit --}}
                        <a href="{{ route('patrol-schedules.edit', $schedule) }}"
                           class="inline-flex p-xs text-on-surface-variant hover:bg-surface-container rounded-full transition-colors" title="Edit">
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </a>
                        @endcan

                        @can('delete', $schedule)
                        {{-- Hapus --}}
                        <form method="POST"
                              action="{{ route('patrol-schedules.destroy', $schedule) }}"
                              class="inline"
                              onsubmit="return confirm('Hapus jadwal patroli \'{{ addslashes($schedule->schedule_name) }}\'?\nData akan di-soft delete dan bisa dipulihkan.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="p-xs text-red-500 hover:bg-red-50 rounded-full transition-colors" title="Hapus">
                                <span class="material-symbols-outlined text-[20px]">delete</span>
                            </button>
                        </form>
                        @endcan
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-xl px-md text-center text-secondary">
                        <div class="flex flex-col items-center gap-sm opacity-60">
                            <span class="material-symbols-outlined text-[48px]">calendar_month</span>
                            <p class="text-body-md font-medium">Belum ada jadwal patroli</p>
                            @if(request()->hasAny(['search','area_id','shift_id']))
                                <p class="text-body-sm">Tidak ada hasil untuk filter yang dipilih. <a href="{{ route('patrol-schedules.index') }}" class="text-primary underline">Reset filter</a></p>
                            @else
                                <p class="text-body-sm">Klik "Buat Jadwal Baru" untuk menambahkan jadwal pertama.</p>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse

            </tbody>
        </table>
    </div>

    {{-- ── Pagination ──────────────────────────────────────────────────── --}}
    @if($schedules->hasPages())
    <div class="flex items-center justify-between p-md border-t border-outline-variant bg-surface-container-low">
        <p class="text-body-sm font-body-sm text-secondary">
            Menampilkan {{ $schedules->firstItem() }}–{{ $schedules->lastItem() }}
            dari {{ $schedules->total() }} jadwal
        </p>
        <div class="flex items-center gap-xs">
            {{-- Previous --}}
            @if($schedules->onFirstPage())
                <span class="p-xs px-sm border border-outline-variant rounded-lg text-secondary opacity-40 text-body-sm cursor-not-allowed">
                    <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                </span>
            @else
                <a href="{{ $schedules->previousPageUrl() }}"
                   class="p-xs px-sm border border-outline-variant rounded-lg text-secondary hover:bg-surface-container transition-colors text-body-sm">
                    <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                </a>
            @endif

            {{-- Page Numbers --}}
            @foreach($schedules->getUrlRange(1, $schedules->lastPage()) as $page => $url)
                @if($page === $schedules->currentPage())
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
            @if($schedules->hasMorePages())
                <a href="{{ $schedules->nextPageUrl() }}"
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

{{-- ══ Modal Detail Jadwal ═══════════════════════════════════════════════ --}}
<div id="routeDetailModal"
     class="hidden fixed inset-0 z-50 items-center justify-center bg-black/30 backdrop-blur-md p-gutter"
     onclick="if (event.target === this) closeScheduleDetail()">
    <div class="animate-modal-pop bg-surface-container-lowest border border-outline-variant/60 rounded-3xl shadow-lift max-w-lg w-full max-h-[85vh] flex flex-col">
        <div class="flex items-center justify-between p-md border-b border-outline-variant/60 bg-surface-container-low/60 rounded-t-3xl shrink-0">
            <div>
                <p class="text-label-md font-label-md text-secondary">Detail Jadwal</p>
                <h3 class="text-headline-md font-headline-md text-on-surface" id="modalRouteName">—</h3>
            </div>
            <button type="button" onclick="closeScheduleDetail()"
                    class="p-xs text-on-surface-variant hover:bg-surface-container rounded-full transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-md overflow-y-auto flex-1">
            {{-- Info jadwal --}}
            <div class="grid grid-cols-2 gap-sm mb-md">
                <div class="bg-surface-container-low border border-outline-variant rounded-lg p-sm">
                    <p class="text-label-md text-secondary mb-[2px]">Area</p>
                    <p class="text-body-sm font-semibold text-on-surface" id="modalScheduleArea">—</p>
                </div>
                <div class="bg-surface-container-low border border-outline-variant rounded-lg p-sm">
                    <p class="text-label-md text-secondary mb-[2px]">Shift</p>
                    <p class="text-body-sm font-semibold text-on-surface" id="modalScheduleShift">—</p>
                </div>
                <div class="bg-surface-container-low border border-outline-variant rounded-lg p-sm">
                    <p class="text-label-md text-secondary mb-[2px]">Putaran</p>
                    <p class="text-body-sm font-semibold text-on-surface" id="modalScheduleRound">—</p>
                </div>
                <div class="bg-surface-container-low border border-outline-variant rounded-lg p-sm">
                    <p class="text-label-md text-secondary mb-[2px]">Waktu</p>
                    <p class="text-body-sm font-semibold text-on-surface" id="modalScheduleTime">—</p>
                </div>
                <div class="bg-surface-container-low border border-outline-variant rounded-lg p-sm">
                    <p class="text-label-md text-secondary mb-[2px]">Status</p>
                    <p class="text-body-sm font-semibold text-on-surface" id="modalScheduleStatus">—</p>
                </div>
                <div class="bg-surface-container-low border border-outline-variant rounded-lg p-sm">
                    <p class="text-label-md text-secondary mb-[2px]">Jumlah Checkpoint</p>
                    <p class="text-body-sm font-semibold text-on-surface" id="modalScheduleCount">—</p>
                </div>
            </div>

            <div class="flex items-center gap-sm mb-md text-body-sm text-secondary bg-surface-container-low p-sm rounded-lg">
                <span class="material-symbols-outlined text-[20px]">info</span>
                <p>Urutan checkpoint yang harus dicatat petugas.</p>
            </div>

            <div id="modalCheckpointList" class="space-y-sm"></div>
        </div>
        <div class="p-md border-t border-outline-variant/60 bg-surface-container-low/60 rounded-b-3xl flex justify-end shrink-0">
            <button type="button" onclick="closeScheduleDetail()"
                    class="bg-surface-container border border-outline-variant text-on-surface py-xs px-md rounded-lg hover:bg-surface-container-high transition-colors font-semibold text-body-sm">
                Tutup
            </button>
        </div>
    </div>
</div>

@php
    $scheduleDetails = $schedules->getCollection()->map(function ($schedule) {
        $statusLabels = [
            'scheduled' => 'Terjadwal',
            'ongoing'   => 'Berjalan',
            'completed' => 'Selesai',
            'missed'    => 'Terlewat',
        ];

        return [
            'id'          => $schedule->id,
            'name'        => $schedule->schedule_name,
            'area'        => $schedule->area?->area_name ?? '-',
            'shift'       => $schedule->shift?->shift_name ?? '-',
            'round'       => $schedule->round_number,
            'start_time'  => \Carbon\Carbon::parse($schedule->start_time)->format('H:i'),
            'end_time'    => \Carbon\Carbon::parse($schedule->end_time)->format('H:i'),
            'status'      => $statusLabels[$schedule->status] ?? ucfirst((string) $schedule->status),
            'checkpoints' => $schedule->checkpoints->values()->map(fn ($cp, $index) => [
                'sequence' => $cp->pivot->sequence_number ?? ($index + 1),
                'name'     => $cp->checkpoint_name,
            ])->all(),
        ];
    })->keyBy('id');
@endphp
<script>
    const scheduleDetails = @json($scheduleDetails);

    function openScheduleDetail(id) {
        const detail = scheduleDetails[id];
        if (!detail) return;

        document.getElementById('modalRouteName').textContent = detail.name;
        document.getElementById('modalScheduleArea').textContent = detail.area;
        document.getElementById('modalScheduleShift').textContent = detail.shift;
        document.getElementById('modalScheduleRound').textContent = detail.round ? `Putaran ke-${detail.round}` : '-';
        document.getElementById('modalScheduleTime').textContent = `${detail.start_time} – ${detail.end_time}`;
        document.getElementById('modalScheduleStatus').textContent = detail.status;
        document.getElementById('modalScheduleCount').textContent = `${detail.checkpoints.length} Titik`;

        const list = document.getElementById('modalCheckpointList');
        list.replaceChildren();

        if (detail.checkpoints.length === 0) {
            const empty = document.createElement('p');
            empty.className = 'text-body-sm text-secondary italic';
            empty.textContent = 'Belum ada checkpoint pada rute ini.';
            list.appendChild(empty);
        } else {
            detail.checkpoints.forEach((checkpoint) => {
                const item = document.createElement('div');
                item.className = 'flex items-center gap-sm';

                const badge = document.createElement('span');
                badge.className = 'flex items-center justify-center w-6 h-6 rounded-full bg-primary text-on-primary text-[11px] font-bold flex-shrink-0';
                badge.textContent = checkpoint.sequence;

                const name = document.createElement('p');
                name.className = 'text-body-sm font-medium text-on-surface';
                name.textContent = checkpoint.name;

                item.appendChild(badge);
                item.appendChild(name);
                list.appendChild(item);
            });
        }

        const modal = document.getElementById('routeDetailModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeScheduleDetail() {
        const modal = document.getElementById('routeDetailModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeScheduleDetail();
    });
</script>

</x-app-layout>
