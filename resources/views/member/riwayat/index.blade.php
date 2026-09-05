<x-member-layout>
<div class="mb-xl border-b border-outline-variant pb-md flex flex-col md:flex-row md:items-end justify-between gap-md">
    <div>
        <h2 class="text-headline-md md:text-display font-bold text-on-surface mb-2">Riwayat Patroli</h2>
        <p class="text-body-lg text-secondary">Catatan seluruh aktivitas patroli Anda.</p>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('member.riwayat') }}" class="flex items-center gap-sm flex-wrap">
        <div class="relative">
            <span class="material-symbols-outlined absolute left-md top-1/2 -translate-y-1/2 text-secondary pointer-events-none">calendar_today</span>
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="bg-surface border border-outline-variant rounded-2xl py-md pl-[48px] pr-md text-body-lg text-on-surface outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 shadow-sm">
        </div>
        <span class="text-secondary">—</span>
        <div class="relative">
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="bg-surface border border-outline-variant rounded-2xl py-md pl-md pr-md text-body-lg text-on-surface outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 shadow-sm">
        </div>
        <select name="status" class="appearance-none bg-surface border border-outline-variant rounded-2xl py-md pl-md pr-xl text-body-lg text-on-surface outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 shadow-sm cursor-pointer">
            <option value="">Semua Status</option>
            <option value="on_time" {{ request('status') === 'on_time' ? 'selected' : '' }}>Tepat Waktu</option>
            <option value="late" {{ request('status') === 'late' ? 'selected' : '' }}>Terlambat</option>
        </select>
        <button type="submit" class="bg-primary text-on-primary font-bold py-md px-xl rounded-2xl hover:opacity-90 transition-all shadow-sm">
            Filter
        </button>
        @if(request()->anyFilled(['date_from', 'date_to', 'status']))
            <a href="{{ route('member.riwayat') }}" class="text-secondary hover:text-on-surface transition-colors text-body-md font-bold">Reset</a>
        @endif
    </form>
</div>

@if($logs->count() > 0)
    <!-- Desktop Table View -->
    <div class="hidden md:block bg-surface border border-outline-variant rounded-3xl overflow-hidden shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-lowest border-b border-outline-variant">
                    <th class="py-md px-lg text-label-md uppercase tracking-wider text-secondary font-bold">Tanggal & Waktu</th>
                    <th class="py-md px-lg text-label-md uppercase tracking-wider text-secondary font-bold">Checkpoint</th>
                    <th class="py-md px-lg text-label-md uppercase tracking-wider text-secondary font-bold">Rute</th>
                    <th class="py-md px-lg text-label-md uppercase tracking-wider text-secondary font-bold">Area</th>
                    <th class="py-md px-lg text-label-md uppercase tracking-wider text-secondary font-bold text-right">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @foreach($logs as $log)
                <tr class="hover:bg-surface-container-lowest transition-colors">
                    <td class="py-lg px-lg">
                        <p class="text-title-md font-bold text-on-surface">{{ \Carbon\Carbon::parse($log->scanned_at)->format('d M Y') }}</p>
                        <p class="text-body-md text-secondary flex items-center gap-xs mt-1">
                            <span class="material-symbols-outlined text-[16px]">schedule</span> {{ \Carbon\Carbon::parse($log->scanned_at)->format('H:i') }}
                        </p>
                    </td>
                    <td class="py-lg px-lg">
                        <p class="text-title-md font-bold text-on-surface">{{ $log->checkpoint->checkpoint_name ?? '-' }}</p>
                    </td>
                    <td class="py-lg px-lg">
                        <p class="text-body-lg text-secondary">{{ $log->patrolSchedule->schedule_name ?? '-' }}</p>
                    </td>
                    <td class="py-lg px-lg">
                        <p class="text-body-lg text-secondary flex items-center gap-xs">
                            <span class="material-symbols-outlined text-[20px]">location_on</span> {{ $log->patrolSchedule->area->area_name ?? '-' }}
                        </p>
                    </td>
                    <td class="py-lg px-lg text-right">
                        @if($log->status === 'on_time')
                            <span class="inline-flex items-center gap-sm px-3 py-1.5 bg-emerald-50 text-emerald-700 text-body-md font-bold rounded-full border border-emerald-200">
                                <span class="material-symbols-outlined text-[18px]">check_circle</span> Tepat Waktu
                            </span>
                        @else
                            <span class="inline-flex items-center gap-sm px-3 py-1.5 bg-amber-50 text-amber-700 text-body-md font-bold rounded-full border border-amber-300">
                                <span class="material-symbols-outlined text-[18px]">warning</span> Terlambat
                            </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="md:hidden space-y-md">
        @foreach($logs as $log)
        <div class="bg-surface border border-outline-variant rounded-2xl p-lg shadow-sm">
            <div class="flex items-start justify-between gap-md mb-md">
                <div>
                    <p class="text-label-md text-secondary font-bold mb-1">{{ \Carbon\Carbon::parse($log->scanned_at)->format('d M Y • H:i') }}</p>
                    <h4 class="text-title-lg font-bold text-on-surface">{{ $log->checkpoint->checkpoint_name ?? '-' }}</h4>
                </div>
                @if($log->status === 'on_time')
                    <span class="inline-flex items-center gap-xs px-2.5 py-1 bg-emerald-50 text-emerald-700 text-label-md font-bold rounded-full border border-emerald-200">Tepat Waktu</span>
                @else
                    <span class="inline-flex items-center gap-xs px-2.5 py-1 bg-amber-50 text-amber-700 text-label-md font-bold rounded-full border border-amber-300">Terlambat</span>
                @endif
            </div>
            <div class="grid grid-cols-2 gap-md bg-surface-container-lowest p-md rounded-xl border border-outline-variant">
                <div>
                    <p class="text-label-sm text-secondary uppercase tracking-wider mb-1">Rute</p>
                    <p class="text-body-md font-bold">{{ $log->patrolSchedule->schedule_name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-label-sm text-secondary uppercase tracking-wider mb-1">Area</p>
                    <p class="text-body-md font-bold">{{ $log->patrolSchedule->area->area_name ?? '-' }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-lg">
        {{ $logs->links() }}
    </div>
@else
    <div class="text-center py-xl">
        <span class="material-symbols-outlined text-[64px] text-outline">history</span>
        <p class="text-title-lg text-secondary mt-md">Belum ada riwayat patroli</p>
        <p class="text-body-lg text-secondary mt-sm">Data akan muncul setelah Anda melakukan patroli.</p>
    </div>
@endif

</x-member-layout>
