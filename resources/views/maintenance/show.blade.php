<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">{{ __('Detail Maintenance') }}</h2>
            <div>
                @if($maintenance->status === 'in_progress')
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#completeModal">
                        <i class="bi bi-check-circle me-1"></i> Selesaikan Maintenance
                    </button>
                    <a href="{{ route('maintenance.edit', $maintenance) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i> Edit
                    </a>
                @endif
                <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid">
            {{-- Alert Messages --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row g-4">
                {{-- Info Maintenance --}}
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-info-circle me-2"></i>Informasi Maintenance
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="text-muted small">Kode Maintenance</label>
                                    <p class="mb-0"><strong>{{ $maintenance->kode_maintenance }}</strong></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">Status</label>
                                    <p class="mb-0">
                                        @if($maintenance->status === 'in_progress')
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-hourglass-split"></i> Sedang Proses
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> Selesai
                                            </span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">Tipe Maintenance</label>
                                    <p class="mb-0">
                                        @if($maintenance->maintenance_type === 'preventive')
                                            <span class="badge bg-info">Preventive</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Corrective</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">Teknisi/Vendor</label>
                                    <p class="mb-0">
                                        @if($maintenance->teknisi)
                                            <i class="bi bi-person me-1"></i>{{ $maintenance->teknisi->name }}
                                        @elseif($maintenance->vendor_name)
                                            <i class="bi bi-building me-1"></i>{{ $maintenance->vendor_name }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">Tanggal Mulai</label>
                                    <p class="mb-0">{{ $maintenance->tanggal_mulai->format('d F Y') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">Tanggal Selesai</label>
                                    <p class="mb-0">
                                        @if($maintenance->tanggal_selesai)
                                            {{ $maintenance->tanggal_selesai->format('d F Y') }}
                                        @else
                                            <span class="text-muted">Belum selesai</span>
                                        @endif
                                    </p>
                                </div>
                                @if($maintenance->masalah_ditemukan)
                                    <div class="col-12">
                                        <label class="text-muted small">Masalah/Keluhan</label>
                                        <p class="mb-0">{{ $maintenance->masalah_ditemukan }}</p>
                                    </div>
                                @endif
                                @if($maintenance->hasil_maintenance)
                                    <div class="col-12">
                                        <label class="text-muted small">Hasil Maintenance</label>
                                        <p class="mb-0">{{ $maintenance->hasil_maintenance }}</p>
                                    </div>
                                @endif
                                @if($maintenance->catatan)
                                    <div class="col-12">
                                        <label class="text-muted small">Catatan</label>
                                        <p class="mb-0">{{ $maintenance->catatan }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                   {{-- Foto Before & After --}}
@if($maintenance->foto_sebelum || $maintenance->foto_sesudah)
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="bi bi-images me-2"></i>Dokumentasi Foto
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @if($maintenance->foto_sebelum)
                    <div class="col-md-6">
                        <label class="text-muted small mb-2">Foto Sebelum</label>
                        <a href="{{ asset('storage/' . $maintenance->foto_sebelum) }}" target="_blank">
                            <img src="{{ asset('storage/' . $maintenance->foto_sebelum) }}" 
                                 class="img-fluid rounded shadow-sm border" 
                                 alt="Foto Sebelum"
                                 style="max-height: 300px; object-fit: cover;"
                                 onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'alert alert-warning mt-2\'>Gambar tidak ditemukan: {{ basename($maintenance->foto_sebelum) }}</div>';"/>
                        </a>
                    </div>
                @else
                    <div class="col-md-6">
                        <label class="text-muted small mb-2">Foto Sebelum</label>
                        <div class="alert alert-warning mt-2">Tidak ada foto sebelum</div>
                    </div>
                @endif

                @if($maintenance->foto_sesudah)
                    <div class="col-md-6">
                        <label class="text-muted small mb-2">Foto Sesudah</label>
                        <a href="{{ asset('storage/' . $maintenance->foto_sesudah) }}" target="_blank">
                            <img src="{{ asset('storage/' . $maintenance->foto_sesudah) }}" 
                                 class="img-fluid rounded shadow-sm border" 
                                 alt="Foto Sesudah"
                                 style="max-height: 300px; object-fit: cover;"
                                 onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'alert alert-warning mt-2\'>Gambar tidak ditemukan: {{ basename($maintenance->foto_sesudah) }}</div>';"/>
                        </a>
                    </div>
                @else
                    <div class="col-md-6">
                        <label class="text-muted small mb-2">Foto Sesudah</label>
                        <div class="alert alert-warning mt-2">Tidak ada foto sesudah</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif


                    {{-- Detail Pekerjaan --}}
                    <div class="card border-0 shadow-sm mt-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="bi bi-list-check me-2"></i>Detail Pekerjaan ({{ $maintenance->items->count() }} Item)
                            </h5>
                        </div>
                        <div class="card-body">
                            @foreach($maintenance->items as $item)
                                <div class="border rounded p-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1">
                                                {{ $loop->iteration }}. {{ $item->nama_item }}
                                                @if($item->is_completed)
                                                    <span class="badge bg-success ms-2">
                                                        <i class="bi bi-check-circle"></i> Selesai
                                                    </span>
                                                @endif
                                            </h6>
                                            <span class="badge bg-secondary">{{ ucfirst($item->kategori) }}</span>
                                        </div>
                                        <div class="text-end">
                                            <strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong>
                                        </div>
                                    </div>
                                    @if($item->deskripsi)
                                        <p class="text-muted small mb-2">{{ $item->deskripsi }}</p>
                                    @endif
                                    <div class="row g-2 small">
                                        <div class="col-6">
                                            <span class="text-muted">Biaya Material:</span>
                                            <strong>Rp {{ number_format($item->biaya_material, 0, ',', '.') }}</strong>
                                        </div>
                                        <div class="col-6">
                                            <span class="text-muted">Biaya Jasa:</span>
                                            <strong>Rp {{ number_format($item->biaya_jasa, 0, ',', '.') }}</strong>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="col-md-4">
                    {{-- Asset Info --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="bi bi-box me-2"></i>Informasi Asset
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                @if($maintenance->asset->gambar)
                                    <img src="{{ asset('gambar-barang/' . $maintenance->asset->gambar) }}" 
                                         class="img-fluid rounded" 
                                         style="max-height: 200px;"
                                         alt="{{ $maintenance->asset->barang->nama_barang }}"
                                         onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'bg-light rounded p-5\'><i class=\'bi bi-image text-muted\' style=\'font-size: 3rem;\'></i><p class=\'text-muted mt-2 mb-0\'>Gambar tidak tersedia</p></div>';">
                                @else
                                    <div class="bg-light rounded p-5">
                                        <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                        <p class="text-muted mt-2 mb-0">Tidak ada gambar</p>
                                    </div>
                                @endif
                            </div>
                            <table class="table table-sm">
                                <tr>
                                    <td class="text-muted">Kode Asset</td>
                                    <td><strong>{{ $maintenance->asset->kode_asset }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Nama Barang</td>
                                    <td>{{ $maintenance->asset->barang->nama_barang }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Kategori</td>
                                    <td>{{ $maintenance->asset->barang->kategori->nama_kategori }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Lokasi</td>
                                    <td>
                                        <i class="bi bi-geo-alt text-muted"></i>
                                        {{ $maintenance->asset->lokasi->nama_lokasi }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Status</td>
                                    <td>
                                        <span class="badge {{ $maintenance->asset->status_badge }}">
                                            {{ ucfirst($maintenance->asset->status) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                            <a href="{{ route('barang-asset.show', $maintenance->asset) }}" 
                               class="btn btn-sm btn-outline-primary w-100">
                                <i class="bi bi-eye me-1"></i> Lihat Detail Asset
                            </a>
                        </div>
                    </div>

                    {{-- Summary Biaya --}}
                    <div class="card border-0 shadow-sm mt-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="bi bi-cash-coin me-2"></i>Ringkasan Biaya
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm mb-0">
                                <tr>
                                    <td>Total Material</td>
                                    <td class="text-end">
                                        <strong>Rp {{ number_format($maintenance->total_biaya_material, 0, ',', '.') }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Total Jasa</td>
                                    <td class="text-end">
                                        <strong>Rp {{ number_format($maintenance->total_biaya_jasa, 0, ',', '.') }}</strong>
                                    </td>
                                </tr>
                                <tr class="table-active">
                                    <td><strong>TOTAL BIAYA</strong></td>
                                    <td class="text-end">
                                        <strong class="text-primary fs-5">
                                            Rp {{ number_format($maintenance->total_biaya, 0, ',', '.') }}
                                        </strong>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Complete Maintenance --}}
    @if($maintenance->status === 'in_progress')
        <div class="modal fade" id="completeModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('maintenance.complete', $maintenance) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-check-circle me-2"></i>Selesaikan Maintenance
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_selesai" class="form-control" 
                                       value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Hasil Maintenance <span class="text-danger">*</span></label>
                                <textarea name="hasil_maintenance" class="form-control" rows="3" 
                                          placeholder="Jelaskan hasil maintenance..." required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Foto Sesudah</label>
                                <input type="file" name="foto_sesudah" class="form-control" accept="image/*">
                                <small class="text-muted">Format: JPG, PNG, GIF. Max: 2MB</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Catatan Tambahan</label>
                                <textarea name="catatan" class="form-control" rows="2" 
                                          placeholder="Catatan tambahan (optional)"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-1"></i> Selesaikan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>