<x-app-layout>

<!-- Header Section -->
<div class="flex flex-col md:flex-row md:items-end justify-between mb-xl gap-md">
    <div>
        <p class="text-label-md font-label-md text-secondary uppercase tracking-widest mb-xs flex items-center gap-xs">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            Live Monitoring
        </p>
        <h2 class="text-headline-lg font-headline-lg text-on-surface">
            Selamat Pagi, {{ Auth::user()?->name ?? 'SPV Security' }}
        </h2>
        <p class="text-body-md text-secondary mt-xs">{{ now()->translatedFormat('l, d F Y') }} &bull; {{ $shiftLabel }}</p>
    </div>

    <div class="flex items-center gap-sm">
        <div class="hidden sm:flex items-center gap-xs px-3 py-2 bg-surface-container-lowest border border-outline-variant rounded-full text-body-sm text-secondary">
            <span class="material-symbols-outlined text-[18px]">group</span>
            <span><span class="font-bold text-on-surface">{{ $activeGuards }}</span> Petugas Aktif</span>
        </div>
        <button onclick="location.reload()" class="flex items-center gap-xs px-4 py-2 bg-primary text-on-primary rounded-full text-body-sm font-bold hover:opacity-90 transition-opacity">
            <span class="material-symbols-outlined text-[18px]">refresh</span>
            Refresh Data
        </button>
    </div>
</div>

<!-- Stats Overview Bento Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-md mb-xl">

    <!-- Stat 1: Total Rute -->
    <div class="soft-card soft-card-hover p-md flex flex-col justify-between">
        <div class="flex justify-between items-start mb-md">
            <p class="text-body-sm font-body-sm text-secondary">Total Rute</p>
            <div class="bg-surface-container p-xs rounded-full">
                <span class="material-symbols-outlined text-secondary text-[20px]">route</span>
            </div>
        </div>
        <div class="flex items-end justify-between">
            <p class="text-headline-lg font-headline-lg text-on-surface">{{ $totalRoutes }}</p>
            <p class="text-label-md font-label-md text-secondary mb-1">rute hari ini</p>
        </div>
    </div>

    <!-- Stat 2: Selesai Tepat Waktu -->
    <div class="soft-card soft-card-hover p-md flex flex-col justify-between">
        <div class="flex justify-between items-start mb-md">
            <p class="text-body-sm font-body-sm text-secondary">Selesai Tepat Waktu</p>
            <div class="bg-emerald-50 p-xs rounded-full">
                <span class="material-symbols-outlined text-emerald-600 text-[20px]">check_circle</span>
            </div>
        </div>
        <div class="flex items-end justify-between">
            <p class="text-headline-lg font-headline-lg text-on-surface">{{ $completedOnTime }}</p>
            <p class="text-label-md font-label-md text-emerald-600 font-bold mb-1">{{ $completionPercentage }}% dari total checkpoint</p>
        </div>
    </div>

    <!-- Stat 3: Titik Terlewat -->
    <div class="soft-card soft-card-hover border-l-4 border-l-amber-400 p-md flex flex-col justify-between">
        <div class="flex justify-between items-start mb-md">
            <p class="text-body-sm font-body-sm text-secondary">Titik Terlewat</p>
            <div class="bg-amber-50 p-xs rounded-full">
                <span class="material-symbols-outlined text-amber-600 text-[20px]">warning</span>
            </div>
        </div>
        <div class="flex items-end justify-between">
            <p class="text-headline-lg font-headline-lg text-amber-600">{{ $missedCheckpoints }}</p>
            <p class="text-label-md font-label-md text-secondary mb-1">perlu ditinjau</p>
        </div>
    </div>

    <!-- Stat 4: Insiden Hari Ini (accent card) -->
    <div class="bg-primary-container rounded-2xl p-md flex flex-col justify-between text-white shadow-sm relative overflow-hidden group">
        <div class="absolute -right-6 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-500">
            <span class="material-symbols-outlined text-[110px]">local_police</span>
        </div>
        <div class="flex justify-between items-start mb-md relative z-10">
            <p class="text-body-sm font-body-sm opacity-90 text-white">Insiden Hari Ini</p>
            <div class="bg-white/20 p-xs rounded-full">
                <span class="material-symbols-outlined text-white text-[20px]">local_police</span>
            </div>
        </div>
        <div class="flex items-end justify-between relative z-10">
            <p class="text-headline-lg font-headline-lg font-bold">{{ $incidentsToday }}</p>
            <p class="text-label-md font-label-md opacity-80 mb-1">laporan hari ini</p>
        </div>
    </div>
</div>

@php
    $totalTrendScans = array_sum(array_column($trend, 'scans'));
    $totalTrendIncidents = array_sum(array_column($trend, 'incidents'));
    $totalCompletedDays = array_sum(array_column($dailyCompletion, 'completed'));
    $totalIncompleteDays = array_sum(array_column($dailyCompletion, 'incomplete'));
@endphp

<!-- Analytics Charts -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-md mb-xl">

    <!-- Chart 1: Tren Patroli vs Insiden -->
    <div class="soft-card p-md flex flex-col">
        <div class="flex items-start justify-between gap-sm mb-md">
            <div>
                <h3 class="text-headline-md font-headline-md text-on-surface">Tren Patroli vs Insiden</h3>
                <p class="text-body-sm font-body-sm text-secondary mt-1">7 hari terakhir</p>
            </div>
            <div class="flex items-center gap-md text-right flex-shrink-0">
                <div>
                    <p class="text-title-lg font-bold text-primary">{{ $totalTrendScans }}</p>
                    <p class="text-label-md text-secondary">Scan</p>
                </div>
                <div>
                    <p class="text-title-lg font-bold text-red-600">{{ $totalTrendIncidents }}</p>
                    <p class="text-label-md text-secondary">Insiden</p>
                </div>
            </div>
        </div>

        @if($totalTrendScans + $totalTrendIncidents > 0)
            <div class="relative h-[300px]">
                <canvas id="patrol-incident-chart" role="img"
                        aria-label="Grafik tren jumlah scan patroli dan insiden selama 7 hari terakhir"></canvas>
            </div>
        @else
            <div class="flex flex-1 flex-col items-center justify-center py-xl text-center">
                <span class="material-symbols-outlined text-[32px] text-secondary mb-xs">monitoring</span>
                <p class="text-body-sm font-semibold text-on-surface mb-1">Belum ada data 7 hari terakhir</p>
                <p class="text-body-sm text-secondary">Aktivitas scan dan insiden akan tampil di sini.</p>
            </div>
        @endif
    </div>

    <!-- Chart 2: Rute Selesai 100% vs Belum -->
    <div class="soft-card p-md flex flex-col">
        <div class="flex items-start justify-between gap-sm mb-md">
            <div>
                <h3 class="text-headline-md font-headline-md text-on-surface">Rute Selesai 100% vs Belum</h3>
                <p class="text-body-sm font-body-sm text-secondary mt-1">7 hari terakhir</p>
            </div>
            <div class="flex items-center gap-md text-right flex-shrink-0">
                <div>
                    <p class="text-title-lg font-bold text-emerald-600">{{ $totalCompletedDays }}</p>
                    <p class="text-label-md text-secondary">Selesai</p>
                </div>
                <div>
                    <p class="text-title-lg font-bold text-amber-600">{{ $totalIncompleteDays }}</p>
                    <p class="text-label-md text-secondary">Belum</p>
                </div>
            </div>
        </div>

        @if($totalCompletedDays + $totalIncompleteDays > 0)
            <div class="relative h-[300px]">
                <canvas id="route-completion-chart" role="img"
                        aria-label="Grafik jumlah rute yang selesai 100% dan belum selesai per hari"></canvas>
            </div>
        @else
            <div class="flex flex-1 flex-col items-center justify-center py-xl text-center">
                <span class="material-symbols-outlined text-[32px] text-secondary mb-xs">route</span>
                <p class="text-body-sm font-semibold text-on-surface mb-1">Belum ada rute terjadwal</p>
                <p class="text-body-sm text-secondary">Penyelesaian rute akan tampil di sini.</p>
            </div>
        @endif
    </div>
</div>

<script type="application/json" id="cc-trend-data">@json($trend)</script>
<script type="application/json" id="cc-completion-data">@json($dailyCompletion)</script>

<!-- Main Content Two Columns -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-md">

    <!-- Left Column (Wide) -->
    <div class="lg:col-span-2 flex flex-col gap-md">

        <!-- Log Patroli Real-time -->
        <div class="soft-card overflow-hidden flex flex-col">
            <div class="p-md border-b border-outline-variant flex justify-between items-center bg-surface-container-low">
                <div>
                    <h3 class="text-headline-md font-headline-md text-on-surface flex items-center gap-sm">
                        Log Patroli Real-time
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                    </h3>
                    <p class="text-body-sm text-secondary mt-1">Aktivitas checkpoint terbaru dari seluruh petugas</p>
                </div>
                @can('viewAny', \App\Models\PatrolLog::class)
                <a href="{{ route('patrol-history.index') }}" class="text-primary text-body-sm font-bold hover:underline flex items-center gap-xs whitespace-nowrap">
                    Lihat Semua <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </a>
                @endcan
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-outline-variant text-label-md font-label-md text-secondary bg-surface-bright">
                            <th class="py-sm px-md font-medium uppercase tracking-wider">Petugas</th>
                            <th class="py-sm px-md font-medium uppercase tracking-wider">Checkpoint</th>
                            <th class="py-sm px-md font-medium uppercase tracking-wider">Waktu</th>
                            <th class="py-sm px-md font-medium uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-body-sm font-body-sm text-on-surface">
                        @forelse($recentLogs as $log)
                        <tr class="border-b border-outline-variant {{ $loop->even ? 'bg-surface-container-low' : '' }} hover:bg-surface-container-lowest transition-colors">
                            <td class="py-sm px-md flex items-center gap-sm">
                                <div class="w-8 h-8 bg-primary-container text-white rounded-full flex items-center justify-center font-bold text-xs">
                                    {{ strtoupper(substr($log->user?->name ?? 'NA', 0, 2)) }}
                                </div>
                                <span>{{ $log->user?->name ?? '-' }}</span>
                            </td>
                            <td class="py-sm px-md">{{ $log->checkpoint?->checkpoint_name ?? '-' }}</td>
                            <td class="py-sm px-md text-secondary">{{ \Carbon\Carbon::parse($log->scanned_at)->format('H:i') }} WIB</td>
                            <td class="py-sm px-md">
                                @if($log->status === 'on_time')
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-50 text-emerald-700 text-label-md font-label-md rounded-full border border-emerald-200">
                                    <span class="material-symbols-outlined text-[14px]">check</span> TEPAT WAKTU
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-amber-50 text-amber-700 text-label-md font-label-md rounded-full border border-amber-200">
                                    <span class="material-symbols-outlined text-[14px]">priority_high</span> TERLAMBAT
                                </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-xl px-md text-center text-secondary text-body-md">Belum ada log patroli hari ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Progres Patroli per Rute -->
        <div class="soft-card overflow-hidden flex flex-col">
            <div class="p-md border-b border-outline-variant bg-surface-container-low">
                <h3 class="text-headline-md font-headline-md text-on-surface">Progres Patroli per Rute</h3>
                <p class="text-body-sm font-body-sm text-secondary">Status checkpoint setiap rute hari ini</p>
            </div>

            <div class="p-md">
                @php
                    $routeStateMeta = [
                        'completed' => ['label' => 'Selesai', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'bar' => 'bg-emerald-500'],
                        'ongoing' => ['label' => 'Berjalan', 'class' => 'bg-blue-50 text-blue-700 border-blue-200', 'bar' => 'bg-primary'],
                        'upcoming' => ['label' => 'Belum Mulai', 'class' => 'bg-slate-50 text-slate-600 border-slate-200', 'bar' => 'bg-slate-400'],
                        'missed' => ['label' => 'Terlewat', 'class' => 'bg-amber-50 text-amber-700 border-amber-200', 'bar' => 'bg-amber-500'],
                    ];
                @endphp

                @forelse($routeProgress as $route)
                    @php $meta = $routeStateMeta[$route['state']] ?? $routeStateMeta['upcoming']; @endphp
                    <div class="py-md border-b border-outline-variant last:border-b-0">
                        <div class="flex items-start justify-between gap-md mb-sm">
                            <div class="min-w-0">
                                <div class="flex items-center gap-xs flex-wrap">
                                    <p class="text-body-sm font-bold text-on-surface truncate">{{ $route['name'] }}</p>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full border text-label-sm font-bold {{ $meta['class'] }}">{{ $meta['label'] }}</span>
                                </div>
                                <p class="text-label-md text-secondary mt-1">
                                    {{ $route['area'] }} &bull; Shift {{ $route['shift'] }} &bull; {{ $route['start_time'] }}–{{ $route['end_time'] }}
                                </p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="text-body-sm font-bold text-on-surface">{{ $route['scanned'] }}/{{ $route['total'] }}</p>
                                <p class="text-label-md text-secondary">{{ $route['percent'] }}%</p>
                            </div>
                        </div>

                        <div class="w-full h-2 bg-surface-container-high rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-700 {{ $meta['bar'] }}" style="width: {{ $route['percent'] }}%"></div>
                        </div>

                        <p class="mt-1 text-label-md text-secondary">
                            @if($route['last_scanned_at'])
                                Scan terakhir: {{ $route['last_scanned_at'] }} WIB
                            @else
                                Belum ada scan hari ini
                            @endif
                        </p>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-xl text-center">
                        <div class="w-16 h-16 rounded-full bg-surface-container-high flex items-center justify-center mb-md">
                            <span class="material-symbols-outlined text-[32px] text-secondary">route</span>
                        </div>
                        <p class="text-body-sm font-semibold text-on-surface mb-1">Belum ada rute terjadwal</p>
                        <p class="text-body-sm text-secondary max-w-[220px]">Rute yang dijadwalkan akan tampil di sini beserta progres checkpoint-nya.</p>
                    </div>
                @endforelse

                @if(count($routeProgress) > 0)
                <div class="mt-md flex flex-wrap gap-md border-t border-outline-variant pt-md">
                    @foreach($routeStateMeta as $meta)
                    <div class="flex items-center gap-xs">
                        <span class="w-2.5 h-2.5 rounded-full {{ $meta['bar'] }}"></span>
                        <span class="text-label-md font-label-md text-secondary">{{ $meta['label'] }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column (Narrow): Incident Feed -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl flex flex-col">
        <div class="p-md border-b border-outline-variant bg-surface-container-low">
            <h3 class="text-headline-md font-headline-md text-on-surface flex items-center gap-xs">
                <span class="material-symbols-outlined text-error">error</span>
                Peringatan Insiden Terbaru
            </h3>
        </div>
        <div class="flex-1 flex flex-col">

            @forelse($recentIncidents as $incident)
            <!-- Incident Item -->
            <div class="p-md border-b border-outline-variant hover:bg-surface-bright cursor-pointer transition-colors flex gap-sm items-start relative">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-error"></div>
                <div class="w-8 h-8 rounded-full bg-error-container flex items-center justify-center flex-shrink-0 mt-0.5">
                    <span class="material-symbols-outlined text-error text-[18px]">door_front</span>
                </div>
                <div class="min-w-0">
                    <p class="text-body-sm font-body-sm text-on-surface font-semibold mb-1">{{ $incident->title }}</p>
                    <p class="text-body-sm font-body-sm text-secondary mb-2">{{ $incident->description }}</p>
                    <div class="flex items-center gap-xs">
                        <span class="material-symbols-outlined text-secondary text-[14px]">schedule</span>
                        <p class="text-label-md font-label-md text-secondary">{{ \Carbon\Carbon::parse($incident->reported_at)->translatedFormat('d M, H:i') }} WIB &bull; Dilaporkan oleh {{ $incident->reportedBy?->name ?? '-' }}</p>
                    </div>
                </div>
            </div>
            @empty
            <!-- Empty state -->
            <div class="flex-1 flex flex-col items-center justify-center p-xl text-center">
                <div class="w-16 h-16 rounded-full bg-emerald-50 flex items-center justify-center mb-md">
                    <span class="material-symbols-outlined text-[32px] text-emerald-500">task_alt</span>
                </div>
                <p class="text-body-sm font-body-sm text-on-surface font-semibold mb-1">Semua aman terkendali</p>
                <p class="text-body-sm font-body-sm text-secondary max-w-[200px]">Tidak ada insiden lain yang perlu ditangani saat ini.</p>
            </div>
            @endforelse

        </div>
    </div>
</div>

</x-app-layout>