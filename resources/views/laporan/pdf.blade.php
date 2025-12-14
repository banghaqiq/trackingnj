<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Paket</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        h1 { font-size: 16px; margin: 0 0 8px; }
        .meta { margin: 0 0 12px; color: #555; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 6px 6px; }
        th { background: #f3f4f6; }
    </style>
</head>
<body>
    <h1>Laporan Paket ({{ strtoupper($periode) }})</h1>
    <p class="meta">Periode: {{ $from->format('Y-m-d') }} s/d {{ $to->format('Y-m-d') }}</p>

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
</body>
</html>
