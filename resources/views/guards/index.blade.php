<x-app-layout>
<!-- Header Section -->
<div class="flex flex-col md:flex-row md:items-end justify-between mb-lg gap-md">
    <div>
        <p class="text-body-md font-body-md text-secondary mb-xs">Data Master</p>
        <h2 class="text-headline-lg font-headline-lg text-on-surface">Data Petugas & Pengguna</h2>
    </div>
    <div class="flex gap-sm">
        <button class="bg-primary text-on-primary py-sm px-md rounded-xl hover:bg-surface-tint transition-colors flex items-center gap-xs text-body-sm font-body-sm font-semibold">
            <span class="material-symbols-outlined text-[18px]">person_add</span> Tambah Pengguna
        </button>
    </div>
</div>

<!-- Stats Summary Cards -->
<div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-4 gap-md mb-xl">
    <!-- Total Users -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-md flex flex-col">
        <div class="flex items-center gap-xs mb-sm">
            <span class="material-symbols-outlined text-[18px] text-primary">group</span>
            <p class="text-body-sm font-body-sm text-secondary">Total Pengguna</p>
        </div>
        <p class="text-headline-lg font-headline-lg text-on-surface">45</p>
    </div>
    <!-- Security Guard -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-md flex flex-col">
        <div class="flex items-center gap-xs mb-sm">
            <span class="material-symbols-outlined text-[18px] text-secondary">badge</span>
            <p class="text-body-sm font-body-sm text-secondary">Security Guard</p>
        </div>
        <p class="text-headline-lg font-headline-lg text-on-surface">32</p>
    </div>
    <!-- Supervisor/Leader -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-md flex flex-col">
        <div class="flex items-center gap-xs mb-sm">
            <span class="material-symbols-outlined text-[18px] text-secondary">admin_panel_settings</span>
            <p class="text-body-sm font-body-sm text-secondary">Supervisor / Leader</p>
        </div>
        <p class="text-headline-lg font-headline-lg text-on-surface">10</p>
    </div>
    <!-- Akun Aktif -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-md flex flex-col">
        <div class="flex items-center gap-xs mb-sm">
            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
            <p class="text-body-sm font-body-sm text-secondary">Akun Aktif</p>
        </div>
        <p class="text-headline-lg font-headline-lg text-on-surface">42</p>
    </div>
</div>

<!-- Filter & Search Bar -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-md mb-md flex flex-col md:flex-row gap-sm items-stretch md:items-center">
    <!-- Search -->
    <div class="relative flex-1 max-w-md">
        <span class="material-symbols-outlined absolute left-sm top-1/2 -translate-y-1/2 text-secondary text-[20px]">search</span>
        <input class="w-full bg-surface-container-low border border-outline-variant rounded-xl py-xs pl-[36px] pr-sm text-body-sm font-body-sm focus:border-primary-container focus:ring-2 focus:ring-primary-container/20 transition-all outline-none text-on-surface" placeholder="Cari nama, NIK, username..." type="text" id="searchUser">
    </div>
    <!-- Filter Role -->
    <select class="bg-surface-container-low border border-outline-variant rounded-xl py-xs px-sm text-body-sm font-body-sm text-on-surface outline-none focus:border-primary-container" id="filterRole">
        <option value="">Semua Peran (Role)</option>
        <option value="security">Security Guard</option>
        <option value="leader">Team Leader</option>
        <option value="supervisor">Supervisor</option>
        <option value="manager">Manager</option>
        <option value="admin">Admin System</option>
    </select>
    <!-- Filter Status -->
    <select class="bg-surface-container-low border border-outline-variant rounded-xl py-xs px-sm text-body-sm font-body-sm text-on-surface outline-none focus:border-primary-container" id="filterStatus">
        <option value="">Semua Status</option>
        <option value="active">Aktif</option>
        <option value="inactive">Nonaktif/Cuti</option>
    </select>
</div>

<!-- Users Table -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-outline-variant text-label-md font-label-md text-secondary bg-surface-bright">
                    <th class="py-sm px-md font-medium uppercase tracking-wider">Profil Petugas / Pengguna</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider">Peran (Role)</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider">Akun Login</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider">Terakhir Login</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider">Status</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-body-sm font-body-sm text-on-surface">
                <!-- Row 1: Security -->
                <tr class="border-b border-outline-variant hover:bg-surface-container-low transition-colors group">
                    <td class="py-sm px-md">
                        <div class="flex items-center gap-sm">
                            <div class="w-10 h-10 bg-surface-container-highest rounded-full border border-outline-variant flex items-center justify-center text-on-surface font-bold text-label-md">
                                AR
                            </div>
                            <div class="flex flex-col">
                                <span class="font-semibold text-primary">Ahmad Riyadi</span>
                                <span class="text-secondary text-label-md font-label-md">NIK: 202601001</span>
                            </div>
                        </div>
                    </td>
                    <td class="py-sm px-md">
                        <span class="inline-flex items-center gap-xs px-2 py-1 bg-surface-container-high text-on-surface text-label-md font-label-md rounded border border-outline-variant">
                            <span class="material-symbols-outlined text-[14px]">shield</span> Security Guard
                        </span>
                    </td>
                    <td class="py-sm px-md">
                        <div class="flex flex-col">
                            <span class="text-on-surface">ahmad.riyadi</span>
                            <span class="text-secondary text-label-md">ahmad.r@sentinel.com</span>
                        </div>
                    </td>
                    <td class="py-sm px-md text-secondary">22 Jul 2026, 06:45</td>
                    <td class="py-sm px-md">
                        <span class="inline-flex items-center gap-xs px-2 py-1 bg-emerald-50 text-emerald-700 text-label-md font-label-md rounded-full border border-emerald-200">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Aktif
                        </span>
                    </td>
                    <td class="py-sm px-md text-right whitespace-nowrap">
                        <button class="p-xs text-on-surface-variant hover:bg-surface-container rounded-full transition-colors" title="Lihat Profil">
                            <span class="material-symbols-outlined text-[20px]">person</span>
                        </button>
                        <button class="p-xs text-on-surface-variant hover:bg-surface-container rounded-full transition-colors" title="Edit Data">
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </button>
                        <button class="p-xs text-red-500 hover:bg-red-50 rounded-full transition-colors" title="Nonaktifkan">
                            <span class="material-symbols-outlined text-[20px]">block</span>
                        </button>
                    </td>
                </tr>

                <!-- Row 2: Supervisor -->
                <tr class="border-b border-outline-variant bg-[#F9FAFB] hover:bg-surface-container-low transition-colors group">
                    <td class="py-sm px-md">
                        <div class="flex items-center gap-sm">
                            <div class="w-10 h-10 bg-indigo-100 rounded-full border border-indigo-200 flex items-center justify-center text-indigo-700 font-bold text-label-md">
                                HM
                            </div>
                            <div class="flex flex-col">
                                <span class="font-semibold text-primary">Hendra Wijaya</span>
                                <span class="text-secondary text-label-md font-label-md">NIK: 202401015</span>
                            </div>
                        </div>
                    </td>
                    <td class="py-sm px-md">
                        <span class="inline-flex items-center gap-xs px-2 py-1 bg-indigo-50 text-indigo-700 text-label-md font-label-md rounded border border-indigo-200">
                            <span class="material-symbols-outlined text-[14px]">local_police</span> Supervisor
                        </span>
                    </td>
                    <td class="py-sm px-md">
                        <div class="flex flex-col">
                            <span class="text-on-surface">hendra.w</span>
                            <span class="text-secondary text-label-md">hendra.w@sentinel.com</span>
                        </div>
                    </td>
                    <td class="py-sm px-md text-secondary">22 Jul 2026, 08:15</td>
                    <td class="py-sm px-md">
                        <span class="inline-flex items-center gap-xs px-2 py-1 bg-emerald-50 text-emerald-700 text-label-md font-label-md rounded-full border border-emerald-200">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Aktif
                        </span>
                    </td>
                    <td class="py-sm px-md text-right whitespace-nowrap">
                        <button class="p-xs text-on-surface-variant hover:bg-surface-container rounded-full transition-colors" title="Lihat Profil">
                            <span class="material-symbols-outlined text-[20px]">person</span>
                        </button>
                        <button class="p-xs text-on-surface-variant hover:bg-surface-container rounded-full transition-colors" title="Edit Data">
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </button>
                        <button class="p-xs text-red-500 hover:bg-red-50 rounded-full transition-colors" title="Nonaktifkan">
                            <span class="material-symbols-outlined text-[20px]">block</span>
                        </button>
                    </td>
                </tr>

                <!-- Row 3: Leader -->
                <tr class="border-b border-outline-variant hover:bg-surface-container-low transition-colors group">
                    <td class="py-sm px-md">
                        <div class="flex items-center gap-sm">
                            <div class="w-10 h-10 bg-surface-container-highest rounded-full border border-outline-variant flex items-center justify-center text-on-surface font-bold text-label-md">
                                CL
                            </div>
                            <div class="flex flex-col">
                                <span class="font-semibold text-primary">Citra Lestari</span>
                                <span class="text-secondary text-label-md font-label-md">NIK: 202502012</span>
                            </div>
                        </div>
                    </td>
                    <td class="py-sm px-md">
                        <span class="inline-flex items-center gap-xs px-2 py-1 bg-blue-50 text-blue-700 text-label-md font-label-md rounded border border-blue-200">
                            <span class="material-symbols-outlined text-[14px]">star</span> Team Leader
                        </span>
                    </td>
                    <td class="py-sm px-md">
                        <div class="flex flex-col">
                            <span class="text-on-surface">citra.l</span>
                            <span class="text-secondary text-label-md">citra.l@sentinel.com</span>
                        </div>
                    </td>
                    <td class="py-sm px-md text-secondary">22 Jul 2026, 06:50</td>
                    <td class="py-sm px-md">
                        <span class="inline-flex items-center gap-xs px-2 py-1 bg-emerald-50 text-emerald-700 text-label-md font-label-md rounded-full border border-emerald-200">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Aktif
                        </span>
                    </td>
                    <td class="py-sm px-md text-right whitespace-nowrap">
                        <button class="p-xs text-on-surface-variant hover:bg-surface-container rounded-full transition-colors" title="Lihat Profil">
                            <span class="material-symbols-outlined text-[20px]">person</span>
                        </button>
                        <button class="p-xs text-on-surface-variant hover:bg-surface-container rounded-full transition-colors" title="Edit Data">
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </button>
                        <button class="p-xs text-red-500 hover:bg-red-50 rounded-full transition-colors" title="Nonaktifkan">
                            <span class="material-symbols-outlined text-[20px]">block</span>
                        </button>
                    </td>
                </tr>

                <!-- Row 4: Inactive Security -->
                <tr class="border-b border-outline-variant bg-[#F9FAFB] hover:bg-surface-container-low transition-colors group opacity-60">
                    <td class="py-sm px-md">
                        <div class="flex items-center gap-sm">
                            <div class="w-10 h-10 bg-surface-container-highest rounded-full border border-outline-variant flex items-center justify-center text-on-surface font-bold text-label-md">
                                DK
                            </div>
                            <div class="flex flex-col">
                                <span class="font-semibold text-primary">Deni Kurniawan</span>
                                <span class="text-secondary text-label-md font-label-md">NIK: 202601004</span>
                            </div>
                        </div>
                    </td>
                    <td class="py-sm px-md">
                        <span class="inline-flex items-center gap-xs px-2 py-1 bg-surface-container-high text-on-surface text-label-md font-label-md rounded border border-outline-variant">
                            <span class="material-symbols-outlined text-[14px]">shield</span> Security Guard
                        </span>
                    </td>
                    <td class="py-sm px-md">
                        <div class="flex flex-col">
                            <span class="text-on-surface">deni.k</span>
                            <span class="text-secondary text-label-md">deni.k@sentinel.com</span>
                        </div>
                    </td>
                    <td class="py-sm px-md text-secondary">-</td>
                    <td class="py-sm px-md">
                        <span class="inline-flex items-center gap-xs px-2 py-1 bg-slate-100 text-slate-700 text-label-md font-label-md rounded-full border border-slate-200">
                            <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span> Nonaktif / Cuti
                        </span>
                    </td>
                    <td class="py-sm px-md text-right whitespace-nowrap">
                        <button class="p-xs text-on-surface-variant hover:bg-surface-container rounded-full transition-colors" title="Lihat Profil">
                            <span class="material-symbols-outlined text-[20px]">person</span>
                        </button>
                        <button class="p-xs text-on-surface-variant hover:bg-surface-container rounded-full transition-colors" title="Edit Data">
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </button>
                        <button class="p-xs text-emerald-600 hover:bg-emerald-50 rounded-full transition-colors" title="Aktifkan">
                            <span class="material-symbols-outlined text-[20px]">check_circle</span>
                        </button>
                    </td>
                </tr>

                <!-- Row 5: Admin -->
                <tr class="border-b border-outline-variant hover:bg-surface-container-low transition-colors group">
                    <td class="py-sm px-md">
                        <div class="flex items-center gap-sm">
                            <div class="w-10 h-10 bg-amber-100 rounded-full border border-amber-200 flex items-center justify-center text-amber-700 font-bold text-label-md">
                                SYS
                            </div>
                            <div class="flex flex-col">
                                <span class="font-semibold text-primary">System Admin</span>
                                <span class="text-secondary text-label-md font-label-md">NIK: -</span>
                            </div>
                        </div>
                    </td>
                    <td class="py-sm px-md">
                        <span class="inline-flex items-center gap-xs px-2 py-1 bg-amber-50 text-amber-700 text-label-md font-label-md rounded border border-amber-200">
                            <span class="material-symbols-outlined text-[14px]">manage_accounts</span> Admin System
                        </span>
                    </td>
                    <td class="py-sm px-md">
                        <div class="flex flex-col">
                            <span class="text-on-surface">admin</span>
                            <span class="text-secondary text-label-md">admin@sentinel.com</span>
                        </div>
                    </td>
                    <td class="py-sm px-md text-secondary">22 Jul 2026, 09:30</td>
                    <td class="py-sm px-md">
                        <span class="inline-flex items-center gap-xs px-2 py-1 bg-emerald-50 text-emerald-700 text-label-md font-label-md rounded-full border border-emerald-200">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Aktif
                        </span>
                    </td>
                    <td class="py-sm px-md text-right whitespace-nowrap">
                        <button class="p-xs text-on-surface-variant hover:bg-surface-container rounded-full transition-colors" title="Lihat Profil">
                            <span class="material-symbols-outlined text-[20px]">person</span>
                        </button>
                        <button class="p-xs text-on-surface-variant hover:bg-surface-container rounded-full transition-colors" title="Edit Data">
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </button>
                        <!-- Admin can't be deleted generally -->
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between p-md border-t border-outline-variant bg-[#F9FAFB]">
        <p class="text-body-sm font-body-sm text-secondary">Menampilkan 1-5 dari 45 pengguna</p>
        <div class="flex items-center gap-xs">
            <button class="p-xs px-sm border border-outline-variant rounded-lg text-secondary hover:bg-surface-container transition-colors text-body-sm" disabled>
                <span class="material-symbols-outlined text-[18px]">chevron_left</span>
            </button>
            <button class="p-xs px-sm border border-primary bg-primary text-on-primary rounded-lg text-body-sm font-semibold">1</button>
            <button class="p-xs px-sm border border-outline-variant rounded-lg text-secondary hover:bg-surface-container transition-colors text-body-sm">2</button>
            <button class="p-xs px-sm border border-outline-variant rounded-lg text-secondary hover:bg-surface-container transition-colors text-body-sm">3</button>
            <button class="p-xs px-sm border border-outline-variant rounded-lg text-secondary hover:bg-surface-container transition-colors text-body-sm">...</button>
            <button class="p-xs px-sm border border-outline-variant rounded-lg text-secondary hover:bg-surface-container transition-colors text-body-sm">5</button>
            <button class="p-xs px-sm border border-outline-variant rounded-lg text-secondary hover:bg-surface-container transition-colors text-body-sm">
                <span class="material-symbols-outlined text-[18px]">chevron_right</span>
            </button>
        </div>
    </div>
</div>
</x-app-layout>
