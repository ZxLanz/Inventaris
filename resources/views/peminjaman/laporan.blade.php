<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-warning { background-color: #ffc107; color: #000; }
        .badge-success { background-color: #28a745; color: #fff; }
        .badge-danger { background-color: #dc3545; color: #fff; }
        .badge-primary { background-color: #007bff; color: #fff; }
        .badge-secondary { background-color: #6c757d; color: #fff; }
        .badge-info { background-color: #17a2b8; color: #fff; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $title }}</h2>
        <p>Periode: {{ $periode }}</p>
        <p>Dicetak: {{ $date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="12%">Kode</th>
                <th width="15%">Peminjam</th>
                <th width="13%">Barang</th>
                <th width="8%">Kode Unit</th>
                <th width="7%">Jumlah</th>
                <th width="10%">Tgl Pinjam</th>
                <th width="13%">Lokasi Tujuan</th>
                <th width="10%">Status</th>
                <th width="8%">Denda</th>
            </tr>
        </thead>
        <tbody>
            @forelse($peminjaman as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->kode_peminjaman }}</td>
                    <td>{{ $item->nama_peminjam }}</td>
                    <td>
                        {{ $item->barang->nama_barang }}
                        @if($item->barang->is_asset)
                            <span class="badge badge-info">Asset</span>
                        @endif
                    </td>
                    {{-- Kode Unit --}}
                    <td>
                        @if($item->barang->is_asset && $item->barangAsset)
                            <strong>{{ $item->barangAsset->kode_asset }}</strong>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $item->jumlah_pinjam }} {{ $item->barang->satuan }}</td>
                    <td>{{ $item->tanggal_pinjam->format('d/m/Y') }}</td>
                    {{-- Lokasi Tujuan --}}
                    <td>{{ $item->lokasi_tujuan ?? '-' }}</td>
                    <td>
                        @if($item->status == 'Menunggu Approval')
                            <span class="badge badge-warning">{{ $item->status }}</span>
                        @elseif($item->status == 'Disetujui')
                            <span class="badge badge-success">{{ $item->status }}</span>
                        @elseif($item->status == 'Ditolak')
                            <span class="badge badge-danger">{{ $item->status }}</span>
                        @elseif($item->status == 'Dipinjam')
                            <span class="badge badge-primary">{{ $item->status }}</span>
                        @elseif($item->status == 'Terlambat')
                            <span class="badge badge-danger">{{ $item->status }}</span>
                        @else
                            <span class="badge badge-secondary">{{ $item->status }}</span>
                        @endif
                    </td>
                    <td>
                        @if($item->total_denda > 0)
                            Rp {{ number_format($item->total_denda, 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center;">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="9" style="text-align: right;">Total Denda:</th>
                <th>Rp {{ number_format($total_denda, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Dicetak oleh: {{ Auth::user()->name }}</p>
    </div>
</body>
</html>
