<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; margin: 24px; }
        .grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
        .card { border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px; background: #fff; }
        .card h3 { margin: 0 0 8px; font-size: 14px; color: #374151; font-weight: 600; }
        .card .value { font-size: 28px; font-weight: 700; }
        .charts { display: grid; grid-template-columns: 1fr; gap: 16px; margin-top: 18px; }
        @media (max-width: 900px) { .grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 520px) { .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <h1>Dashboard</h1>

    <div class="grid">
        <div class="card">
            <h3>Total Diterima Hari Ini</h3>
            <div class="value">{{ $todayDiterima }}</div>
        </div>
        <div class="card">
            <h3>Total Diambil Hari Ini</h3>
            <div class="value">{{ $todayDiambil }}</div>
        </div>
        <div class="card">
            <h3>Belum Diambil (Status Diantar)</h3>
            <div class="value">{{ $belumDiambil }}</div>
        </div>
        <div class="card">
            <h3>Salah Wilayah / Tanpa Wilayah</h3>
            <div class="value">{{ $salahWilayah }}</div>
        </div>
    </div>

    <div class="charts">
        <div class="card">
            <h3>Trend Harian (14 hari terakhir)</h3>
            <canvas id="chartDaily" height="90"></canvas>
        </div>
        <div class="card">
            <h3>Trend Mingguan (8 minggu terakhir)</h3>
            <canvas id="chartWeekly" height="90"></canvas>
        </div>
        <div class="card">
            <h3>Trend Bulanan (12 bulan terakhir)</h3>
            <canvas id="chartMonthly" height="90"></canvas>
        </div>
    </div>

    <script>
        async function loadChart(canvasId, period, title) {
            const url = new URL(@json(route('dashboard.chart-data')), window.location.origin);
            url.searchParams.set('period', period);

            const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
            const payload = await res.json();

            const ctx = document.getElementById(canvasId);

            return new Chart(ctx, {
                type: 'line',
                data: {
                    labels: payload.labels,
                    datasets: [{
                        label: title,
                        data: payload.data,
                        borderWidth: 2,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.12)',
                        fill: true,
                        tension: 0.25,
                    }],
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        }

        loadChart('chartDaily', 'daily', 'Harian');
        loadChart('chartWeekly', 'weekly', 'Mingguan');
        loadChart('chartMonthly', 'monthly', 'Bulanan');
    </script>
</body>
</html>
