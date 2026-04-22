import Chart from 'chart.js/auto';

let instance = null;

export function initCompareChart() {
    const canvas = document.getElementById('compareChart');
    const dataEl = document.getElementById('compare-chart-data');
    if (!canvas || !dataEl) return;

    const data = JSON.parse(dataEl.textContent);

    if (instance) {
        instance.destroy();
        instance = null;
    }

    if (!data.labels?.length || !data.datasets?.length) return;

    instance = new Chart(canvas, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: data.datasets.map(ds => ({
                ...ds,
                borderDash: ds.borderDash ?? [],
            })),
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        color: '#737373',
                        usePointStyle: true,
                        pointStyleWidth: 8,
                        padding: 16,
                        font: { size: 11 },
                    },
                },
                tooltip: {
                    callbacks: {
                        label: ctx => `${ctx.dataset.label}: ${parseFloat(ctx.raw).toFixed(2)} €`,
                    },
                },
            },
            scales: {
                x: {
                    ticks: { color: '#a3a3a3' },
                    grid: { color: 'rgba(115,115,115,0.2)' },
                },
                y: {
                    ticks: {
                        color: '#a3a3a3',
                        callback: v => `${parseFloat(v).toFixed(2)} €`,
                    },
                    grid: { color: 'rgba(115,115,115,0.2)' },
                },
            },
        },
    });
}

