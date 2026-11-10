import {
    BarController,
    BarElement,
    CategoryScale,
    Chart,
    Filler,
    Legend,
    LineController,
    LineElement,
    LinearScale,
    PointElement,
    Tooltip,
} from 'chart.js';

Chart.register(
    BarController,
    BarElement,
    LineController,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    Tooltip,
    Legend,
    Filler,
);

const COLORS = {
    primary: '#0a1422',
    incident: '#ef4444',
    incidentSoft: 'rgba(239, 68, 68, 0.12)',
    completed: '#10b981',
    incomplete: '#f59e0b',
    text: '#6b7280',
    grid: 'rgba(15, 23, 42, 0.07)',
};

const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

function readJson(id) {
    const element = document.getElementById(id);

    if (!element) {
        return [];
    }

    try {
        return JSON.parse(element.textContent || '[]');
    } catch (error) {
        return [];
    }
}

function baseOptions() {
    return {
        responsive: true,
        maintainAspectRatio: false,
        animation: prefersReducedMotion ? false : { duration: 500 },
        interaction: {
            mode: 'index',
            intersect: false,
        },
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    usePointStyle: true,
                    boxWidth: 8,
                    padding: 16,
                    color: COLORS.text,
                    font: { size: 11, family: 'Inter, sans-serif' },
                },
            },
            tooltip: {
                backgroundColor: COLORS.primary,
                padding: 10,
                cornerRadius: 8,
                titleFont: { size: 12, family: 'Inter, sans-serif' },
                bodyFont: { size: 12, family: 'Inter, sans-serif' },
            },
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: {
                    color: COLORS.text,
                    font: { size: 11, family: 'Inter, sans-serif' },
                },
            },
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0,
                    color: COLORS.text,
                    font: { size: 11, family: 'Inter, sans-serif' },
                },
                grid: { color: COLORS.grid },
            },
        },
    };
}

function destroyExisting(canvas) {
    const existing = Chart.getChart(canvas);

    if (existing) {
        existing.destroy();
    }
}

function initTrendChart() {
    const canvas = document.getElementById('patrol-incident-chart');

    if (!canvas) {
        return;
    }

    const trend = readJson('cc-trend-data');

    destroyExisting(canvas);

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels: trend.map((item) => item.label),
            datasets: [
                {
                    type: 'bar',
                    label: 'Scan Patroli',
                    data: trend.map((item) => item.scans),
                    backgroundColor: COLORS.primary,
                    borderRadius: 6,
                    maxBarThickness: 28,
                    order: 2,
                },
                {
                    type: 'line',
                    label: 'Insiden',
                    data: trend.map((item) => item.incidents),
                    borderColor: COLORS.incident,
                    backgroundColor: COLORS.incidentSoft,
                    pointBackgroundColor: COLORS.incident,
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    tension: 0.35,
                    fill: true,
                    order: 1,
                },
            ],
        },
        options: baseOptions(),
    });
}

function initCompletionChart() {
    const canvas = document.getElementById('route-completion-chart');

    if (!canvas) {
        return;
    }

    const completion = readJson('cc-completion-data');
    const percentByIndex = completion.map((item) => item.percent);

    destroyExisting(canvas);

    const options = baseOptions();

    options.scales.x.stacked = true;
    options.scales.y.stacked = true;
    options.plugins.tooltip.callbacks = {
        label: (context) => `${context.dataset.label}: ${context.parsed.y} rute`,
        footer: (items) => {
            const percent = percentByIndex[items[0]?.dataIndex] ?? 0;

            return `Penyelesaian: ${percent}%`;
        },
    };

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels: completion.map((item) => item.label),
            datasets: [
                {
                    label: 'Selesai 100%',
                    data: completion.map((item) => item.completed),
                    backgroundColor: COLORS.completed,
                    borderRadius: 6,
                    maxBarThickness: 28,
                    stack: 'routes',
                },
                {
                    label: 'Belum Selesai',
                    data: completion.map((item) => item.incomplete),
                    backgroundColor: COLORS.incomplete,
                    borderRadius: 6,
                    maxBarThickness: 28,
                    stack: 'routes',
                },
            ],
        },
        options,
    });
}

export function initDashboardCharts() {
    initTrendChart();
    initCompletionChart();
}
