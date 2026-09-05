<x-app-layout>
<!-- Header Section -->
<div class="flex flex-col md:flex-row md:items-end justify-between mb-lg gap-md">
    <div>
        <p class="text-body-md font-body-md text-secondary mb-xs">Data Master</p>
        <h2 class="text-headline-lg font-headline-lg text-on-surface">Data Master Shift</h2>
    </div>
    <div class="flex gap-sm">
        <button class="bg-primary text-on-primary py-sm px-md rounded-xl hover:bg-surface-tint transition-colors flex items-center gap-xs text-body-sm font-body-sm font-semibold">
            <span class="material-symbols-outlined text-[18px]">add_alarm</span> Tambah Shift Baru
        </button>
    </div>
</div>

<!-- Stats Summary Cards -->
<div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-4 gap-md mb-xl">
    <!-- Total Shift -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-md flex flex-col">
        <div class="flex items-center gap-xs mb-sm">
            <span class="material-symbols-outlined text-[18px] text-primary">schedule</span>
            <p class="text-body-sm font-body-sm text-secondary">Total Shift</p>
        </div>
        <p class="text-headline-lg font-headline-lg text-on-surface">3</p>
    </div>
    <!-- Total Durasi Rata-rata -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-md flex flex-col">
        <div class="flex items-center gap-xs mb-sm">
            <span class="material-symbols-outlined text-[18px] text-secondary">update</span>
            <p class="text-body-sm font-body-sm text-secondary">Durasi Rata-rata</p>
        </div>
        <p class="text-headline-lg font-headline-lg text-on-surface">8 <span class="text-title-md font-normal text-secondary">Jam</span></p>
    </div>
    <!-- Aktif -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-md flex flex-col">
        <div class="flex items-center gap-xs mb-sm">
            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
            <p class="text-body-sm font-body-sm text-secondary">Shift Aktif</p>
        </div>
        <p class="text-headline-lg font-headline-lg text-on-surface">3</p>
    </div>
    <!-- Nonaktif -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-md flex flex-col">
        <div class="flex items-center gap-xs mb-sm">
            <div class="w-2 h-2 rounded-full bg-slate-400"></div>
            <p class="text-body-sm font-body-sm text-secondary">Nonaktif</p>
        </div>
        <p class="text-headline-lg font-headline-lg text-on-surface">0</p>
    </div>
</div>

<!-- Shifts Table -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-outline-variant text-label-md font-label-md text-secondary bg-surface-bright">
                    <th class="py-sm px-md font-medium uppercase tracking-wider">Nama Shift</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider">Jam Mulai</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider">Jam Selesai</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider">Durasi (Jam)</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider">Status</th>
                    <th class="py-sm px-md font-medium uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-body-sm font-body-sm text-on-surface">
                <!-- Row 1 -->
                <tr class="border-b border-outline-variant hover:bg-surface-container-low transition-colors group">
                    <td class="py-sm px-md">
                        <div class="flex items-center gap-sm">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-50 text-amber-600 border border-amber-200">
                                <span class="material-symbols-outlined text-[16px]">light_mode</span>
                            </span>
                            <div class="flex flex-col">
                                <span class="font-semibold text-primary">Shift Pagi</span>
                                <span class="text-secondary text-label-md font-label-md">Kode: SFT-1</span>
                            </div>
                        </div>
                    </td>
                    <td class="py-sm px-md font-semibold text-on-surface">07:00 WIB</td>
                    <td class="py-sm px-md font-semibold text-on-surface">15:00 WIB</td>
                    <td class="py-sm px-md">
                        <span class="inline-block px-2 py-1 bg-surface-container-high text-on-surface text-label-md font-bold rounded border border-outline-variant">
                            8 Jam
                        </span>
                    </td>
                    <td class="py-sm px-md">
                        <span class="inline-flex items-center gap-xs px-2 py-1 bg-emerald-50 text-emerald-700 text-label-md font-label-md rounded-full border border-emerald-200">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Aktif
                        </span>
                    </td>
                    <td class="py-sm px-md text-right whitespace-nowrap">
                        <button class="p-xs text-on-surface-variant hover:bg-surface-container rounded-full transition-colors" title="Edit Shift">
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </button>
                        <button class="p-xs text-red-500 hover:bg-red-50 rounded-full transition-colors" title="Hapus">
                            <span class="material-symbols-outlined text-[20px]">delete</span>
                        </button>
                    </td>
                </tr>

                <!-- Row 2 -->
                <tr class="border-b border-outline-variant bg-[#F9FAFB] hover:bg-surface-container-low transition-colors group">
                    <td class="py-sm px-md">
                        <div class="flex items-center gap-sm">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-50 text-blue-600 border border-blue-200">
                                <span class="material-symbols-outlined text-[16px]">wb_twilight</span>
                            </span>
                            <div class="flex flex-col">
                                <span class="font-semibold text-primary">Shift Siang</span>
                                <span class="text-secondary text-label-md font-label-md">Kode: SFT-2</span>
                            </div>
                        </div>
                    </td>
                    <td class="py-sm px-md font-semibold text-on-surface">15:00 WIB</td>
                    <td class="py-sm px-md font-semibold text-on-surface">23:00 WIB</td>
                    <td class="py-sm px-md">
                        <span class="inline-block px-2 py-1 bg-surface-container-high text-on-surface text-label-md font-bold rounded border border-outline-variant">
                            8 Jam
                        </span>
                    </td>
                    <td class="py-sm px-md">
                        <span class="inline-flex items-center gap-xs px-2 py-1 bg-emerald-50 text-emerald-700 text-label-md font-label-md rounded-full border border-emerald-200">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Aktif
                        </span>
                    </td>
                    <td class="py-sm px-md text-right whitespace-nowrap">
                        <button class="p-xs text-on-surface-variant hover:bg-surface-container rounded-full transition-colors" title="Edit Shift">
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </button>
                        <button class="p-xs text-red-500 hover:bg-red-50 rounded-full transition-colors" title="Hapus">
                            <span class="material-symbols-outlined text-[20px]">delete</span>
                        </button>
                    </td>
                </tr>

                <!-- Row 3 -->
                <tr class="border-b border-outline-variant hover:bg-surface-container-low transition-colors group">
                    <td class="py-sm px-md">
                        <div class="flex items-center gap-sm">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 border border-indigo-200">
                                <span class="material-symbols-outlined text-[16px]">dark_mode</span>
                            </span>
                            <div class="flex flex-col">
                                <span class="font-semibold text-primary">Shift Malam</span>
                                <span class="text-secondary text-label-md font-label-md">Kode: SFT-3</span>
                            </div>
                        </div>
                    </td>
                    <td class="py-sm px-md font-semibold text-on-surface">23:00 WIB</td>
                    <td class="py-sm px-md font-semibold text-on-surface">07:00 WIB</td>
                    <td class="py-sm px-md">
                        <span class="inline-block px-2 py-1 bg-surface-container-high text-on-surface text-label-md font-bold rounded border border-outline-variant">
                            8 Jam
                        </span>
                    </td>
                    <td class="py-sm px-md">
                        <span class="inline-flex items-center gap-xs px-2 py-1 bg-emerald-50 text-emerald-700 text-label-md font-label-md rounded-full border border-emerald-200">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Aktif
                        </span>
                    </td>
                    <td class="py-sm px-md text-right whitespace-nowrap">
                        <button class="p-xs text-on-surface-variant hover:bg-surface-container rounded-full transition-colors" title="Edit Shift">
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </button>
                        <button class="p-xs text-red-500 hover:bg-red-50 rounded-full transition-colors" title="Hapus">
                            <span class="material-symbols-outlined text-[20px]">delete</span>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
</x-app-layout>
