<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">{{ __('Maintenance & Perbaikan') }}</h2>
            <div>
                {{-- 🆕 Tombol Cetak Laporan --}}
                <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#laporanModal">
                    <i class="bi bi-printer me-1"></i> Cetak Laporan
                </button>
                <a href="{{ route('maintenance.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Maintenance
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

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Statistics Cards --}}
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded p-3">
                                        <i class="bi bi-tools fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="text-muted mb-1">Total Maintenance</h6>
                                    <h3 class="mb-0">{{ $stats['total'] }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-warning bg-opacity-10 text-warning rounded p-3">
                                        <i class="bi bi-hourglass-split fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="text-muted mb-1">Sedang Proses</h6>
                                    <h3 class="mb-0">{{ $stats['in_progress'] }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-success bg-opacity-10 text-success rounded p-3">
                                        <i class="bi bi-check-circle fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="text-muted mb-1">Selesai</h6>
                                    <h3 class="mb-0">{{ $stats['completed'] }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-info bg-opacity-10 text-info rounded p-3">
                                        <i class="bi bi-cash-coin fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="text-muted mb-1">Total Biaya</h6>
                                    <h3 class="mb-0">Rp {{ number_format($stats['total_cost'], 0, ',', '.') }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filter & Search --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('maintenance.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Tipe Maintenance</label>
                                <select name="maintenance_type" class="form-select">
                                    <option value="">Semua Tipe</option>
                                    <option value="preventive" {{ request('maintenance_type') == 'preventive' ? 'selected' : '' }}>
                                        Preventive
                                    </option>
                                    <option value="corrective" {{ request('maintenance_type') == 'corrective' ? 'selected' : '' }}>
                                        Corrective
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>
                                        Sedang Proses
                                    </option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                                        Selesai
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" class="form-control" 
                                       value="{{ request('tanggal_mulai') }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" class="form-control" 
                                       value="{{ request('tanggal_selesai') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Cari</label>
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Cari kode maintenance, kode asset, nama barang..." 
                                       value="{{ request('search') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label d-block">&nbsp;</label>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search me-1"></i> Filter
                                </button>
                                <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-clockwise me-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Maintenance List --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    @if ($maintenances->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kode</th>
                                        <th>Asset</th>
                                        <th>Tipe</th>
                                        <th>Teknisi</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th>Biaya</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($maintenances as $maintenance)
                                        <tr>
                                            <td>
                                                <strong>{{ $maintenance->kode_maintenance }}</strong>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $maintenance->asset->kode_asset }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $maintenance->asset->barang->nama_barang }}</small>
                                                    <br>
                                                    <small class="text-muted">
                                                        <i class="bi bi-geo-alt"></i> {{ $maintenance->asset->lokasi->nama_lokasi }}
                                                    </small>
                                                </div>
                                            </td>
                                            <td>
                                                @if ($maintenance->maintenance_type === 'preventive')
                                                    <span class="badge bg-info">Preventive</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Corrective</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($maintenance->teknisi)
                                                    {{ $maintenance->teknisi->name }}
                                                @elseif ($maintenance->vendor_name)
                                                    <span class="text-muted">{{ $maintenance->vendor_name }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>
                                                    <strong>Mulai:</strong> {{ $maintenance->tanggal_mulai->format('d M Y') }}
                                                    <br>
                                                    @if ($maintenance->tanggal_selesai)
                                                        <strong>Selesai:</strong> {{ $maintenance->tanggal_selesai->format('d M Y') }}
                                                    @else
                                                        <span class="text-muted">Belum selesai</span>
                                                    @endif
                                                </small>
                                            </td>
                                            <td>
                                                @if ($maintenance->status === 'in_progress')
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="bi bi-hourglass-split"></i> Proses
                                                    </span>
                                                @else
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle"></i> Selesai
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>Rp {{ number_format($maintenance->total_biaya, 0, ',', '.') }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $maintenance->items_count }} item</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('maintenance.show', $maintenance) }}" 
                                                       class="btn btn-sm btn-info" title="Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    
                                                    @if ($maintenance->status === 'in_progress')
                                                        <a href="{{ route('maintenance.edit', $maintenance) }}" 
                                                           class="btn btn-sm btn-warning" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                    @endif

                                                    <form action="{{ route('maintenance.destroy', $maintenance) }}" 
                                                          method="POST" 
                                                          onsubmit="return confirm('Yakin ingin menghapus maintenance ini?')"
                                                          class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Menampilkan {{ $maintenances->firstItem() }} - {{ $maintenances->lastItem() }} 
                                dari {{ $maintenances->total() }} data
                            </div>
                            <div>
                                {{ $maintenances->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-3">Belum ada data maintenance</p>
                            <a href="{{ route('maintenance.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Maintenance Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- 🆕 Modal Cetak Laporan --}}
    <div class="modal fade" id="laporanModal" tabindex="-1" aria-labelledby="laporanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('maintenance.laporan') }}" method="GET" target="_blank">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="laporanModalLabel">
                            <i class="bi bi-printer me-2"></i>Cetak Laporan Maintenance
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Pilih filter untuk laporan yang ingin dicetak.</strong><br>
                            <small>Kosongkan untuk mencetak semua data.</small>
                        </div>

                        {{-- Filter Periode --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-calendar-range me-1"></i>Periode Tanggal
                            </label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small">Tanggal Mulai</label>
                                    <input type="date" name="tanggal_mulai" class="form-control" 
                                           value="{{ request('tanggal_mulai') }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small">Tanggal Selesai</label>
                                    <input type="date" name="tanggal_selesai" class="form-control" 
                                           value="{{ request('tanggal_selesai') }}">
                                </div>
                            </div>
                            <small class="text-muted">Kosongkan untuk semua periode</small>
                        </div>

                        {{-- Filter Status --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-check-circle me-1"></i>Status Maintenance
                            </label>
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>
                                    Sedang Proses
                                </option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                                    Selesai
                                </option>
                            </select>
                        </div>

                        {{-- Filter Tipe --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-tools me-1"></i>Tipe Maintenance
                            </label>
                            <select name="maintenance_type" class="form-select">
                                <option value="">Semua Tipe</option>
                                <option value="preventive" {{ request('maintenance_type') == 'preventive' ? 'selected' : '' }}>
                                    Preventive (Pemeliharaan Rutin)
                                </option>
                                <option value="corrective" {{ request('maintenance_type') == 'corrective' ? 'selected' : '' }}>
                                    Corrective (Perbaikan)
                                </option>
                            </select>
                        </div>

                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <small><strong>Catatan:</strong> Laporan akan dibuka di tab baru dalam format PDF.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-printer me-1"></i>Cetak Laporan PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>