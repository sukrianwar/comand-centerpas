<x-app-layout>
<!-- Header Section -->
<div class="flex flex-col md:flex-row md:items-end justify-between mb-lg gap-md">
    <div>
        <p class="text-body-md font-body-md text-secondary mb-xs">Administration</p>
        <h2 class="text-headline-lg font-headline-lg text-on-surface">System Settings</h2>
    </div>
    <div class="flex gap-sm">
        <button class="bg-primary text-on-primary py-sm px-md rounded-xl hover:bg-surface-tint transition-colors flex items-center gap-xs text-body-sm font-body-sm font-semibold shadow-sm">
            <span class="material-symbols-outlined text-[18px]">save</span> Simpan Perubahan
        </button>
    </div>
</div>

<div class="flex flex-col md:flex-row gap-lg">
    <!-- Settings Sidebar Navigation -->
    <div class="w-full md:w-64 flex-shrink-0 space-y-xs">
        <button class="w-full text-left px-md py-sm rounded-xl font-bold bg-primary text-on-primary shadow-sm flex items-center gap-sm">
            <span class="material-symbols-outlined text-[20px]">database</span> Retensi & Storage
        </button>
        <button class="w-full text-left px-md py-sm rounded-xl text-secondary hover:bg-surface-container-high transition-colors flex items-center gap-sm">
            <span class="material-symbols-outlined text-[20px]">security</span> Keamanan (Security)
        </button>
        <button class="w-full text-left px-md py-sm rounded-xl text-secondary hover:bg-surface-container-high transition-colors flex items-center gap-sm">
            <span class="material-symbols-outlined text-[20px]">cloud_sync</span> Backup Database
        </button>
        <button class="w-full text-left px-md py-sm rounded-xl text-secondary hover:bg-surface-container-high transition-colors flex items-center gap-sm">
            <span class="material-symbols-outlined text-[20px]">settings_ethernet</span> Jaringan & Sistem
        </button>
    </div>

    <!-- Main Settings Content -->
    <div class="flex-1 space-y-md">
        
        <!-- Section: Storage Monitoring -->
        <div class="soft-card p-md lg:p-lg">
            <div class="flex items-center gap-sm mb-md">
                <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                    <span class="material-symbols-outlined">dns</span>
                </div>
                <div>
                    <h3 class="text-title-lg font-headline-md text-on-surface">Monitoring Kapasitas Storage</h3>
                    <p class="text-body-sm font-body-sm text-secondary">Status penggunaan penyimpanan server lokal (NAS / Local Disk)</p>
                </div>
            </div>

            <div class="bg-surface-container-low border border-outline-variant rounded-lg p-md">
                <div class="flex justify-between items-end mb-sm">
                    <div>
                        <p class="text-label-md font-label-md text-secondary">Kapasitas Terpakai</p>
                        <p class="text-headline-md font-bold text-on-surface">320 GB <span class="text-body-md font-normal text-secondary">/ 1 TB</span></p>
                    </div>
                    <p class="text-title-md font-bold text-emerald-600">32%</p>
                </div>
                <!-- Progress Bar -->
                <div class="w-full h-3 bg-surface-container-highest rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-500 rounded-full" style="width: 32%"></div>
                </div>
                <div class="flex justify-between mt-xs">
                    <p class="text-label-sm text-secondary">Aman</p>
                    <p class="text-label-sm text-secondary">Kritis (&gt;90%)</p>
                </div>
            </div>
        </div>

        <!-- Section: Retensi Data -->
        <div class="soft-card p-md lg:p-lg">
            <div class="flex items-center gap-sm mb-lg">
                <div class="w-10 h-10 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center">
                    <span class="material-symbols-outlined">auto_delete</span>
                </div>
                <div>
                    <h3 class="text-title-lg font-headline-md text-on-surface">Kebijakan Retensi Data</h3>
                    <p class="text-body-sm font-body-sm text-secondary">Konfigurasi batas waktu penyimpanan data operasional sebelum dihapus otomatis</p>
                </div>
            </div>

            <div class="space-y-lg border-t border-outline-variant pt-lg">
                <!-- Field: Retensi Log Patroli -->
                <div class="flex flex-col md:flex-row gap-md items-start md:items-center justify-between">
                    <div class="max-w-lg">
                        <label class="block text-title-md font-bold text-on-surface mb-xs">Data Patroli & Riwayat Checkpoint</label>
                        <p class="text-body-sm text-secondary">Menyimpan riwayat patroli, timestamp, dan bukti foto checkpoint sesuai dengan standar Kepatuhan Data (PDP).</p>
                    </div>
                    <div class="flex items-center gap-sm">
                        <input type="number" value="90" class="w-24 bg-surface-container-low border border-outline-variant rounded-lg py-sm px-md text-on-surface text-center font-bold focus:border-primary-container outline-none">
                        <span class="text-body-md font-semibold text-secondary">Hari</span>
                    </div>
                </div>

                <!-- Field: Retensi Media/Foto -->
                <div class="flex flex-col md:flex-row gap-md items-start md:items-center justify-between">
                    <div class="max-w-lg">
                        <label class="block text-title-md font-bold text-on-surface mb-xs">Foto & Media Bukti Insiden</label>
                        <p class="text-body-sm text-secondary">Menyimpan file media yang memakan kapasitas besar. Setelah melewati batas waktu, foto akan dikompresi ekstrim atau dihapus.</p>
                    </div>
                    <div class="flex items-center gap-sm">
                        <input type="number" value="180" class="w-24 bg-surface-container-low border border-outline-variant rounded-lg py-sm px-md text-on-surface text-center font-bold focus:border-primary-container outline-none">
                        <span class="text-body-md font-semibold text-secondary">Hari</span>
                    </div>
                </div>

                <!-- Field: Auto Compress -->
                <div class="flex flex-col md:flex-row gap-md items-start md:items-center justify-between">
                    <div class="max-w-lg">
                        <label class="block text-title-md font-bold text-on-surface mb-xs">Kompresi Otomatis Media</label>
                        <p class="text-body-sm text-secondary">Otomatis mengompresi ukuran file foto insiden & patroli saat diunggah ke server untuk menghemat disk NAS.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" value="" class="sr-only peer" checked>
                        <div class="w-11 h-6 bg-surface-container-highest peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                        <span class="ml-3 text-body-md font-medium text-on-surface">Aktif</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Section: Audit Trail -->
        <div class="soft-card p-md lg:p-lg">
            <div class="flex items-center gap-sm mb-lg">
                <div class="w-10 h-10 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center">
                    <span class="material-symbols-outlined">history_edu</span>
                </div>
                <div>
                    <h3 class="text-title-lg font-headline-md text-on-surface">Audit Trail (Log Aktivitas)</h3>
                    <p class="text-body-sm font-body-sm text-secondary">Pengaturan untuk pencatatan riwayat perubahan data master.</p>
                </div>
            </div>

            <div class="space-y-lg border-t border-outline-variant pt-lg">
                <!-- Field: Retensi Audit Log -->
                <div class="flex flex-col md:flex-row gap-md items-start md:items-center justify-between">
                    <div class="max-w-lg">
                        <label class="block text-title-md font-bold text-on-surface mb-xs">Retensi Log Audit</label>
                        <p class="text-body-sm text-secondary">Lama waktu sistem menyimpan log perubahan data master (append-only) sebelum diarsipkan.</p>
                    </div>
                    <div class="flex items-center gap-sm">
                        <select class="w-32 bg-surface-container-low border border-outline-variant rounded-lg py-sm px-md text-on-surface font-bold focus:border-primary-container outline-none">
                            <option value="365">1 Tahun</option>
                            <option value="730">2 Tahun</option>
                            <option value="9999" selected>Selamanya</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
</x-app-layout>
