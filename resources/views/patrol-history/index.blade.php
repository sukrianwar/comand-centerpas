<x-app-layout>

@php
    $statusMeta = [
        'on_time' => ['label' => 'Tepat Waktu', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'icon' => 'check_circle'],
        'late' => ['label' => 'Terlambat', 'class' => 'bg-amber-50 text-amber-700 border-amber-300', 'icon' => 'warning'],
    ];

    $exportQuery = request()->only([
        'date_from', 'date_to', 'user_id', 'area_id',
        'patrol_schedule_id', 'status', 'search',
    ]);
@endphp

<div x-data="patrolHistoryPage()">

    {{-- Header --}}
    <div class="mb-lg flex flex-col gap-md md:flex-row md:items-end md:justify-between">
        <div>
            <p class="mb-xs text-body-md font-body-md text-secondary">Guard Management</p>
            <h2 class="text-headline-lg font-headline-lg text-on-surface">Riwayat Patroli</h2>
            <p class="mt-xs text-body-sm text-secondary">Monitoring seluruh pencatatan checkpoint oleh petugas.</p>
        </div>
        <div class="flex flex-wrap items-center gap-sm">
            @if($stats['total'] > 0)
                <a href="{{ route('patrol-history.export', $exportQuery) }}"
                   class="inline-flex items-center gap-xs rounded-xl bg-emerald-600 px-md py-xs text-body-sm font-bold text-white shadow-sm transition-colors hover:bg-emerald-700">
                    <span class="material-symbols-outlined text-[18px]">download</span>
                    Export Excel
                </a>
            @else
                <span class="inline-flex cursor-not-allowed items-center gap-xs rounded-xl bg-surface-container-high px-md py-xs text-body-sm font-bold text-secondary opacity-60">
                    <span class="material-symbols-outlined text-[18px]">download</span>
                    Export Excel
                </span>
            @endif
            <button type="button" onclick="location.reload()"
                    class="inline-flex items-center gap-xs rounded-xl bg-primary px-md py-xs text-body-sm font-bold text-on-primary shadow-sm transition-colors hover:bg-surface-tint">
                <span class="material-symbols-outlined text-[18px]">refresh</span>
                Refresh
            </button>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="mb-lg grid grid-cols-2 gap-md lg:grid-cols-4">
        <div class="soft-card p-md">
            <div class="mb-sm flex items-center justify-between">
                <p class="text-body-sm text-secondary">Total Scan</p>
                <span class="material-symbols-outlined text-[20px] text-primary">fact_check</span>
            </div>
            <p class="text-headline-lg font-bold text-on-surface">{{ $stats['total'] }}</p>
        </div>
        <div class="soft-card p-md">
            <div class="mb-sm flex items-center justify-between">
                <p class="text-body-sm text-secondary">Petugas Bertugas</p>
                <span class="material-symbols-outlined text-[20px] text-blue-600">group</span>
            </div>
            <p class="text-headline-lg font-bold text-on-surface">{{ $stats['guards'] }}</p>
        </div>
        <div class="soft-card border-l-4 border-l-emerald-400 p-md">
            <div class="mb-sm flex items-center justify-between">
                <p class="text-body-sm text-secondary">Tepat Waktu</p>
                <span class="material-symbols-outlined text-[20px] text-emerald-600">check_circle</span>
            </div>
            <p class="text-headline-lg font-bold text-emerald-600">{{ $stats['on_time'] }}</p>
        </div>
        <div class="soft-card border-l-4 border-l-amber-400 p-md">
            <div class="mb-sm flex items-center justify-between">
                <p class="text-body-sm text-secondary">Terlambat</p>
                <span class="material-symbols-outlined text-[20px] text-amber-600">warning</span>
            </div>
            <p class="text-headline-lg font-bold text-amber-600">{{ $stats['late'] }}</p>
        </div>
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('patrol-history.index') }}"
          class="soft-card mb-md p-md">
        <div class="grid grid-cols-1 gap-sm md:grid-cols-2 xl:grid-cols-12">
            <div class="relative xl:col-span-4">
                <span class="material-symbols-outlined absolute left-sm top-1/2 -translate-y-1/2 text-[18px] text-secondary">search</span>
                <input type="text" name="search" value="{{ $filters['search'] }}"
                       placeholder="Cari petugas, checkpoint, rute..."
                       class="w-full rounded-xl border border-outline-variant bg-surface-container-low py-xs pl-[36px] pr-sm text-body-sm text-on-surface outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/10">
            </div>

            <div class="xl:col-span-2">
                <input type="date" name="date_from" value="{{ $filters['date_from'] }}"
                       aria-label="Tanggal mulai"
                       class="w-full rounded-xl border border-outline-variant bg-surface-container-low px-sm py-xs text-body-sm text-on-surface outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/10">
            </div>

            <div class="xl:col-span-2">
                <input type="date" name="date_to" value="{{ $filters['date_to'] }}"
                       aria-label="Tanggal akhir"
                       class="w-full rounded-xl border border-outline-variant bg-surface-container-low px-sm py-xs text-body-sm text-on-surface outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/10">
            </div>

            <div class="xl:col-span-2">
                <select name="user_id"
                        class="w-full rounded-xl border border-outline-variant bg-surface-container-low px-sm py-xs text-body-sm text-on-surface outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/10">
                    <option value="">Semua Petugas</option>
                    @foreach($guards as $guard)
                        <option value="{{ $guard->id }}" @selected((string) $filters['user_id'] === (string) $guard->id)>
                            {{ $guard->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="xl:col-span-2">
                <select name="status"
                        class="w-full rounded-xl border border-outline-variant bg-surface-container-low px-sm py-xs text-body-sm text-on-surface outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/10">
                    <option value="">Status</option>
                    <option value="on_time" @selected($filters['status'] === 'on_time')>Tepat</option>
                    <option value="late" @selected($filters['status'] === 'late')>Terlambat</option>
                </select>
            </div>

            <div class="xl:col-span-4">
                <select name="area_id"
                        class="w-full rounded-xl border border-outline-variant bg-surface-container-low px-sm py-xs text-body-sm text-on-surface outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/10">
                    <option value="">Semua Area</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" @selected((string) $filters['area_id'] === (string) $area->id)>
                            {{ $area->area_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="xl:col-span-5">
                <select name="patrol_schedule_id"
                        class="w-full rounded-xl border border-outline-variant bg-surface-container-low px-sm py-xs text-body-sm text-on-surface outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/10">
                    <option value="">Semua Rute</option>
                    @foreach($schedules as $schedule)
                        <option value="{{ $schedule->id }}" @selected((string) $filters['patrol_schedule_id'] === (string) $schedule->id)>
                            {{ $schedule->schedule_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-sm xl:col-span-3">
                <button type="submit"
                        class="rounded-xl bg-primary px-md py-xs text-body-sm font-bold text-on-primary transition-colors hover:bg-surface-tint">
                    Filter
                </button>
                @if(request()->hasAny(['date_from', 'date_to', 'user_id', 'area_id', 'patrol_schedule_id', 'status', 'search']))
                    <a href="{{ route('patrol-history.index') }}"
                       class="rounded-xl border border-outline-variant px-md py-xs text-body-sm font-semibold text-secondary transition-colors hover:bg-surface-container">
                        Reset
                    </a>
                @endif
            </div>
        </div>
        <p class="mt-sm text-[11px] text-secondary">
            Default menampilkan hari ini. Kosongkan tanggal lalu klik Filter untuk melihat seluruh riwayat.
        </p>
    </form>

    {{-- Desktop table --}}
    <div class="soft-card hidden overflow-hidden md:block">
        <div class="overflow-x-auto">
            <table class="table-modern w-full border-collapse text-left">
                <thead>
                    <tr class="border-b border-outline-variant/60 text-label-md font-label-md text-secondary">
                        <th class="px-md py-sm font-medium uppercase tracking-wider">Waktu</th>
                        <th class="px-md py-sm font-medium uppercase tracking-wider">Petugas</th>
                        <th class="px-md py-sm font-medium uppercase tracking-wider">Checkpoint</th>
                        <th class="px-md py-sm font-medium uppercase tracking-wider">Rute &amp; Area</th>
                        <th class="px-md py-sm font-medium uppercase tracking-wider">Shift</th>
                        <th class="px-md py-sm font-medium uppercase tracking-wider">Status</th>
                        <th class="px-md py-sm text-right font-medium uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-body-sm text-on-surface">
                    @forelse($logs as $log)
                        @php($status = $statusMeta[$log->status] ?? $statusMeta['on_time'])
                        <tr class="border-b border-outline-variant transition-colors hover:bg-surface-container-low {{ $loop->even ? 'bg-surface-container-low/50' : '' }}">
                            <td class="whitespace-nowrap px-md py-sm">
                                <p class="font-semibold">{{ $log->scanned_at?->format('d M Y') }}</p>
                                <p class="text-[11px] text-secondary">{{ $log->scanned_at?->format('H:i') }} WIB</p>
                            </td>
                            <td class="px-md py-sm">
                                <div class="flex items-center gap-sm">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-container text-[10px] font-bold text-on-primary-container">
                                        {{ strtoupper(substr($log->user?->name ?? '?', 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold">{{ $log->user?->name ?? '-' }}</p>
                                        <p class="truncate text-[11px] text-secondary">{{ $log->user?->username ? '@'.$log->user->username : '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-md py-sm">{{ $log->checkpoint?->checkpoint_name ?? '-' }}</td>
                            <td class="px-md py-sm">
                                <p class="font-medium">{{ $log->patrolSchedule?->schedule_name ?? '-' }}</p>
                                <p class="text-[11px] text-secondary">{{ $log->patrolSchedule?->area?->area_name ?? '-' }}</p>
                            </td>
                            <td class="px-md py-sm text-secondary">{{ $log->patrolSchedule?->shiftMaster?->shift_name ?? '-' }}</td>
                            <td class="px-md py-sm">
                                <span class="inline-flex items-center gap-xs rounded-full border px-2 py-1 text-[10px] font-bold {{ $status['class'] }}">
                                    <span class="material-symbols-outlined text-[13px]">{{ $status['icon'] }}</span>
                                    {{ $status['label'] }}
                                </span>
                            </td>
                            <td class="px-md py-sm text-right">
                                <button type="button" @click="openDetail({{ $log->id }})"
                                        class="rounded-full p-xs text-on-surface-variant transition-colors hover:bg-surface-container"
                                        title="Lihat detail">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-md py-xl text-center text-secondary">
                                <span class="material-symbols-outlined mb-sm block text-[48px] opacity-50">history</span>
                                <p class="text-body-md font-medium">Belum ada riwayat patroli pada filter ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Mobile cards --}}
    <div class="space-y-sm md:hidden">
        @forelse($logs as $log)
            @php($status = $statusMeta[$log->status] ?? $statusMeta['on_time'])
            <div class="soft-card p-md">
                <div class="mb-sm flex items-start justify-between gap-sm">
                    <div>
                        <p class="text-[11px] font-bold text-secondary">{{ $log->scanned_at?->format('d M Y, H:i') }} WIB</p>
                        <h4 class="text-title-md font-bold text-on-surface">{{ $log->checkpoint?->checkpoint_name ?? '-' }}</h4>
                    </div>
                    <span class="inline-flex flex-shrink-0 items-center gap-xs rounded-full border px-2 py-1 text-[10px] font-bold {{ $status['class'] }}">
                        {{ $status['label'] }}
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-sm rounded-xl border border-outline-variant bg-surface-container-lowest p-sm text-[12px]">
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-secondary">Petugas</p>
                        <p class="font-semibold">{{ $log->user?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-secondary">Shift</p>
                        <p class="font-semibold">{{ $log->patrolSchedule?->shiftMaster?->shift_name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-secondary">Rute</p>
                        <p class="font-semibold">{{ $log->patrolSchedule?->schedule_name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-secondary">Area</p>
                        <p class="font-semibold">{{ $log->patrolSchedule?->area?->area_name ?? '-' }}</p>
                    </div>
                </div>
                <button type="button" @click="openDetail({{ $log->id }})"
                        class="mt-sm inline-flex items-center gap-xs text-[12px] font-bold text-primary">
                    <span class="material-symbols-outlined text-[16px]">visibility</span>
                    Lihat detail
                </button>
            </div>
        @empty
            <div class="soft-card p-xl text-center text-secondary">
                <span class="material-symbols-outlined mb-sm block text-[48px] opacity-50">history</span>
                <p class="text-body-md font-medium">Belum ada riwayat patroli pada filter ini.</p>
            </div>
        @endforelse
    </div>

    @if($logs->hasPages())
        <div class="mt-lg">
            {{ $logs->links() }}
        </div>
    @endif

    {{-- Modal detail --}}
    <div x-show="detail !== null" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-gutter backdrop-blur-sm"
         @keydown.escape.window="detail = null"
         @click.self="detail = null"
         style="display: none;">
        <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-surface-container-lowest shadow-xl">
            <div class="sticky top-0 flex items-start justify-between gap-sm border-b border-outline-variant bg-surface-container-low p-md">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-widest text-secondary">Detail Patroli</p>
                    <h3 class="text-headline-md font-bold text-on-surface" x-text="detail ? detail.checkpoint : ''"></h3>
                </div>
                <button type="button" @click="detail = null"
                        class="rounded-full p-xs text-on-surface-variant transition-colors hover:bg-surface-container"
                        aria-label="Tutup detail">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="space-y-md p-md" x-show="detail">
                <div class="flex flex-wrap items-center gap-sm">
                    <span class="inline-flex items-center gap-xs rounded-full border px-2 py-1 text-[11px] font-bold"
                          :class="detail && detail.status === 'Terlambat'
                              ? 'bg-amber-50 text-amber-700 border-amber-300'
                              : 'bg-emerald-50 text-emerald-700 border-emerald-200'">
                        <span class="material-symbols-outlined text-[14px]"
                              x-text="detail && detail.status === 'Terlambat' ? 'warning' : 'check_circle'"></span>
                        <span x-text="detail ? detail.status : ''"></span>
                    </span>
                    <span class="text-body-sm text-secondary" x-text="detail ? detail.scanned_at + ' WIB' : ''"></span>
                </div>

                <div class="grid grid-cols-1 gap-md sm:grid-cols-2">
                    <div>
                        <p class="mb-xs text-[10px] font-bold uppercase tracking-wider text-secondary">Petugas</p>
                        <p class="text-body-sm font-semibold text-on-surface" x-text="detail ? detail.guard : ''"></p>
                        <p class="text-[11px] text-secondary" x-text="detail && detail.guard_username ? '@' + detail.guard_username : ''"></p>
                    </div>
                    <div>
                        <p class="mb-xs text-[10px] font-bold uppercase tracking-wider text-secondary">Rute / Area</p>
                        <p class="text-body-sm font-semibold text-on-surface" x-text="detail ? detail.schedule : ''"></p>
                        <p class="text-[11px] text-secondary" x-text="detail ? detail.area : ''"></p>
                    </div>
                    <div>
                        <p class="mb-xs text-[10px] font-bold uppercase tracking-wider text-secondary">Shift</p>
                        <p class="text-body-sm font-semibold text-on-surface" x-text="detail ? detail.shift : ''"></p>
                    </div>
                    <div>
                        <p class="mb-xs text-[10px] font-bold uppercase tracking-wider text-secondary">Catatan</p>
                        <p class="text-body-sm text-on-surface" x-text="detail && detail.notes ? detail.notes : 'Tidak ada catatan.'"></p>
                    </div>
                </div>

                <div x-show="detail && detail.photo_urls && detail.photo_urls.length">
                    <p class="mb-xs text-[10px] font-bold uppercase tracking-wider text-secondary">Foto Bukti</p>
                    <div class="grid grid-cols-2 gap-sm sm:grid-cols-3">
                        <template x-for="(url, index) in (detail ? detail.photo_urls : [])" :key="index">
                            <a :href="url" target="_blank" rel="noopener">
                                <img :src="url" alt="Foto bukti patroli"
                                     class="h-40 w-full rounded-xl border border-outline-variant bg-surface-container object-cover">
                            </a>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function patrolHistoryPage() {
    return {
        logs: @json($logsJson),
        detail: null,

        openDetail(id) {
            const item = this.logs.find((log) => log.id === id);

            if (item) {
                this.detail = item;
            }
        },
    };
}
</script>

</x-app-layout>
