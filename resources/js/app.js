import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// ── Smooth navigation untuk aplikasi multi-page (tanpa SPA) ──────────
(function () {
    const prefetched = new Set();
    const MAX_PREFETCH = 20;

    function resolveUrl(link) {
        if (!link || !link.href) {
            return null;
        }

        try {
            return new URL(link.href, window.location.href);
        } catch (error) {
            return null;
        }
    }

    function prefetch(link) {
        const url = resolveUrl(link);

        if (!url || url.origin !== window.location.origin) {
            return;
        }

        if (link.target && link.target !== '_self') {
            return;
        }

        if (link.hasAttribute('download') || link.dataset.noPrefetch !== undefined) {
            return;
        }

        if (url.href === window.location.href || prefetched.has(url.href)) {
            return;
        }

        if (prefetched.size >= MAX_PREFETCH) {
            return;
        }

        prefetched.add(url.href);

        const element = document.createElement('link');
        element.rel = 'prefetch';
        element.as = 'document';
        element.href = url.href;
        document.head.appendChild(element);
    }

    function prefetchTarget(event) {
        const link = event.target.closest('#sidebar-nav a[href], a[data-prefetch]');

        if (link) {
            prefetch(link);
        }
    }

    document.addEventListener('mouseover', prefetchTarget, { passive: true });
    document.addEventListener('focusin', prefetchTarget);

    // Progress bar & highlight instan saat link internal diklik.
    const progress = document.getElementById('cc-progress');
    let progressTimer = null;
    let resetTimer = null;

    function resetProgress() {
        if (!progress) {
            return;
        }

        window.clearTimeout(progressTimer);
        window.clearTimeout(resetTimer);
        progress.classList.remove('cc-active');
        progress.style.width = '0';
    }

    function startProgress() {
        if (!progress) {
            return;
        }

        window.clearTimeout(progressTimer);
        progress.classList.add('cc-active');
        progress.style.width = '10%';

        progressTimer = window.setTimeout(() => {
            progress.style.width = '80%';
        }, 80);

        // Jaga-jaga bila navigasi dibatalkan/gagal.
        resetTimer = window.setTimeout(resetProgress, 8000);
    }

    window.addEventListener('pageshow', resetProgress);

    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[href]');

        if (!link) {
            return;
        }

        if (event.defaultPrevented || event.button !== 0) {
            return;
        }

        if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
            return;
        }

        if (link.target && link.target !== '_self') {
            return;
        }

        if (link.hasAttribute('download')) {
            return;
        }

        const url = resolveUrl(link);

        if (!url || url.origin !== window.location.origin) {
            return;
        }

        // Abaikan link ke halaman yang sama (termasuk anchor/hash).
        if (url.pathname === window.location.pathname && url.search === window.location.search) {
            return;
        }

        startProgress();

        if (link.closest('#sidebar-nav')) {
            document.querySelectorAll('#sidebar-nav .cc-nav-pending').forEach((item) => {
                item.classList.remove('cc-nav-pending');
            });

            link.classList.add('cc-nav-pending');
        }
    });
})();

// ── Chart dashboard (dimuat hanya di halaman yang punya canvas) ──────
if (
    document.getElementById('patrol-incident-chart') ||
    document.getElementById('route-completion-chart')
) {
    import('./dashboard-chart').then(({ initDashboardCharts }) => {
        initDashboardCharts();
    });
}
