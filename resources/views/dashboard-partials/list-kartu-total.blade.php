<<<<<<< HEAD
{{-- Baris 1: Card Utama (Barang, Kategori, Lokasi, User) --}}
=======
>>>>>>> 7128ee3caecc07cd0adb1d836df3fe5b20ca7d83
<div class="row">
    @php
        $kartu = [
            [
                'text' => 'TOTAL BARANG',
                'total' => $jumlahBarang,
                'route' => 'barang.index',
                'icon' => 'bi-box-seam',
                'color' => 'primary',
            ],
            [
                'text' => 'TOTAL KATEGORI',
                'total' => $jumlahKategori,
                'route' => 'kategori.index',
                'icon' => 'bi-tag',
                'color' => 'secondary',
            ],
            [
                'text' => 'TOTAL LOKASI',
                'total' => $jumlahLokasi,
                'route' => 'lokasi.index',
                'icon' => 'bi-geo-alt',
                'color' => 'success',
            ],
            [
                'text' => 'TOTAL USER',
                'total' => $jumlahUser,
                'route' => 'user.index',
                'icon' => 'bi-people',
                'color' => 'danger',
                'role'  => 'admin',
            ],
        ];
    @endphp

<<<<<<< HEAD
    @foreach ($kartu as $item)
        @php
            extract($item);
=======
    @foreach ($kartu as $kartu)
        @php
            extract($kartu);
>>>>>>> 7128ee3caecc07cd0adb1d836df3fe5b20ca7d83
        @endphp

        @isset($role)
            @role($role)
                <x-kartu-total :text="$text" :route="$route" :total="$total" :icon="$icon" color="{{ $color }}" />
            @endrole
        @else
            <x-kartu-total :text="$text" :route="$route" :total="$total" :icon="$icon" color="{{ $color }}" />
        @endisset
    @endforeach
</div>
<<<<<<< HEAD

{{-- Baris 2: Card Peminjaman (Baris Terpisah) --}}
<div class="row mt-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card shadow py-2 border-info">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <div class="fw-bold text-info mb-1">TOTAL PEMINJAMAN</div>
                        <div class="h5">{{ $totalPeminjaman }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-clipboard-check text-info" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('peminjaman.index') }}" class="text-secondary text-decoration-none">
                    Lihat Selengkapnya <i class="bi bi-box-arrow-up-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card shadow py-2 border-primary">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <div class="fw-bold text-primary mb-1">SEDANG DIPINJAM</div>
                        <div class="h5">{{ $peminjamanAktif }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-hourglass-split text-primary" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('peminjaman.index', ['status' => 'Dipinjam']) }}" class="text-secondary text-decoration-none">
                    Lihat Selengkapnya <i class="bi bi-box-arrow-up-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card shadow py-2 border-danger">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <div class="fw-bold text-danger mb-1">TERLAMBAT</div>
                        <div class="h5">{{ $peminjamanTerlambat }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-exclamation-triangle text-danger" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('peminjaman.index', ['status' => 'Terlambat']) }}" class="text-secondary text-decoration-none">
                    Lihat Selengkapnya <i class="bi bi-box-arrow-up-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card shadow py-2 border-warning">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <div class="fw-bold text-warning mb-1">MENUNGGU APPROVAL</div>
                        <div class="h5">{{ $menungguApproval }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-clock-history text-warning" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('peminjaman.index', ['status' => 'Menunggu Approval']) }}" class="text-secondary text-decoration-none">
                    Lihat Selengkapnya <i class="bi bi-box-arrow-up-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>
=======
>>>>>>> 7128ee3caecc07cd0adb1d836df3fe5b20ca7d83
