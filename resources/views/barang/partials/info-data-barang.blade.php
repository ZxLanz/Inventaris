<table class="table table-bordered table-striped">
    <tbody>
        <tr>
            <th style="width: 30%;">Nama Barang</th>
            <td>{{ $barang->nama_barang }}</td>
        </tr>
        <tr>
            <th>Kategori</th>
            <td>{{ $barang->kategori->nama_kategori }}</td>
        </tr>
        <tr>
            <th>Lokasi</th>
            <td>{{ $barang->lokasi->nama_lokasi }}</td>
        </tr>
        <tr>
            <th>Jumlah</th>
            <td>{{ $barang->jumlah }} {{ $barang->satuan }}</td>
        </tr>
        <tr>
            <th>Kondisi</th>
            <td>
                @php
                    $badgeClass = 'bg-success';
                    if ($barang->kondisi == 'Rusak Ringan') {
                        $badgeClass = 'bg-warning text-dark';
                    } elseif ($barang->kondisi == 'Rusak Berat') {
                        $badgeClass = 'bg-danger';
                    }
                @endphp
                <span class="badge {{ $badgeClass }}">{{ $barang->kondisi }}</span>
            </td>
        </tr>
        <tr>
            <th>Tanggal Pengadaan</th>
            <td>{{ \Carbon\Carbon::parse($barang->tanggal_pengadaan)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <th>Sumber Barang</th>
            <td>
                @if($barang->sumber_barang)
                    @switch($barang->sumber_barang)
                        @case('Pembelian')
                            <i class="bi bi-cart-check text-primary"></i> Pembelian
                            @break
                        @case('Hibah')
                            <i class="bi bi-gift text-success"></i> Hibah
                            @break
                        @case('Donasi')
                            <i class="bi bi-heart text-danger"></i> Donasi
                            @break
                        @case('Bantuan Pemerintah')
                            <i class="bi bi-building text-info"></i> Bantuan Pemerintah
                            @break
                        @case('Lainnya')
                            <i class="bi bi-box text-secondary"></i> Lainnya
                            @break
                        @default
                            {{ $barang->sumber_barang }}
                    @endswitch
                @else
                    <span class="text-muted">Tidak ada informasi</span>
                @endif
            </td>
        </tr>
        <tr>
            <th>Terakhir Diperbarui</th>
            <td>{{ \Carbon\Carbon::parse($barang->updated_at)->translatedFormat('d F Y, H:i') }}</td>
        </tr>
    </tbody>
</table>