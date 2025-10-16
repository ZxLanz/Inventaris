{{-- resources/views/barang/partials/list-laporan.blade.php --}}
{{-- FIXED VERSION - Simple Black & White --}}

<table>
    <thead>
        <tr>
            <th style="width: 5%;">No</th>
            <th style="width: 10%;">Kode Barang</th>
            <th style="width: 20%;">Nama Barang</th>
            <th style="width: 12%;">Kategori</th>
            <th style="width: 15%;">Lokasi</th>
            <th style="width: 10%;">Jumlah</th>
            <th style="width: 10%;">Kondisi</th>
            <th style="width: 12%;">Sumber Barang</th>
            <th style="width: 10%;">Tgl. Pengadaan</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($barangs as $index => $barang)
        <tr>
            {{-- Nomor --}}
            <td style="text-align: center;">{{ $index + 1 }}</td>

            {{-- Kode Barang / Prefix --}}
            <td style="text-align: center;">
                @if($barang->jenis == 'asset')
                    {{ $barang->prefix }}
                @else
                    -
                @endif
            </td>

            {{-- Nama Barang --}}
            <td>{{ $barang->nama_barang }}</td>

            {{-- Kategori --}}
            <td>{{ $barang->kategori->nama_kategori }}</td>

            {{-- Lokasi --}}
            <td>{{ $barang->lokasi->nama_lokasi }}</td>

            {{-- Jumlah --}}
            <td style="text-align: center;">
                @if($barang->jenis == 'asset')
                    {{ $barang->assets->count() }} Unit
                @else
                    {{ $barang->jumlah }} {{ $barang->satuan }}
                @endif
            </td>

            {{-- Kondisi --}}
            <td style="text-align: center;">{{ $barang->kondisi }}</td>

            {{-- Sumber Barang --}}
            <td>{{ $barang->sumber_barang ?? '-' }}</td>

            {{-- Tanggal Pengadaan --}}
            <td style="text-align: center;">
                {{ \Carbon\Carbon::parse($barang->tanggal_pengadaan)->format('d-m-Y') }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="9" style="text-align: center; padding: 20px; color: #999;">
                Tidak ada data barang.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>