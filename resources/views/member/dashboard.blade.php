<x-member-layout>

<!-- Greeting & Top Stats -->
<div class="flex flex-col xl:flex-row xl:items-end justify-between gap-lg mb-xl">
    <div class="flex items-center gap-lg">
        <div class="w-20 h-20 bg-gradient-to-br from-primary to-primary-container rounded-2xl border border-primary/20 shadow-lg flex items-center justify-center text-on-primary font-bold text-[32px] tracking-wider">
            {{ strtoupper(substr($user->name, 0, 2)) }}
        </div>
        <div>
            <p class="text-body-lg text-secondary font-medium mb-1">{{ $greeting }}</p>
            <h2 class="text-headline-md md:text-display font-bold text-on-surface tracking-tight">{{ $user->name }}</h2>
            @if($currentBadge)
            <p class="text-body-lg text-secondary mt-1 flex items-center gap-sm">
                <span class="material-symbols-outlined text-[20px]">badge</span> {{ $currentBadge }}
            </p>
            @else
            <p class="text-body-lg text-secondary mt-1 flex items-center gap-sm">
                <span class="material-symbols-outlined text-[20px]">badge</span> {{ $user->role?->role_name ?? 'Petugas' }}
            </p>
            @endif
        </div>
    </div>
</div>

<!-- Main Grid Layout -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-lg mb-xl">

    <!-- Left Column: Active Patrol & Quick Actions -->
    <div class="lg:col-span-8 space-y-lg">

        @if($activeSchedules->isNotEmpty())
        {{-- Active Patrol Card (jadwal aktif pertama) --}}
        @php $schedule = $activeSchedules->first(); @endphp
        <div class="bg-gradient-to-r from-primary to-blue-700 text-on-primary rounded-3xl p-lg md:p-xl shadow-xl shadow-primary/20 relative overflow-hidden group">
            <!-- Background Decoration -->
            <div class="absolute -right-12 -top-12 opacity-10 group-hover:scale-110 transition-transform duration-700 ease-in-out">
                <span class="material-symbols-outlined text-[240px]">shield</span>
            </div>

            <div class="relative z-10">
                <div class="flex flex-col md:flex-row md:items-start justify-between gap-lg mb-xl">
                    <div>
                        <div class="inline-flex items-center gap-xs px-3 py-1 bg-on-primary/20 backdrop-blur-md rounded-full text-label-md font-bold text-white mb-md border border-white/20 shadow-sm">
                            <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span> Patroli Berjalan
                        </div>
                        <h3 class="text-headline-md md:text-display font-bold mb-2">{{ $schedule->schedule_name }}</h3>
                        <p class="text-title-md opacity-90 flex items-center gap-sm">
                            <span class="material-symbols-outlined text-[20px]">location_on</span> Area: {{ $schedule->area?->area_name ?? '-' }}
                        </p>
                    </div>
                    <div class="bg-black/20 backdrop-blur-md text-white px-lg py-md rounded-2xl text-center border border-white/10 shadow-inner">
                        <p class="text-label-md opacity-80 uppercase tracking-widest mb-1">Jadwal</p>
                        <p class="text-headline-md font-bold">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</p>
                    </div>
                </div>

                <!-- Progress -->
                @php
                    $total = $schedule->checkpoints->count();
                    $scanned = count($schedule->scannedCheckpointIds);
                    $percent = $total > 0 ? round($scanned / $total * 100) : 0;
                @endphp
                <div class="mb-xl bg-black/10 rounded-2xl p-md border border-white/10 backdrop-blur-sm">
                    <div class="flex justify-between text-body-lg mb-sm font-semibold">
                        <span class="flex items-center gap-sm"><span class="material-symbols-outlined text-[20px]">flag</span> Progress Checkpoint</span>
                        <span>{{ $scanned }} dari {{ $total }}</span>
                    </div>
                    <div class="w-full h-4 bg-black/20 rounded-full overflow-hidden shadow-inner">
                        <div class="h-full bg-emerald-400 rounded-full transition-all duration-1000 ease-out shadow-[0_0_10px_rgba(52,211,153,0.5)] relative overflow-hidden" style="width: {{ $percent }}%">
                            <div class="absolute inset-0 bg-white/20 w-full h-full transform -skew-x-12 animate-[shimmer_2s_infinite]"></div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-md">
                    <a href="{{ route('member.patrol') }}" class="flex-1 bg-white text-primary hover:bg-surface-container-lowest py-lg rounded-2xl font-bold text-title-lg flex items-center justify-center gap-sm transition-all shadow-lg hover:shadow-xl active:scale-[0.98]">
                        <span class="material-symbols-outlined text-[32px]">directions_walk</span> Lanjutkan Patroli
                    </a>
                    @if($activeSchedules->count() > 1)
                    <a href="{{ route('member.patrol') }}" class="bg-white/15 hover:bg-white/25 text-white py-lg px-xl rounded-2xl font-bold text-title-lg flex items-center justify-center gap-sm transition-all border border-white/20 active:scale-[0.98]">
                        <span class="material-symbols-outlined text-[32px]">swap_horiz</span> {{ $activeSchedules->count() }} Jadwal
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @else
        {{-- Empty State: tidak ada jadwal aktif --}}
        <div class="bg-surface border border-outline-variant rounded-3xl p-lg md:p-xl shadow-sm relative overflow-hidden">
            <div class="absolute -right-12 -top-12 opacity-5">
                <span class="material-symbols-outlined text-[240px]">event_busy</span>
            </div>
            <div class="relative z-10 text-center py-xl">
                <div class="w-20 h-20 mx-auto mb-lg bg-surface-container-high rounded-3xl flex items-center justify-center opacity-60">
                    <span class="material-symbols-outlined text-[40px] text-secondary">event_busy</span>
                </div>
                <h3 class="text-headline-md font-bold text-on-surface mb-2">Belum Ada Patroli Aktif</h3>
                <p class="text-body-lg text-secondary max-w-md mx-auto mb-lg">Saat ini tidak ada jadwal yang sedang berjalan. Halaman patroli akan menampilkan jadwal aktif secara otomatis saat masuk jam patroli.</p>
                <a href="{{ route('member.patrol') }}" class="inline-flex items-center gap-sm bg-primary text-on-primary py-md px-xl rounded-2xl font-bold text-title-md transition-all hover:bg-surface-tint hover:shadow-lg active:scale-[0.98]">
                    <span class="material-symbols-outlined text-[24px]">directions_walk</span> Buka Halaman Patroli
                </a>
            </div>
        </div>
        @endif

        <!-- Quick Action Grid -->
        <div>
            <h3 class="text-title-lg font-bold text-on-surface mb-md">Aksi Cepat</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-md">
                <!-- Riwayat Patroli -->
                <a href="{{ route('member.riwayat') }}" class="bg-surface border border-outline-variant rounded-2xl p-lg flex flex-col items-center justify-center gap-md hover:border-emerald-500 hover:shadow-lg hover:shadow-emerald-500/10 transition-all group active:scale-95">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-[32px]">history</span>
                    </div>
                    <span class="text-body-lg font-bold text-on-surface text-center">Riwayat<br>Patroli</span>
                </a>

                <!-- Semua Jadwal -->
                <a href="{{ route('member.patrol') }}" class="bg-surface border border-outline-variant rounded-2xl p-lg flex flex-col items-center justify-center gap-md hover:border-blue-500 hover:shadow-lg hover:shadow-blue-500/10 transition-all group active:scale-95">
                    <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-[32px]">calendar_month</span>
                    </div>
                    <span class="text-body-lg font-bold text-on-surface text-center">Semua<br>Jadwal</span>
                </a>

                <!-- Lapor Insiden -->
                <button type="button" @click="$dispatch('open-incident-modal')"
                        class="bg-surface border border-outline-variant rounded-2xl p-lg flex flex-col items-center justify-center gap-md hover:border-red-500 hover:shadow-lg hover:shadow-red-500/10 transition-all group active:scale-95">
                    <div class="w-16 h-16 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-[32px]">report_problem</span>
                    </div>
                    <span class="text-body-lg font-bold text-on-surface text-center">Lapor<br>Insiden</span>
                </button>
            </div>
        </div>

    </div>

    <!-- Right Column: Stats & Schedule -->
    <div class="lg:col-span-4 space-y-lg">

        <!-- Statistik Ringkas -->
        <div class="bg-surface border border-outline-variant rounded-3xl p-lg shadow-sm">
            <h3 class="text-title-lg font-bold text-on-surface mb-lg">Statistik Hari Ini</h3>
            <div class="grid grid-cols-2 gap-md">
                <div class="soft-card soft-card-hover p-md text-center">
                    <p class="text-headline-md md:text-display font-bold text-primary mb-1">{{ $stats['jadwal_total'] }}</p>
                    <p class="text-body-md text-secondary font-medium">Jadwal Aktif</p>
                </div>
                <div class="soft-card soft-card-hover p-md text-center">
                    <p class="text-headline-md md:text-display font-bold text-emerald-600 mb-1">{{ $stats['cp_selesai'] }}</p>
                    <p class="text-body-md text-secondary font-medium">CP Selesai</p>
                </div>
                <div class="soft-card soft-card-hover p-md text-center">
                    <p class="text-headline-md md:text-display font-bold text-amber-600 mb-1">{{ $stats['cp_terlewat'] }}</p>
                    <p class="text-body-md text-secondary font-medium">CP Belum Discan</p>
                </div>
            </div>
        </div>

        <!-- Jadwal Mendatang -->
        <div class="bg-surface border border-outline-variant rounded-3xl p-lg shadow-sm">
            <div class="flex items-center justify-between mb-lg">
                <h3 class="text-title-lg font-bold text-on-surface">Jadwal Mendatang</h3>
                <a href="{{ route('member.patrol') }}" class="text-body-md font-bold text-primary hover:underline hover:text-primary-container transition-colors">Lihat Semua</a>
            </div>

            @forelse($upcomingSchedules as $upcoming)
            <div class="group border border-outline-variant rounded-2xl p-md flex items-center gap-md hover:bg-surface-container-lowest transition-colors relative overflow-hidden">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-surface-variant"></div>
                <div class="w-14 h-14 rounded-xl bg-surface-container-high flex flex-col items-center justify-center flex-shrink-0 text-secondary group-hover:scale-105 transition-transform">
                    <span class="text-title-md font-bold">{{ \Carbon\Carbon::parse($upcoming->start_time)->format('H:i') }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-title-md font-bold text-on-surface truncate">{{ $upcoming->schedule_name }}</h4>
                    <p class="text-body-md text-secondary mt-1 flex items-center gap-xs">
                        <span class="material-symbols-outlined text-[16px]">location_on</span>
                        {{ $upcoming->area?->area_name ?? '-' }}
                        <span class="material-symbols-outlined text-[16px] ml-xs">schedule</span>
                        {{ \Carbon\Carbon::parse($upcoming->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($upcoming->end_time)->format('H:i') }}
                    </p>
                </div>
            </div>
            @empty
            <div class="text-center py-lg text-secondary text-body-sm opacity-60">
                <span class="material-symbols-outlined text-[32px] block mb-xs">calendar_month</span>
                Belum ada jadwal mendatang.
            </div>
            @endforelse
        </div>

    </div>
</div>

<style>
@keyframes shimmer {
    100% {
        transform: translateX(100%) skew(-12deg);
    }
}
</style>

</x-member-layout>
