import Chart from 'chart.js/auto';

function callback(context) {
    const label = context.dataset.label || '';
    const value = context.raw;

    return `${label}: ${parseFloat(value).toFixed(2)} €`;
}

const chartDefaults = {
    responsive: true,
    maintainAspectRatio: false,
    interaction: {intersect: false, mode: 'index'},
    plugins: {
        tooltip: {callbacks: {label: callback}},
        legend: {labels: {color: '#d4d4d4'}},
    },
    scales: {
        x: {
            ticks: {color: '#a3a3a3'},
            grid: {color: 'rgba(115, 115, 115, 0.2)'},
        },
        y: {
            ticks: {
                color: '#a3a3a3',
                callback: (value) => `${value.toFixed(2)} €`,
            },
            grid: {color: 'rgba(115, 115, 115, 0.2)'},
        },
    },
};

export function initPriceChart() {
    const canvas = document.getElementById('priceChart');
    if (!canvas || canvas.chart) return;

    // console.log(canvas);
    // console.log(canvas.dataset);

    const labels = JSON.parse(canvas.dataset.labels);
    const avgPrices = JSON.parse(canvas.dataset.avg);
    const minPrices = JSON.parse(canvas.dataset.min);

    canvas.chart = new Chart(canvas, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Priemerná cena',
                    data: avgPrices,
                    borderColor: '#a78bfa',
                    backgroundColor: 'rgba(167, 139, 250, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3,
                },
                {
                    label: 'Najnižšia cena',
                    data: minPrices,
                    borderColor: '#34d399',
                    backgroundColor: 'rgba(52, 211, 153, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3,
                },
            ],
        },
        options: chartDefaults,
    });
}
