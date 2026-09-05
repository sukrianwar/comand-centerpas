<x-member-layout>
<div class="mb-xl border-b border-outline-variant pb-md">
    <h2 class="text-headline-md md:text-display font-bold text-on-surface mb-2">Profil Pengguna</h2>
    <p class="text-body-lg text-secondary">Kelola informasi akun dan tinjau statistik kinerja Anda.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-lg">
    
    <!-- Left Column: Profile Info -->
    <div class="lg:col-span-1 space-y-lg">
        <!-- Profile Card -->
        <div class="bg-surface border border-outline-variant rounded-3xl p-xl text-center shadow-lg relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-b from-primary/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            
            <div class="relative z-10">
                <div class="w-32 h-32 mx-auto bg-gradient-to-br from-primary to-primary-container rounded-full border-4 border-surface shadow-xl flex items-center justify-center text-on-primary font-bold text-[48px] tracking-wider mb-lg">
                    AR
                </div>
                
                <h3 class="text-headline-md md:text-display font-bold text-on-surface mb-1">Ahmad Riyadi</h3>
                <p class="text-title-md text-primary font-medium mb-lg">Security Guard</p>
                
                <div class="space-y-md text-left bg-surface-container-lowest p-md rounded-2xl border border-outline-variant">
                    <div class="flex items-center gap-md">
                        <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
                            <span class="material-symbols-outlined">badge</span>
                        </div>
                        <div>
                            <p class="text-label-md text-secondary">ID Karyawan (NIK)</p>
                            <p class="text-body-lg font-bold text-on-surface">202601001</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-md">
                        <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
                            <span class="material-symbols-outlined">mail</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-label-md text-secondary">Email</p>
                            <p class="text-body-lg font-bold text-on-surface truncate">ahmad.r@sentinel.com</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-md">
                        <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
                            <span class="material-symbols-outlined">work</span>
                        </div>
                        <div>
                            <p class="text-label-md text-secondary">Bergabung Sejak</p>
                            <p class="text-body-lg font-bold text-on-surface">15 Jan 2024</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Stats & Settings -->
    <div class="lg:col-span-2 space-y-lg">
        
        <!-- Statistik Kinerja Bulanan -->
        <div class="bg-surface border border-outline-variant rounded-3xl p-lg shadow-sm">
            <h3 class="text-title-lg font-bold text-on-surface mb-lg">Kinerja Bulan Ini</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-md">
                <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-lg text-center hover:border-primary hover:shadow-md transition-all">
                    <div class="w-12 h-12 mx-auto bg-primary/10 text-primary rounded-full flex items-center justify-center mb-sm">
                        <span class="material-symbols-outlined text-[24px]">route</span>
                    </div>
                    <p class="text-headline-md md:text-display font-bold text-on-surface mb-1">56</p>
                    <p class="text-body-md text-secondary font-medium">Total Patroli</p>
                </div>
                <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-lg text-center hover:border-emerald-500 hover:shadow-md transition-all">
                    <div class="w-12 h-12 mx-auto bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mb-sm">
                        <span class="material-symbols-outlined text-[24px]">verified</span>
                    </div>
                    <p class="text-headline-md md:text-display font-bold text-emerald-600 mb-1">98%</p>
                    <p class="text-body-md text-secondary font-medium">Compliance</p>
                </div>
                <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-lg text-center hover:border-amber-500 hover:shadow-md transition-all">
                    <div class="w-12 h-12 mx-auto bg-amber-50 text-amber-600 rounded-full flex items-center justify-center mb-sm">
                        <span class="material-symbols-outlined text-[24px]">warning</span>
                    </div>
                    <p class="text-headline-md md:text-display font-bold text-amber-600 mb-1">2</p>
                    <p class="text-body-md text-secondary font-medium">CP Terlewat</p>
                </div>
                <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-lg text-center hover:border-blue-500 hover:shadow-md transition-all">
                    <div class="w-12 h-12 mx-auto bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-sm">
                        <span class="material-symbols-outlined text-[24px]">report</span>
                    </div>
                    <p class="text-headline-md md:text-display font-bold text-blue-600 mb-1">3</p>
                    <p class="text-body-md text-secondary font-medium">Laporan Insiden</p>
                </div>
            </div>
        </div>

        <!-- Account Actions -->
        <div class="bg-surface border border-outline-variant rounded-3xl p-lg shadow-sm">
            <h3 class="text-title-lg font-bold text-on-surface mb-lg">Pengaturan Akun</h3>
            <div class="space-y-md">
                <button class="w-full bg-surface-container-lowest border border-outline-variant rounded-2xl p-lg flex items-center gap-md hover:bg-surface-container hover:shadow-sm transition-all text-left group">
                    <div class="w-12 h-12 rounded-full bg-surface-variant flex items-center justify-center text-on-surface-variant group-hover:bg-primary group-hover:text-on-primary transition-colors">
                        <span class="material-symbols-outlined text-[24px]">lock</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-title-md font-bold text-on-surface group-hover:text-primary transition-colors">Ubah Password</p>
                        <p class="text-body-md text-secondary mt-1">Perbarui kata sandi untuk keamanan akun Anda</p>
                    </div>
                    <span class="material-symbols-outlined text-secondary group-hover:text-primary group-hover:translate-x-1 transition-all text-[28px]">chevron_right</span>
                </button>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full bg-surface-container-lowest border border-red-200 rounded-2xl p-lg flex items-center gap-md hover:bg-red-50 hover:border-red-300 hover:shadow-sm transition-all text-left group">
                        <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-red-500 group-hover:bg-red-500 group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[24px]">logout</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-title-md font-bold text-red-600">Logout</p>
                            <p class="text-body-md text-secondary mt-1">Keluar dari sesi Anda di perangkat ini</p>
                        </div>
                        <span class="material-symbols-outlined text-red-300 group-hover:text-red-500 group-hover:translate-x-1 transition-all text-[28px]">chevron_right</span>
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
</x-member-layout>
