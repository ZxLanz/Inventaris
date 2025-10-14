<x-main-layout title-page="{{ __('Dashboard') }}">
    <h3 class="mb-4 fw-light">
        Selamat Datang, <strong>{{ Auth::user()->name }}</strong>
    </h3>

    @include('dashboard-partials.list-kartu-total')

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h5 class="card-title m-0">Ringkasan Kondisi Barang</h5>
                </div>
                <div class="card-body">
                    @include('dashboard-partials.list-kondisi-barang')
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h5 class="card-title m-0">5 Barang Terakhir Ditambahkan</h5>
                </div>
                <div class="card-body table-responsive">
                    @include('dashboard-partials.list-barang-terbaru')
                </div>
            </div>
        </div>
    </div>
</x-main-layout>
