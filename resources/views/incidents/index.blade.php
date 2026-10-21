<x-app-layout>

@php
    // ── Mapping tampilan (badge/ikon) ───────────────────────────────────────
    $categoryMeta = [
        'kebakaran'          => ['icon' => 'local_fire_department', 'class' => 'bg-red-50 text-red-700 border-red-200', 'iconColor' => 'text-red-500'],
        'kebocoran'          => ['icon' => 'plumbing',              'class' => 'bg-blue-50 text-blue-700 border-blue-200',  'iconColor' => 'text-blue-500'],
        'orang_mencurigakan' => ['icon' => 'person_alert',          'class' => 'bg-amber-50 text-amber-700 border-amber-200', 'iconColor' => 'text-amber-600'],
        'kerusakan'          => ['icon' => 'build',                 'class' => 'bg-gray-50 text-gray-700 border-gray-200', 'iconColor' => 'text-gray-600'],
        'kecelakaan'         => ['icon' => 'emergency',             'class' => 'bg-red-50 text-red-700 border-red-200', 'iconColor' => 'text-red-400'],
        'lainnya'            => ['icon' => 'help',                  'class' => 'bg-gray-50 text-gray-700 border-gray-200', 'iconColor' => 'text-gray-500'],
    ];
    $priorityMeta = [
        'critical' => ['label' => 'CRITICAL', 'class' => 'bg-red-100 text-red-800',          'dot' => 'bg-red-500'],
        'high'     => ['label' => 'HIGH',     'class' => 'bg-orange-100 text-orange-800',    'dot' => 'bg-orange-500'],
        'medium'   => ['label' => 'MEDIUM',   'class' => 'bg-yellow-100 text-yellow-800',    'dot' => 'bg-yellow-500'],
        'low'      => ['label' => 'LOW',      'class' => 'bg-slate-100 text-slate-700',      'dot' => 'bg-slate-400'],
    ];
    $statusMeta = [
        'new'                => ['label' => 'NEW',            'class' => 'bg-blue-50 text-blue-700 border-blue-200'],
        'in_review'          => ['label' => 'IN REVIEW',      'class' => 'bg-amber-50 text-amber-700 border-amber-200'],
        'approved'           => ['label' => 'APPROVED',       'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
        'rejected'           => ['label' => 'REJECTED',       'class' => 'bg-red-50 text-red-600 border-red-200'],
        'follow_up_required' => ['label' => 'FOLLOW-UP',      'class' => 'bg-orange-50 text-orange-700 border-orange-200'],
        'closed'             => ['label' => 'CLOSED',         'class' => 'bg-surface-container text-secondary border-outline-variant'],
    ];

    // ── Data JSON untuk modal detail (Alpine) ────────────────────────────────
    $incidentsJson = $incidents->getCollection()->map(function ($i) use ($categoryMeta, $priorityMeta, $statusMeta) {
        $st = $statusMeta[$i->status] ?? $statusMeta['new'];
        $pr = $priorityMeta[$i->priority] ?? $priorityMeta['low'];
        $ca = $categoryMeta[$i->category] ?? $categoryMeta['lainnya'];

        return [
            'id'             => $i->id,
            'id_text'        => '#INC-' . str_pad($i->id, 3, '0', STR_PAD_LEFT),
            'title'          => $i->title,
            'description'    => $i->description,
            'reporter'       => $i->user?->name ?? '-',
            'area'           => $i->area?->area_name ?? '-',
            'reported_at'    => $i->reported_at?->format('d M Y, H:i'),
            'coords'         => $i->latitude !== null && $i->longitude !== null
                ? trim($i->latitude) . ', ' . trim($i->longitude)
                : null,
            'photo_url'      => $i->photo ? \Illuminate\Support\Facades\Storage::disk('public')->url($i->photo) : null,
            'status_label'   => $st['label'],
            'status_class'   => $st['class'],
            'priority_label' => $pr['label'],
            'priority_class' => $pr['class'],
            'priority_dot'   => $pr['dot'],
            'category_label' => $ca['label'] ?? \App\Models\Incident::CATEGORIES[$i->category] ?? $i->category,
            'category_class' => $ca['class'],
            'history'        => $i->statusHistory->map(fn ($h) => [
                'status' => $h->status,
                'meta'   => $h->changed_at?->format('d M Y, H:i') . ' oleh ' . ($h->user?->name ?? '-'),
            ])->values()->all(),
        ];
    })->values()->all();
@endphp

{{-- ══ Alpine: state untuk modal detail insiden ═══════════════════════════ --}}
<div x-data="incidentPage()" x-init="init()">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-lg gap-md">
        <div>
            <p class="text-body-md font-body-md text-secondary mb-xs">Incident Management</p>
            <h2 class="text-headline-lg font-headline-lg text-on-surface">Laporan Insiden</h2>
        </div>
    </div>

    <!-- Stats Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-md mb-xl">
        <!-- Total Insiden -->
        <div class="soft-card soft-card-hover p-md flex flex-col">
            <p class="text-body-sm font-body-sm text-secondary mb-sm">Total Insiden</p>
            <p class="text-headline-lg font-headline-lg text-on-surface">{{ $stats['total'] }}</p>
        </div>
        <!-- New -->
        <div class="soft-card soft-card-hover p-md flex flex-col">
            <div class="flex items-center gap-xs mb-sm">
                <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                <p class="text-body-sm font-body-sm text-secondary">New</p>
            </div>
            <p class="text-headline-lg font-headline-lg text-on-surface">{{ $stats['new'] }}</p>
        </div>
        <!-- In Review -->
        <div class="soft-card soft-card-hover p-md flex flex-col">
            <div class="flex items-center gap-xs mb-sm">
                <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                <p class="text-body-sm font-body-sm text-secondary">In Review</p>
            </div>
            <p class="text-headline-lg font-headline-lg text-on-surface">{{ $stats['in_review'] }}</p>
        </div>
        <!-- Follow-up -->
        <div class="soft-card soft-card-hover p-md flex flex-col">
            <div class="flex items-center gap-xs mb-sm">
                <div class="w-2 h-2 rounded-full bg-orange-500"></div>
                <p class="text-body-sm font-body-sm text-secondary">Follow-up</p>
            </div>
            <p class="text-headline-lg font-headline-lg text-on-surface">{{ $stats['follow_up'] }}</p>
        </div>
        <!-- Closed -->
        <div class="soft-card soft-card-hover p-md flex flex-col">
            <div class="flex items-center gap-xs mb-sm">
                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                <p class="text-body-sm font-body-sm text-secondary">Closed</p>
            </div>
            <p class="text-headline-lg font-headline-lg text-on-surface">{{ $stats['closed'] }}</p>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <form method="GET" action="{{ route('incidents.index') }}"
          class="soft-card soft-card-hover p-md mb-md flex flex-col lg:flex-row gap-sm items-stretch lg:items-center">
        <!-- Search -->
        <div class="relative flex-1 max-w-md">
            <span class="material-symbols-outlined absolute left-sm top-1/2 -translate-y-1/2 text-secondary text-[20px]">search</span>
            <input name="search" value="{{ request('search') }}"
                   class="w-full bg-surface-container-low border border-outline-variant rounded-xl py-xs pl-[36px] pr-sm text-body-sm font-body-sm focus:border-primary-container focus:ring-2 focus:ring-primary-container/20 transition-all outline-none text-on-surface"
                   placeholder="Cari judul insiden, petugas...">
        </div>
        <!-- Filter Status -->
        <select name="status" class="bg-surface-container-low border border-outline-variant rounded-xl py-xs px-sm text-body-sm font-body-sm text-on-surface outline-none focus:border-primary-container">
            <option value="">Semua Status</option>
            @foreach(\App\Models\Incident::STATUSES as $val => $label)
                <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
            @endforeach
        </select>
        <!-- Filter Kategori -->
        <select name="category" class="bg-surface-container-low border border-outline-variant rounded-xl py-xs px-sm text-body-sm font-body-sm text-on-surface outline-none focus:border-primary-container">
            <option value="">Semua Kategori</option>
            @foreach(\App\Models\Incident::CATEGORIES as $val => $label)
                <option value="{{ $val }}" @selected(request('category') === $val)>{{ $label }}</option>
            @endforeach
        </select>
        <!-- Filter Prioritas -->
        <select name="priority" class="bg-surface-container-low border border-outline-variant rounded-xl py-xs px-sm text-body-sm font-body-sm text-on-surface outline-none focus:border-primary-container">
            <option value="">Semua Prioritas</option>
            @foreach(\App\Models\Incident::PRIORITIES as $val => $label)
                <option value="{{ $val }}" @selected(request('priority') === $val)>{{ ucfirst($label) }}</option>
            @endforeach
        </select>
        <!-- Filter Tanggal -->
        <input type="date" name="date" value="{{ request('date') }}"
               class="bg-surface-container-low border border-outline-variant rounded-xl py-xs px-sm text-body-sm font-body-sm text-on-surface outline-none focus:border-primary-container">
        <!-- Tombol -->
        <button type="submit" class="bg-primary text-on-primary py-xs px-md rounded-xl text-body-sm font-semibold hover:opacity-90 transition-opacity whitespace-nowrap">
            Filter
        </button>
        @if(request()->hasAny(['search','status','category','priority','date']))
        <a href="{{ route('incidents.index') }}"
           class="py-xs px-md rounded-xl text-body-sm border border-outline-variant text-secondary hover:bg-surface-container transition-colors whitespace-nowrap text-center">
            Reset
        </a>
        @endif
    </form>

    <!-- Incident Table -->
    <div class="soft-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-modern w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-outline-variant/60 text-label-md font-label-md text-secondary">
                        <th class="py-sm px-md font-medium uppercase tracking-wider">ID</th>
                        <th class="py-sm px-md font-medium uppercase tracking-wider">Judul</th>
                        <th class="py-sm px-md font-medium uppercase tracking-wider">Kategori</th>
                        <th class="py-sm px-md font-medium uppercase tracking-wider">Prioritas</th>
                        <th class="py-sm px-md font-medium uppercase tracking-wider">Pelapor</th>
                        <th class="py-sm px-md font-medium uppercase tracking-wider">Lokasi</th>
                        <th class="py-sm px-md font-medium uppercase tracking-wider">Waktu</th>
                        <th class="py-sm px-md font-medium uppercase tracking-wider">Status</th>
                        <th class="py-sm px-md font-medium uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-body-sm font-body-sm text-on-surface">

                    @forelse($incidents as $incident)
                    @php
                        $cat  = $categoryMeta[$incident->category] ?? $categoryMeta['lainnya'];
                        $prio = $priorityMeta[$incident->priority] ?? $priorityMeta['low'];
                        $st   = $statusMeta[$incident->status] ?? $statusMeta['new'];
                    @endphp
                    <tr class="border-b border-outline-variant {{ $loop->even ? 'bg-surface-container-low' : '' }} hover:bg-surface-container-low transition-colors">
                        <td class="py-sm px-md text-secondary whitespace-nowrap">#INC-{{ str_pad($incident->id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td class="py-sm px-md">
                            <div class="flex items-center gap-sm">
                                <span class="material-symbols-outlined text-[18px] {{ $cat['iconColor'] }}">{{ $cat['icon'] }}</span>
                                <span class="font-semibold">{{ $incident->title }}</span>
                            </div>
                        </td>
                        <td class="py-sm px-md">
                            <span class="inline-flex items-center gap-xs px-2 py-1 text-label-md font-label-md rounded border {{ $cat['class'] }}">
                                {{ \App\Models\Incident::CATEGORIES[$incident->category] ?? $incident->category }}
                            </span>
                        </td>
                        <td class="py-sm px-md">
                            <span class="inline-flex items-center gap-xs px-2 py-1 text-label-md font-label-md rounded-full font-bold {{ $prio['class'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $prio['dot'] }}"></span>
                                {{ $prio['label'] }}
                            </span>
                        </td>
                        <td class="py-sm px-md">
                            <div class="flex items-center gap-sm">
                                <div class="w-7 h-7 bg-surface-container-highest rounded-full flex items-center justify-center text-on-surface font-bold text-[10px]">
                                    {{ strtoupper(substr($incident->user?->name ?? '?', 0, 2)) }}
                                </div>
                                <span>{{ $incident->user?->name ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="py-sm px-md text-secondary">{{ $incident->area?->area_name ?? '-' }}</td>
                        <td class="py-sm px-md text-secondary whitespace-nowrap">
                            {{ $incident->reported_at?->format('d M Y, H:i') }}
                        </td>
                        <td class="py-sm px-md">
                            <span class="inline-block px-2 py-1 text-label-md font-label-md rounded border {{ $st['class'] }}">{{ $st['label'] }}</span>
                        </td>
                        <td class="py-sm px-md text-right whitespace-nowrap">
                            <button type="button" @click="openDetail({{ $incident->id }})"
                                    class="p-xs text-on-surface-variant hover:bg-surface-container rounded-full transition-colors" title="Lihat Detail">
                                <span class="material-symbols-outlined text-[20px]">visibility</span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-xl px-md text-center text-secondary">
                            <div class="flex flex-col items-center gap-sm opacity-60">
                                <span class="material-symbols-outlined text-[48px]">report_problem</span>
                                <p class="text-body-md font-medium">Belum ada laporan insiden</p>
                                @if(request()->hasAny(['search','status','category','priority','date']))
                                    <p class="text-body-sm">Tidak ada hasil untuk filter yang dipilih. <a href="{{ route('incidents.index') }}" class="text-primary underline">Reset filter</a></p>
                                @else
                                    <p class="text-body-sm">Laporan insiden dari petugas akan tampil di sini.</p>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($incidents->hasPages())
        <div class="flex items-center justify-between p-md border-t border-outline-variant bg-surface-container-low">
            <p class="text-body-sm font-body-sm text-secondary">
                Menampilkan {{ $incidents->firstItem() }}–{{ $incidents->lastItem() }}
                dari {{ $incidents->total() }} insiden
            </p>
            <div class="flex items-center gap-xs">
                {{-- Previous --}}
                @if($incidents->onFirstPage())
                    <span class="p-xs px-sm border border-outline-variant rounded-lg text-secondary opacity-40 text-body-sm cursor-not-allowed">
                        <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                    </span>
                @else
                    <a href="{{ $incidents->previousPageUrl() }}"
                       class="p-xs px-sm border border-outline-variant rounded-lg text-secondary hover:bg-surface-container transition-colors text-body-sm">
                        <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                    </a>
                @endif

                {{-- Page Numbers --}}
                @foreach($incidents->getUrlRange(1, $incidents->lastPage()) as $page => $url)
                    @if($page == $incidents->currentPage())
                        <span class="p-xs px-sm border border-primary bg-primary text-on-primary rounded-lg text-body-sm font-semibold">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}"
                           class="p-xs px-sm border border-outline-variant rounded-lg text-secondary hover:bg-surface-container transition-colors text-body-sm">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Next --}}
                @if($incidents->hasMorePages())
                    <a href="{{ $incidents->nextPageUrl() }}"
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

    {{-- ══ Modal Detail Insiden ═════════════════════════════════════════════ --}}
    <div x-show="detail !== null"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-gutter"
         style="display:none"
         @keydown.escape.window="detail = null">
        <div class="bg-surface-container-lowest rounded-xl shadow-xl max-w-2xl w-full max-h-[85vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-md border-b border-outline-variant bg-surface-container-low rounded-t-xl sticky top-0">
                <div>
                    <p class="text-label-md font-label-md text-secondary" x-text="detail ? detail.id_text : ''"></p>
                    <h3 class="text-headline-md font-headline-md text-on-surface" x-text="detail ? detail.title : ''"></h3>
                </div>
                <button @click="detail = null" class="p-xs text-on-surface-variant hover:bg-surface-container rounded-full transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <!-- Modal Body -->
            <div class="p-md space-y-md">
                <!-- Status, Prioritas, Kategori -->
                <div class="flex flex-wrap gap-sm">
                    <template x-if="detail">
                        <span class="inline-block px-2 py-1 text-label-md font-label-md rounded border"
                              :class="detail.status_class" x-text="detail.status_label"></span>
                    </template>
                    <template x-if="detail">
                        <span class="inline-flex items-center gap-xs px-2 py-1 text-label-md font-label-md rounded-full font-bold"
                              :class="detail.priority_class">
                            <span class="w-1.5 h-1.5 rounded-full" :class="detail.priority_dot"></span>
                            <span x-text="detail.priority_label"></span>
                        </span>
                    </template>
                    <template x-if="detail">
                        <span class="inline-flex items-center gap-xs px-2 py-1 text-label-md font-label-md rounded border"
                              :class="detail.category_class" x-text="detail.category_label"></span>
                    </template>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-2 gap-md">
                    <div>
                        <p class="text-label-md font-label-md text-secondary mb-xs">Pelapor</p>
                        <p class="text-body-sm font-body-sm text-on-surface" x-text="detail ? detail.reporter : ''"></p>
                    </div>
                    <div>
                        <p class="text-label-md font-label-md text-secondary mb-xs">Waktu Lapor</p>
                        <p class="text-body-sm font-body-sm text-on-surface" x-text="detail ? detail.reported_at : ''"></p>
                    </div>
                    <div>
                        <p class="text-label-md font-label-md text-secondary mb-xs">Lokasi</p>
                        <p class="text-body-sm font-body-sm text-on-surface" x-text="detail ? detail.area : ''"></p>
                    </div>
                    <div>
                        <p class="text-label-md font-label-md text-secondary mb-xs">Koordinat GPS</p>
                        <p class="text-body-sm font-body-sm text-on-surface font-mono" x-text="detail ? detail.coords : '-'"></p>
                    </div>
                </div>

                <!-- Deskripsi -->
                <div>
                    <p class="text-label-md font-label-md text-secondary mb-xs">Deskripsi</p>
                    <p class="text-body-sm font-body-sm text-on-surface bg-surface-container-low p-sm rounded-lg border border-outline-variant"
                       x-text="detail && detail.description ? detail.description : 'Tidak ada deskripsi.'"></p>
                </div>

                <!-- Bukti Foto -->
                <div x-show="detail && detail.photo_url">
                    <p class="text-label-md font-label-md text-secondary mb-xs">Bukti Foto</p>
                    <template x-if="detail && detail.photo_url">
                        <img :src="detail.photo_url" alt="Bukti foto insiden"
                             class="w-48 h-48 object-cover rounded-lg border border-outline-variant">
                    </template>
                </div>

                <!-- Timeline / Lifecycle -->
                <div>
                    <p class="text-label-md font-label-md text-secondary mb-sm">Riwayat Status</p>
                    <div class="space-y-sm">
                        <template x-if="detail && detail.history.length > 0">
                            <template x-for="(h, i) in detail.history" :key="i">
                                <div class="flex items-start gap-sm">
                                    <div class="w-2 h-2 rounded-full bg-blue-500 mt-1.5 flex-shrink-0"></div>
                                    <div>
                                        <p class="text-body-sm font-body-sm text-on-surface font-semibold" x-text="h.status"></p>
                                        <p class="text-label-md font-label-md text-secondary" x-text="h.meta"></p>
                                    </div>
                                </div>
                            </template>
                        </template>
                        <template x-if="detail && detail.history.length === 0">
                            <div class="flex items-start gap-sm opacity-60">
                                <div class="w-2 h-2 rounded-full bg-outline-variant mt-1.5 flex-shrink-0"></div>
                                <div>
                                    <p class="text-body-sm font-body-sm text-on-surface font-semibold">New — Laporan Dibuat</p>
                                    <p class="text-label-md font-label-md text-secondary" x-text="detail ? detail.reported_at + ' oleh ' + detail.reporter : ''"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ══ Alpine.js — Data halaman & modal detail ════════════════════════════ --}}
<script>
function incidentPage() {
    return {
        incidents: @json($incidentsJson),
        detail: null,

        init() {},

        openDetail(id) {
            const item = this.incidents.find(x => x.id === id);
            if (item) this.detail = item;
        },
    }
}
</script>

</x-app-layout>
