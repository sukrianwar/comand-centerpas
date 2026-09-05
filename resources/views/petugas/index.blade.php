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
        <p class="text-body-md font-body-md text-secondary mb-xs">Guard Management</p>
        <h2 class="text-headline-lg font-headline-lg text-on-surface">Data Petugas</h2>
    </div>
    <div class="flex gap-sm">
        <a href="{{ route('petugas.create') }}"
           class="bg-primary text-on-primary py-sm px-md rounded-xl hover:bg-surface-tint transition-colors flex items-center gap-xs text-body-sm font-body-sm font-semibold shadow-sm">
            <span class="material-symbols-outlined text-[18px]">add</span> Tambah Petugas Baru
        </a>
    </div>
</div>

{{-- ══ Filter & Search Bar ═════════════════════════════════════════════════ --}}
<form method="GET" action="{{ route('petugas.index') }}"
      class="bg-surface-container-lowest border border-outline-variant rounded-xl p-md mb-md flex flex-col lg:flex-row gap-sm items-stretch lg:items-center">
    
    {{-- Search --}}
    <div class="relative flex-1">
        <span class="material-symbols-outlined absolute left-sm top-1/2 -translate-y-1/2 text-secondary text-[20px]">search</span>
        <input name="search" value="{{ request('search') }}"
               class="w-full bg-surface-container-low border border-outline-variant rounded-xl py-xs pl-[36px] pr-sm text-body-sm font-body-sm focus:border-primary-container focus:ring-2 focus:ring-primary-container/20 transition-all outline-none text-on-surface"
               placeholder="Cari nama atau username..."
               type="text">
    </div>

    {{-- Filter Role --}}
    <div class="w-full lg:w-48">
        <select name="role_id"
                class="w-full bg-surface-container-low border border-outline-variant rounded-xl py-xs px-sm text-body-sm text-on-surface outline-none focus:border-primary focus:ring-2 focus:ring-primary/10">
            <option value="">Semua Role</option>
            @foreach($roles as $role)
                <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                    {{ $role->role_name }}
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
    @if(request()->filled('search') || request()->filled('role_id') || request()->filled('status'))
    <a href="{{ route('petugas.index') }}"
       class="py-xs px-md rounded-xl text-body-sm border border-outline-variant text-secondary hover:bg-surface-container transition-colors whitespace-nowrap text-center">
        Reset
    </a>
    @endif
</form>

{{-- ══ Data Table ══════════════════════════════════════════════════════════ --}}
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden" x-data="statusToggle()">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-outline-variant text-label-md font-label-md text-secondary bg-surface-bright">
                    <th class="py-sm px-md font-medium uppercase tracking-wider w-12"></th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider">Info Petugas</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider">Role</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider">Email</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider text-center">Status Akses</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-body-sm font-body-sm text-on-surface">

                @forelse($users as $user)
                <tr class="border-b border-outline-variant {{ $loop->even ? 'bg-[#F9FAFB]' : '' }} hover:bg-surface-container-low transition-colors group">

                    {{-- Avatar --}}
                    <td class="py-sm pl-md pr-2 text-center">
                        <div class="w-10 h-10 rounded-full bg-primary-container text-on-primary-container flex items-center justify-center font-bold text-label-lg mx-auto border border-primary/20">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                    </td>

                    {{-- Info Petugas --}}
                    <td class="py-sm px-md">
                        <div class="font-bold text-primary">{{ $user->name }}</div>
                        <div class="text-secondary text-[12px] font-mono mt-0.5">{{ '@' . $user->username }}</div>
                    </td>

                    {{-- Role --}}
                    <td class="py-sm px-md">
                        @php
                            $roleColors = [
                                'Admin' => 'bg-red-50 text-red-700 border-red-200',
                                'Supervisor' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'Security Guard' => 'bg-amber-50 text-amber-700 border-amber-200',
                            ];
                            $colorClass = $roleColors[$user->role->role_name] ?? 'bg-surface-container-high text-secondary border-outline-variant';
                        @endphp
                        <span class="inline-flex items-center px-2 py-1 {{ $colorClass }} text-[11px] font-bold rounded border uppercase tracking-wide">
                            {{ $user->role->role_name }}
                        </span>
                    </td>

                    {{-- Email --}}
                    <td class="py-sm px-md text-secondary">
                        {{ $user->email ?: '-' }}
                    </td>

                    {{-- Status Toggle --}}
                    <td class="py-sm px-md text-center">
                        @if(auth()->check() && auth()->id() === $user->id)
                            <span class="inline-flex items-center justify-center px-2 py-1 bg-emerald-50 text-emerald-700 text-[11px] uppercase tracking-wider rounded-full border border-emerald-200 font-bold opacity-60 cursor-not-allowed" title="Anda tidak bisa mengubah status Anda sendiri">
                                Active (You)
                            </span>
                        @else
                            <button @click="toggleStatus({{ $user->id }}, '{{ route('petugas.toggle-status', $user) }}', $event)" 
                                    class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-1"
                                    :class="statuses[{{ $user->id }}] === 'active' ? 'bg-emerald-500' : 'bg-surface-container-highest'"
                                    x-init="statuses[{{ $user->id }}] = '{{ $user->status }}'">
                                <span class="sr-only">Toggle Status</span>
                                <span class="inline-block h-3 w-3 transform rounded-full bg-white transition-transform"
                                      :class="statuses[{{ $user->id }}] === 'active' ? 'translate-x-5' : 'translate-x-1'"></span>
                            </button>
                            <div class="text-[10px] uppercase font-bold mt-1" 
                                 :class="statuses[{{ $user->id }}] === 'active' ? 'text-emerald-600' : 'text-secondary'"
                                 x-text="statuses[{{ $user->id }}]"></div>
                        @endif
                    </td>

                    {{-- Aksi --}}
                    <td class="py-sm px-md text-right whitespace-nowrap">
                        <a href="{{ route('petugas.edit', $user) }}"
                           class="inline-flex p-xs text-on-surface-variant hover:bg-surface-container rounded-full transition-colors" title="Edit">
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </a>

                        @if(!auth()->check() || auth()->id() !== $user->id)
                        <form method="POST"
                              action="{{ route('petugas.destroy', $user) }}"
                              class="inline"
                              onsubmit="return confirm('Hapus petugas \'{{ addslashes($user->name) }}\'? Aksi ini tidak akan menghapus riwayat patroli terkait.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="p-xs text-red-500 hover:bg-red-50 rounded-full transition-colors" title="Hapus">
                                <span class="material-symbols-outlined text-[20px]">delete</span>
                            </button>
                        </form>
                        @endif
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-xl px-md text-center text-secondary">
                        <div class="flex flex-col items-center gap-sm opacity-60">
                            <span class="material-symbols-outlined text-[48px]">group_off</span>
                            <p class="text-body-md font-medium">Belum ada data petugas</p>
                        </div>
                    </td>
                </tr>
                @endforelse

            </tbody>
        </table>
    </div>

    {{-- ── Pagination ──────────────────────────────────────────────────── --}}
    @if($users->hasPages())
    <div class="flex items-center justify-between p-md border-t border-outline-variant bg-[#F9FAFB]">
        <p class="text-body-sm font-body-sm text-secondary">
            Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }}
            dari {{ $users->total() }} petugas
        </p>
        <div class="flex items-center gap-xs">
            {{-- Prev/Next logic omitted for brevity, use simple pagination or same as others --}}
            {{ $users->links() }} 
            {{-- Assuming you have tailwind pagination configured, otherwise just use the links() default --}}
        </div>
    </div>
    @endif
</div>

{{-- Script for Toggle via fetch API --}}
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('statusToggle', () => ({
        statuses: {},
        async toggleStatus(id, url, event) {
            // Optimistic UI update
            const oldStatus = this.statuses[id];
            this.statuses[id] = oldStatus === 'active' ? 'inactive' : 'active';
            
            try {
                const response = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Terjadi kesalahan');
                }
                
                // Confirm status from server
                this.statuses[id] = data.new_status;
                
            } catch (error) {
                // Revert if failed
                this.statuses[id] = oldStatus;
                alert(error.message);
            }
        }
    }));
});
</script>

</x-app-layout>
