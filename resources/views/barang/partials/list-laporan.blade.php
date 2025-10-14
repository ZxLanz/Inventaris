<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Kategori</th>
            <th>Lokasi</th>
            <th>Jumlah</th>
            <th>Kondisi</th>
<<<<<<< HEAD
            <th>Sumber Barang</th>
=======
>>>>>>> 7128ee3caecc07cd0adb1d836df3fe5b20ca7d83
            <th>Tgl. Pengadaan</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($barangs as $index => $barang)
        <tr>
            <td>{{ $index + 1 }}</td>
<<<<<<< HEAD
            <td>{{ $barang->kode_barang ?? '-' }}</td>
=======
            <td>{{ $barang->kode_barang }}</td>
>>>>>>> 7128ee3caecc07cd0adb1d836df3fe5b20ca7d83
            <td>{{ $barang->nama_barang }}</td>
            <td>{{ $barang->kategori->nama_kategori }}</td>
            <td>{{ $barang->lokasi->nama_lokasi }}</td>
            <td>{{ $barang->jumlah }} {{ $barang->satuan }}</td>
            <td>{{ $barang->kondisi }}</td>
<<<<<<< HEAD
            <td>{{ $barang->sumber_barang ?? '-' }}</td>
=======
>>>>>>> 7128ee3caecc07cd0adb1d836df3fe5b20ca7d83
            <td>{{ date('d-m-Y', strtotime($barang->tanggal_pengadaan)) }}</td>
        </tr>
        @empty
        <tr>
<<<<<<< HEAD
            <td colspan="9" style="text-align: center;">Tidak ada data.</td>
=======
            <td colspan="8" style="text-align: center;">Tidak ada data.</td>
>>>>>>> 7128ee3caecc07cd0adb1d836df3fe5b20ca7d83
        </tr>
        @endforelse
    </tbody>
</table>