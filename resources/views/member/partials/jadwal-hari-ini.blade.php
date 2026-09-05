{{-- Daftar semua jadwal hari ini (aktif + mendatang) — dipakai di halaman patrol --}}
@if($todaySchedules->isNotEmpty())
<div class="bg-surface border border-outline-variant rounded-3xl p-lg shadow-sm">
    <h3 class="text-title-lg font-bold text-on-surface mb-md flex items-center gap-sm">
        <span class="material-symbols-outlined text-primary">calendar_month</span>
        Jadwal Hari Ini
    </h3>
    <div class="space-y-sm">
        @foreach($todaySchedules as $ts)
        <div class="flex items-center gap-md p-md rounded-2xl border transition-all
                    {{ in_array($ts->id, $activeScheduleIds) ? 'bg-emerald-50/50 border-emerald-200' : 'bg-surface-container-lowest border-outline-variant' }}">
            <div class="w-11 h-11 rounded-xl bg-surface-container-high flex items-center justify-center flex-shrink-0 text-secondary">
                <span class="material-symbols-outlined">route</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-body-md font-bold text-on-surface truncate">{{ $ts->schedule_name }}</p>
                <p class="text-label-md text-secondary truncate flex items-center gap-xs">
                    <span class="material-symbols-outlined text-[14px]">location_on</span>
                    {{ $ts->area?->area_name ?? '-' }}
                </p>
            </div>
            <div class="flex flex-col items-end gap-xs flex-shrink-0">
                <span class="text-label-md text-secondary whitespace-nowrap">
                    {{ \Carbon\Carbon::parse($ts->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($ts->end_time)->format('H:i') }}
                </span>
                @if(in_array($ts->id, $activeScheduleIds))
                <span class="inline-flex items-center gap-xs px-2 py-0.5 bg-emerald-100 text-emerald-700 text-label-sm font-bold rounded-full border border-emerald-200 whitespace-nowrap">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span> Aktif
                </span>
                @else
                <span class="inline-flex items-center gap-xs px-2 py-0.5 bg-surface-container-high text-secondary text-label-sm font-bold rounded-full border border-outline-variant whitespace-nowrap">
                    <span class="material-symbols-outlined text-[12px]">schedule</span> Menunggu Jam {{ \Carbon\Carbon::parse($ts->start_time)->format('H:i') }}
                </span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
