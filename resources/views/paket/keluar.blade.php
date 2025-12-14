<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Paket Keluar</title>
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; margin: 24px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border-bottom: 1px solid #e5e7eb; padding: 10px 8px; text-align: left; }
        th { font-size: 12px; text-transform: uppercase; letter-spacing: 0.03em; color: #374151; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 999px; font-size: 12px; font-weight: 600; }
        .ready { background: #fef3c7; color: #92400e; }
        .taken { background: #dbeafe; color: #1e40af; }
        .done { background: #dcfce7; color: #166534; }
        .muted { color: #6b7280; }
    </style>
</head>
<body>
    <h1>Paket Keluar</h1>

    <p class="muted">
        Highlight: <span class="badge ready">Siap Diambil Keamanan</span>
        <span class="badge taken">Sudah Diambil (Diantar)</span>
        <span class="badge done">Selesai</span>
    </p>

    <table>
        <thead>
        <tr>
            <th>Kode Resi</th>
            <th>Penerima</th>
            <th>Wilayah</th>
            <th>Asrama</th>
            <th>Status</th>
            <th>Tanggal Diterima</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($pakets as $paket)
            @php
                $badgeClass = $paket->isDiproses() ? 'ready' : ($paket->isDiantar() ? 'taken' : ($paket->isSelesai() ? 'done' : ''));
            @endphp
            <tr>
                <td>{{ $paket->kode_resi }}</td>
                <td>{{ $paket->nama_penerima }}</td>
                <td>{{ $paket->wilayah?->nama ?? '-' }}</td>
                <td>{{ $paket->asrama?->nama ?? '-' }}</td>
                <td><span class="badge {{ $badgeClass }}">{{ $paket->status->label() }}</span></td>
                <td>{{ optional($paket->tanggal_diterima)->format('Y-m-d') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div style="margin-top: 14px;">
        {{ $pakets->links() }}
    </div>
</body>
</html>
