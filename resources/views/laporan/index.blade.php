<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Paket</title>
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; margin: 24px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border-bottom: 1px solid #e5e7eb; padding: 10px 8px; text-align: left; }
        th { font-size: 12px; text-transform: uppercase; letter-spacing: 0.03em; color: #374151; }
        .filters { display: flex; flex-wrap: wrap; gap: 10px; align-items: end; margin-bottom: 14px; }
        label { font-size: 12px; display: block; color: #374151; margin-bottom: 4px; }
        input, select { padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; }
        button, a.btn { padding: 8px 12px; border-radius: 8px; border: 1px solid #d1d5db; background: #fff; color: #111827; text-decoration: none; display: inline-block; }
        .btn-primary { background: #2563eb; color: #fff; border-color: #2563eb; }
        .muted { color: #6b7280; }
    </style>
</head>
<body>
    <h1>Laporan Paket</h1>

    <form method="get" action="{{ route('laporan.index') }}" class="filters">
        <div>
            <label for="periode">Periode</label>
            <select name="periode" id="periode">
                <option value="day" @selected($periode === 'day')>Harian</option>
                <option value="week" @selected($periode === 'week')>Mingguan</option>
                <option value="month" @selected($periode === 'month')>Bulanan</option>
            </select>
        </div>
        <div>
            <label for="tanggal">Tanggal (YYYY-MM-DD)</label>
            <input type="text" name="tanggal" id="tanggal" value="{{ request('tanggal', $from->format('Y-m-d')) }}" placeholder="{{ now()->format('Y-m-d') }}">
        </div>
        <div>
            <button class="btn-primary" type="submit">Terapkan</button>
        </div>

        <div style="margin-left: auto; display: flex; gap: 8px;">
            <a class="btn" href="{{ route('laporan.export-excel', request()->query()) }}">Export Excel</a>
            <a class="btn" href="{{ route('laporan.export-pdf', request()->query()) }}">Export PDF</a>
        </div>
    </form>

    <p class="muted">Rentang: {{ $from->format('Y-m-d') }} s/d {{ $to->format('Y-m-d') }}</p>

    <table>
        <thead>
        <tr>
            <th>Kode Resi</th>
            <th>Penerima</th>
            <th>Wilayah</th>
            <th>Asrama</th>
            <th>Status</th>
            <th>Tgl Diterima</th>
            <th>Tgl Diambil</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($pakets as $paket)
            <tr>
                <td>{{ $paket->kode_resi }}</td>
                <td>{{ $paket->nama_penerima }}</td>
                <td>{{ $paket->wilayah?->nama ?? '-' }}</td>
                <td>{{ $paket->asrama?->nama ?? '-' }}</td>
                <td>{{ $paket->status->label() }}</td>
                <td>{{ optional($paket->tanggal_diterima)->format('Y-m-d') }}</td>
                <td>{{ optional($paket->tanggal_diambil)->format('Y-m-d') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div style="margin-top: 14px;">
        {{ $pakets->links() }}
    </div>
</body>
</html>
