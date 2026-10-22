@if(! empty($impersonator))
<div class="mb-md flex flex-col gap-sm sm:flex-row sm:items-center sm:justify-between rounded-xl border border-amber-300 bg-amber-50 px-md py-sm text-amber-900 shadow-sm">
    <div class="flex items-start gap-sm">
        <span class="material-symbols-outlined text-[20px] text-amber-600">switch_account</span>
        <div class="text-body-sm font-body-sm">
            <p class="font-semibold">Mode impersonasi aktif</p>
            <p class="text-[12px] text-amber-800">
                Anda login sebagai <span class="font-semibold">{{ auth()->user()?->name }}</span>.
                Sesi impersonasi ini dicatat.
            </p>
        </div>
    </div>

    <form method="POST" action="{{ route('impersonation.stop') }}" class="flex-shrink-0">
        @csrf
        <button type="submit"
                class="inline-flex items-center gap-xs rounded-lg bg-amber-600 px-sm py-xs text-[12px] font-semibold text-white transition-colors hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">
            <span class="material-symbols-outlined text-[16px]">undo</span>
            Kembali ke {{ $impersonator->name }}
        </button>
    </form>
</div>
@endif
