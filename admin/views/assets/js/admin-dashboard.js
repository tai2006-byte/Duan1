(() => {
    const payload = document.getElementById('dashboardChartData');
    if (!payload || !window.Chart) return;
    const data = JSON.parse(payload.textContent);
    const format = new Intl.NumberFormat('vi-VN');
    Chart.defaults.global.defaultFontFamily = 'system-ui, -apple-system, Segoe UI, sans-serif';
    Chart.defaults.global.defaultFontColor = '#738096';
    Chart.defaults.global.defaultFontSize = 10.4;
    const canvas = document.getElementById('dashboardRevenue');
    const context = canvas.getContext('2d');
    const fill = context.createLinearGradient(0, 0, 0, 245);
    fill.addColorStop(0, 'rgba(55,114,255,.24)');
    fill.addColorStop(1, 'rgba(55,114,255,.02)');
    new Chart(context, {
        type: 'line',
        data: { labels: data.labels, datasets: [{ label: 'Doanh thu', data: data.revenue, borderColor: '#3772ff', backgroundColor: fill, borderWidth: 2, pointRadius: data.labels.length > 35 ? 0 : 2, pointHoverRadius: 4, pointBackgroundColor: '#fff', pointBorderWidth: 1.5, lineTension: 0, fill: true }] },
        options: {
            responsive: true, maintainAspectRatio: false, legend: { display: false },
            animation: { duration: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 300 },
            scales: {
                xAxes: [{ gridLines: { display: false }, ticks: { maxTicksLimit: 9, maxRotation: 0 } }],
                yAxes: [{ gridLines: { color: '#edf0f5', drawBorder: false }, ticks: { beginAtZero: true, maxTicksLimit: 5, precision: 0, callback: value => format.format(value) } }]
            },
            tooltips: { displayColors: false, callbacks: { label: item => format.format(item.yLabel) + ' đ' } }
        }
    });
    const total = data.statuses.reduce((sum, value) => sum + value, 0);
    new Chart(document.getElementById('dashboardStatuses').getContext('2d'), {
        type: 'doughnut',
        data: { labels: total ? data.statusLabels : ['Chưa có đơn hàng'], datasets: [{ data: total ? data.statuses : [1], backgroundColor: total ? ['#3b73ff', '#9268d8', '#f5ad19', '#4dbb59', '#ed4c4c'] : ['#edf0f5'], borderColor: '#fff', borderWidth: 2 }] },
        options: { responsive: true, maintainAspectRatio: false, cutoutPercentage: 72, legend: { display: false }, animation: { duration: 0 }, tooltips: { enabled: !!total, callbacks: { label: (item, chart) => chart.labels[item.index] + ': ' + format.format(chart.datasets[0].data[item.index]) + ' đơn' } } }
    });
})();
