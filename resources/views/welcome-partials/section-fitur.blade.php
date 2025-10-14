<section id="fitur" class="py-5">
    <div class="container text-center">
        <h2 class="fw-bold mb-5">Fitur Unggulan</h2>
        <div class="row">
            @php
            $fitur = [
                ['judul' => 'Manajemen Barang', 'deskripsi' => 'Tambah, edit, dan hapus barang dengan mudah sesuai kebutuhan instansi Anda.'],
                ['judul' => 'Laporan Cepat', 'deskripsi' => 'Dapatkan laporan stok barang secara real-time untuk pengambilan keputusan yang lebih baik.'],
                ['judul' => 'Akses Multi-User', 'deskripsi' => 'Bekerja sama dengan tim Anda, dengan kontrol hak akses yang terjaga.'],
            ];
            @endphp
            @foreach ($fitur as $f)
            <x-card-fitur :judul="$f['judul']" :deskripsi="$f['deskripsi']" />
            @endforeach
        </div>
    </div>
</section>