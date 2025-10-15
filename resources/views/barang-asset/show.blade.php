<x-main-layout :title-page="'Detail Asset - ' . $barangAsset->kode_asset">
    <div class="row g-4">
        {{-- Main Content --}}
        <div class="col-md-8">
            {{-- Asset Information Card --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>Informasi Asset
                    </h5>
                </div>
                <div class="card-body">
                    <x-notif-alert class="mb-4" />

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Kode Asset</label>
                            <p class="mb-0">
                                <span class="badge bg-primary fs-6">{{ $barangAsset->kode_asset }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Status</label>
                            <p class="mb-0">
                                @if($barangAsset->status == 'tersedia')
                                    <span class="badge bg-success">Tersedia</span>
                                @elseif($barangAsset->status == 'dipinjam')
                                    <span class="badge bg-warning text-dark">Dipinjam</span>
                                @elseif($barangAsset->status == 'maintenance')
                                    <span class="badge bg-info">Maintenance</span>
                                @else
                                    <span class="badge bg-danger">Rusak</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Nama Barang</label>
                            <p class="mb-0"><strong>{{ $barangAsset->barang->nama_barang }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Kategori</label>
                            <p class="mb-0">{{ $barangAsset->barang->kategori->nama_kategori }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Lokasi</label>
                            <p class="mb-0">
                                <i class="bi bi-geo-alt text-muted"></i>
                                {{ $barangAsset->lokasi->nama_lokasi }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Kondisi</label>
                            <p class="mb-0">
                                @if($barangAsset->kondisi == 'Baik')
                                    <span class="badge bg-success">{{ $barangAsset->kondisi }}</span>
                                @elseif($barangAsset->kondisi == 'Rusak Ringan')
                                    <span class="badge bg-warning text-dark">{{ $barangAsset->kondisi }}</span>
                                @else
                                    <span class="badge bg-danger">{{ $barangAsset->kondisi }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Tanggal Pengadaan</label>
                            <p class="mb-0">
                                {{ \Carbon\Carbon::parse($barangAsset->tanggal_pengadaan)->format('d F Y') }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Umur Asset</label>
                            <p class="mb-0">
                                {{ \Carbon\Carbon::parse($barangAsset->tanggal_pengadaan)->diffForHumans() }}
                            </p>
                        </div>
                        @if($barangAsset->keterangan)
                            <div class="col-12">
                                <label class="text-muted small">Keterangan</label>
                                <p class="mb-0">{{ $barangAsset->keterangan }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Action Buttons --}}
                    <div class="row mt-4">
                        <div class="col-12">
                            <a href="{{ route('barang.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <a href="{{ route('barang-asset.edit', $barangAsset) }}" class="btn btn-warning">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            @if($barangAsset->status != 'dipinjam')
                                <button type="button" 
                                        class="btn btn-danger float-end" 
                                        onclick="confirmDelete()">
                                    <i class="bi bi-trash"></i> Hapus Asset
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Riwayat Maintenance --}}
            @if($barangAsset->maintenanceRecords && $barangAsset->maintenanceRecords->count() > 0)
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-tools me-2"></i>Riwayat Maintenance ({{ $barangAsset->maintenanceRecords->count() }})
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Tipe</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th>Biaya</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($barangAsset->maintenanceRecords as $maintenance)
                                        <tr>
                                            <td><small>{{ $maintenance->kode_maintenance }}</small></td>
                                            <td>
                                                @if($maintenance->maintenance_type == 'preventive')
                                                    <span class="badge bg-info">Preventive</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Corrective</span>
                                                @endif
                                            </td>
                                            <td><small>{{ $maintenance->tanggal_mulai->format('d M Y') }}</small></td>
                                            <td>
                                                @if($maintenance->status == 'completed')
                                                    <span class="badge bg-success">Selesai</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Proses</span>
                                                @endif
                                            </td>
                                            <td><small>Rp {{ number_format($maintenance->total_biaya, 0, ',', '.') }}</small></td>
                                            <td>
                                                <a href="{{ route('maintenance.show', $maintenance) }}" 
                                                   class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-md-4">
            {{-- Gambar Asset --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-image me-2"></i>Foto Asset
                    </h5>
                </div>
                <div class="card-body text-center">
                    @if($barangAsset->gambar)
                        <img src="{{ asset('gambar-barang/' . $barangAsset->gambar) }}" 
                             alt="{{ $barangAsset->barang->nama_barang }}" 
                             class="img-fluid rounded"
                             style="max-height: 400px;"
                             onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'bg-light rounded p-5\'><i class=\'bi bi-image text-muted\' style=\'font-size: 4rem;\'></i><p class=\'text-muted mt-3 mb-0\'>Gambar tidak tersedia</p></div>';">
                    @else
                        <div class="bg-light rounded p-5">
                            <i class="bi bi-image text-muted" style="font-size: 4rem;"></i>
                            <p class="text-muted mt-3 mb-0">Tidak ada gambar</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-bar-chart me-2"></i>Statistik
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Total Maintenance</small>
                        <h4 class="mb-0">{{ $barangAsset->maintenanceRecords ? $barangAsset->maintenanceRecords->count() : 0 }}</h4>
                    </div>
                    <div>
                        <small class="text-muted">Total Biaya Maintenance</small>
                        <h4 class="mb-0">
                            Rp {{ number_format($barangAsset->maintenanceRecords ? $barangAsset->maintenanceRecords->sum('total_biaya') : 0, 0, ',', '.') }}
                        </h4>
                    </div>
                </div>
            </div>

            {{-- Info Barang Master --}}
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-box-seam me-2"></i>Info Barang Master
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td class="text-muted">Kode Barang</td>
                            <td><strong>{{ $barangAsset->barang->kode_barang }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Total Unit</td>
                            <td><strong>{{ $barangAsset->barang->jumlah }} unit</strong></td>
                        </tr>
                        @if($barangAsset->barang->harga_satuan)
                        <tr>
                            <td class="text-muted">Harga Satuan</td>
                            <td><strong>Rp {{ number_format($barangAsset->barang->harga_satuan, 0, ',', '.') }}</strong></td>
                        </tr>
                        @endif
                    </table>
                    <a href="{{ route('barang.show', $barangAsset->barang) }}" 
                       class="btn btn-sm btn-outline-primary w-100 mt-3">
                        <i class="bi bi-eye me-1"></i> Lihat Detail Barang
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden Delete Form --}}
    <form id="delete-form" 
          action="{{ route('barang-asset.destroy', $barangAsset->id) }}" 
          method="POST" 
          style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    {{-- JavaScript --}}
    <script>
    function confirmDelete() {
        if (confirm('Yakin ingin menghapus asset {{ $barangAsset->kode_asset }}?\n\nTindakan ini tidak dapat dibatalkan!')) {
            document.getElementById('delete-form').submit();
        }
    }
    </script>
</x-main-layout>