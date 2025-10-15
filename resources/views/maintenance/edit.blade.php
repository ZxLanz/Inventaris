<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">{{ __('Edit Maintenance') }}</h2>
            <div>
                <a href="{{ route('maintenance.show', $maintenance) }}" class="btn btn-info">
                    <i class="bi bi-eye me-1"></i> Lihat Detail
                </a>
                <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
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

            {{-- Info Alert --}}
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Kode Maintenance:</strong> {{ $maintenance->kode_maintenance }} | 
                <strong>Asset:</strong> {{ $maintenance->asset->kode_asset }} - {{ $maintenance->asset->barang->nama_barang }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>

            {{-- Form Card --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil me-2"></i>Form Edit Maintenance
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('maintenance.update', $maintenance) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        @include('maintenance.partials._form')
                    </form>
                </div>
            </div>

            {{-- Warning Card --}}
            <div class="card border-warning mt-4">
                <div class="card-body">
                    <h6 class="card-title text-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>Perhatian
                    </h6>
                    <ul class="mb-0 small">
                        <li>Asset tidak dapat diubah setelah maintenance dibuat</li>
                        <li>Anda dapat menambah, mengubah, atau menghapus item pekerjaan</li>
                        <li>Centang "Pekerjaan ini sudah selesai" untuk item yang sudah dikerjakan</li>
                        <li>Setelah semua pekerjaan selesai, gunakan tombol "Selesaikan Maintenance" di halaman detail</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>