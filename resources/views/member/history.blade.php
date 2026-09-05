<x-member-layout>
<div class="mb-xl border-b border-outline-variant pb-md flex flex-col md:flex-row md:items-end justify-between gap-md">
    <div>
        <h2 class="text-headline-md md:text-display font-bold text-on-surface mb-2">Riwayat Patroli</h2>
        <p class="text-body-lg text-secondary">Catatan komprehensif seluruh aktivitas patroli Anda.</p>
    </div>
    
    <!-- Filter -->
    <div class="flex items-center gap-sm">
        <div class="relative">
            <span class="material-symbols-outlined absolute left-md top-1/2 -translate-y-1/2 text-secondary pointer-events-none">calendar_today</span>
            <select class="appearance-none bg-surface border border-outline-variant rounded-2xl py-md pl-[48px] pr-xl text-body-lg text-on-surface font-medium outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 shadow-sm cursor-pointer hover:bg-surface-container-lowest transition-colors">
                <option>Hari Ini</option>
                <option>7 Hari Terakhir</option>
                <option>30 Hari Terakhir</option>
                <option>Bulan Ini</option>
            </select>
            <span class="material-symbols-outlined absolute right-md top-1/2 -translate-y-1/2 text-secondary pointer-events-none">expand_more</span>
        </div>
    </div>
</div>

<!-- Desktop Table View (Hidden on very small screens, visible on md+) -->
<div class="hidden md:block bg-surface border border-outline-variant rounded-3xl overflow-hidden shadow-sm">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-surface-container-lowest border-b border-outline-variant">
                <th class="py-md px-lg text-label-md uppercase tracking-wider text-secondary font-bold">Tanggal & Waktu</th>
                <th class="py-md px-lg text-label-md uppercase tracking-wider text-secondary font-bold">Rute Patroli</th>
                <th class="py-md px-lg text-label-md uppercase tracking-wider text-secondary font-bold">Area</th>
                <th class="py-md px-lg text-label-md uppercase tracking-wider text-secondary font-bold text-center">Progress</th>
                <th class="py-md px-lg text-label-md uppercase tracking-wider text-secondary font-bold text-right">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant">
            <!-- Row 1 -->
            <tr class="hover:bg-surface-container-lowest transition-colors">
                <td class="py-lg px-lg">
                    <p class="text-title-md font-bold text-on-surface">22 Jul 2026</p>
                    <p class="text-body-md text-secondary flex items-center gap-xs mt-1">
                        <span class="material-symbols-outlined text-[16px]">schedule</span> 07:00 – 07:52 <span class="text-outline">|</span> 52 Menit
                    </p>
                </td>
                <td class="py-lg px-lg">
                    <p class="text-title-md font-bold text-on-surface">Rute Utama Pagi</p>
                </td>
                <td class="py-lg px-lg">
                    <p class="text-body-lg text-secondary flex items-center gap-xs">
                        <span class="material-symbols-outlined text-[20px]">location_on</span> Gedung Utama
                    </p>
                </td>
                <td class="py-lg px-lg text-center">
                    <div class="inline-flex items-center justify-center w-full max-w-[120px] bg-surface-container-high h-2 rounded-full overflow-hidden">
                        <div class="bg-emerald-500 h-full w-full"></div>
                    </div>
                    <p class="text-label-md text-secondary mt-2">12/12 Checkpoint</p>
                </td>
                <td class="py-lg px-lg text-right">
                    <span class="inline-flex items-center gap-sm px-3 py-1.5 bg-emerald-50 text-emerald-700 text-body-md font-bold rounded-full border border-emerald-200">
                        <span class="material-symbols-outlined text-[18px]">check_circle</span> Selesai
                    </span>
                </td>
            </tr>
            
            <!-- Row 2 -->
            <tr class="hover:bg-surface-container-lowest transition-colors">
                <td class="py-lg px-lg">
                    <p class="text-title-md font-bold text-on-surface">21 Jul 2026</p>
                    <p class="text-body-md text-secondary flex items-center gap-xs mt-1">
                        <span class="material-symbols-outlined text-[16px]">schedule</span> 13:00 – 13:38 <span class="text-outline">|</span> 38 Menit
                    </p>
                </td>
                <td class="py-lg px-lg">
                    <p class="text-title-md font-bold text-on-surface">Rute Logistik Siang</p>
                </td>
                <td class="py-lg px-lg">
                    <p class="text-body-lg text-secondary flex items-center gap-xs">
                        <span class="material-symbols-outlined text-[20px]">location_on</span> Gudang Logistik
                    </p>
                </td>
                <td class="py-lg px-lg text-center">
                    <div class="inline-flex items-center justify-center w-full max-w-[120px] bg-surface-container-high h-2 rounded-full overflow-hidden">
                        <div class="bg-emerald-500 h-full w-full"></div>
                    </div>
                    <p class="text-label-md text-secondary mt-2">8/8 Checkpoint</p>
                </td>
                <td class="py-lg px-lg text-right">
                    <span class="inline-flex items-center gap-sm px-3 py-1.5 bg-emerald-50 text-emerald-700 text-body-md font-bold rounded-full border border-emerald-200">
                        <span class="material-symbols-outlined text-[18px]">check_circle</span> Selesai
                    </span>
                </td>
            </tr>

            <!-- Row 3 - Missed -->
            <tr class="hover:bg-surface-container-lowest transition-colors bg-amber-50/20">
                <td class="py-lg px-lg">
                    <p class="text-title-md font-bold text-on-surface">21 Jul 2026</p>
                    <p class="text-body-md text-secondary flex items-center gap-xs mt-1">
                        <span class="material-symbols-outlined text-[16px]">schedule</span> 09:00 – 09:55 <span class="text-outline">|</span> 55 Menit
                    </p>
                </td>
                <td class="py-lg px-lg">
                    <p class="text-title-md font-bold text-on-surface">Rute Utama Pagi</p>
                </td>
                <td class="py-lg px-lg">
                    <p class="text-body-lg text-secondary flex items-center gap-xs">
                        <span class="material-symbols-outlined text-[20px]">location_on</span> Gedung Utama
                    </p>
                </td>
                <td class="py-lg px-lg text-center">
                    <div class="inline-flex items-center justify-center w-full max-w-[120px] bg-surface-container-high h-2 rounded-full overflow-hidden">
                        <div class="bg-amber-500 h-full" style="width: 91%"></div>
                    </div>
                    <p class="text-label-md text-amber-700 font-bold mt-2">11/12 Checkpoint</p>
                </td>
                <td class="py-lg px-lg text-right">
                    <span class="inline-flex items-center gap-sm px-3 py-1.5 bg-amber-50 text-amber-700 text-body-md font-bold rounded-full border border-amber-300 shadow-sm">
                        <span class="material-symbols-outlined text-[18px]">warning</span> 1 Terlewat
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Mobile Card View (Visible only on very small screens) -->
<div class="md:hidden space-y-md">

    <!-- Card 1 -->
    <div class="bg-surface border border-outline-variant rounded-2xl p-lg shadow-sm">
        <div class="flex items-start justify-between gap-md mb-md">
            <div>
                <p class="text-label-md text-secondary font-bold mb-1">22 Jul 2026 • 07:00</p>
                <h4 class="text-title-lg font-bold text-on-surface">Rute Utama Pagi</h4>
            </div>
            <span class="inline-flex items-center gap-xs px-2.5 py-1 bg-emerald-50 text-emerald-700 text-label-md font-bold rounded-full border border-emerald-200">
                Selesai
            </span>
        </div>
        <div class="grid grid-cols-2 gap-md bg-surface-container-lowest p-md rounded-xl border border-outline-variant">
            <div>
                <p class="text-label-sm text-secondary uppercase tracking-wider mb-1">Area</p>
                <p class="text-body-md font-bold">Gedung Utama</p>
            </div>
            <div>
                <p class="text-label-sm text-secondary uppercase tracking-wider mb-1">Waktu</p>
                <p class="text-body-md font-bold">52 Menit</p>
            </div>
            <div class="col-span-2">
                <p class="text-label-sm text-secondary uppercase tracking-wider mb-1">Progress (12/12)</p>
                <div class="w-full bg-surface-container-high h-1.5 rounded-full overflow-hidden">
                    <div class="bg-emerald-500 h-full w-full"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3 (Missed) -->
    <div class="bg-surface border border-amber-200 rounded-2xl p-lg shadow-sm bg-amber-50/10">
        <div class="flex items-start justify-between gap-md mb-md">
            <div>
                <p class="text-label-md text-secondary font-bold mb-1">21 Jul 2026 • 09:00</p>
                <h4 class="text-title-lg font-bold text-on-surface">Rute Utama Pagi</h4>
            </div>
            <span class="inline-flex items-center gap-xs px-2.5 py-1 bg-amber-50 text-amber-700 text-label-md font-bold rounded-full border border-amber-300">
                1 Terlewat
            </span>
        </div>
        <div class="grid grid-cols-2 gap-md bg-white p-md rounded-xl border border-outline-variant shadow-sm">
            <div>
                <p class="text-label-sm text-secondary uppercase tracking-wider mb-1">Area</p>
                <p class="text-body-md font-bold">Gedung Utama</p>
            </div>
            <div>
                <p class="text-label-sm text-secondary uppercase tracking-wider mb-1">Waktu</p>
                <p class="text-body-md font-bold">55 Menit</p>
            </div>
            <div class="col-span-2">
                <p class="text-label-sm text-amber-700 font-bold uppercase tracking-wider mb-1">Progress (11/12)</p>
                <div class="w-full bg-surface-container-high h-1.5 rounded-full overflow-hidden">
                    <div class="bg-amber-500 h-full" style="width: 91%"></div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Pagination / Load More -->
<div class="mt-lg flex justify-center">
    <button class="bg-surface border border-outline-variant hover:border-primary text-primary font-bold py-md px-xl rounded-2xl transition-all hover:shadow-md">
        Muat Lebih Banyak
    </button>
</div>

</x-member-layout>
