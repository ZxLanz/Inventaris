<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">{{ __('Tambah Maintenance Baru') }}</h2>
            <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid">
            {{-- Alert Messages --}}
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h6 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Terdapat kesalahan:</h6>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Form Card --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-plus-circle me-2"></i>Form Tambah Maintenance
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('maintenance.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @include('maintenance.partials._form')
                    </form>
                </div>
            </div>

            {{-- Info Card --}}
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-info-circle me-2"></i>Petunjuk Pengisian</h6>
                    <ul class="mb-0 small text-muted">
                        <li><strong>Tipe Maintenance:</strong>
                            <ul>
                                <li><strong>Preventive:</strong> Pemeliharaan rutin/terjadwal (contoh: service AC, kalibrasi alat)</li>
                                <li><strong>Corrective:</strong> Perbaikan saat barang rusak/bermasalah</li>
                            </ul>
                        </li>
                        <li><strong>Teknisi/Vendor:</strong> Pilih salah satu (teknisi internal atau vendor external)</li>
                        <li><strong>Detail Pekerjaan:</strong> Minimal 1 item pekerjaan harus diisi</li>
                        <li><strong>Biaya Material:</strong> Biaya spare parts/komponen yang diganti</li>
                        <li><strong>Biaya Jasa:</strong> Biaya tenaga kerja/jasa teknisi</li>
                        <li><strong>Status Asset:</strong> Setelah maintenance disimpan, status asset otomatis berubah menjadi "Maintenance"</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>