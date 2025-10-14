{{-- resources/views/barang/partials/list-barang.blade.php --}}

<div class="table-responsive">
    <table class="table table-hover table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="12%">Kode/Prefix</th>
                <th width="18%">Nama Barang</th>
                <th width="8%">Jenis</th>
                <th width="10%">Kategori</th>
                <th width="8%" class="text-center">Jumlah</th>
                <th width="12%">Sumber Barang</th>
                <th width="12%">Lokasi</th>
                <th width="15%" class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($barangs as $index => $barang)
                {{-- BARIS UTAMA BARANG --}}
                <tr>
                    <td class="text-center">{{ $barangs->firstItem() + $index }}</td>
                    <td>
                        @if($barang->jenis == 'asset')
                            <span class="badge bg-primary">{{ $barang->prefix }}</span>
                        @else
                            <span class="badge bg-secondary">-</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            @if($barang->gambar)
                                <img src="{{ asset('gambar-barang/' . $barang->gambar) }}" 
                                     alt="{{ $barang->nama_barang }}" 
                                     class="rounded me-2"
                                     style="width: 40px; height: 40px; object-fit: cover;">
                            @endif
                            <div>
                                <strong>{{ $barang->nama_barang }}</strong>
                                @if($barang->jenis == 'asset' && $barang->assets->isNotEmpty())
                                    <br>
                                    <button class="btn btn-sm btn-link p-0 text-decoration-none" 
                                            type="button" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#detail-{{ $barang->id }}" 
                                            aria-expanded="false">
                                        <i class="bi bi-chevron-down"></i> Lihat Detail Unit
                                    </button>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($barang->jenis == 'asset')
                            <span class="badge bg-info text-dark">
                                <i class="bi bi-boxes"></i> Asset
                            </span>
                        @else
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-basket"></i> Consumable
                            </span>
                        @endif
                    </td>
                    <td>{{ $barang->kategori->nama_kategori }}</td>
                    <td class="text-center">
                        @if($barang->jenis == 'asset')
                            <strong>{{ $barang->assets->count() }}</strong> Unit
                        @else
                            <strong>{{ $barang->jumlah }}</strong> {{ $barang->satuan }}
                        @endif
                    </td>
                    <td>
                        @if($barang->sumber_barang)
                            <small>
                                @switch($barang->sumber_barang)
                                    @case('Pembelian')
                                        <i class="bi bi-cart-check text-primary"></i>
                                        @break
                                    @case('Hibah')
                                        <i class="bi bi-gift text-success"></i>
                                        @break
                                    @case('Donasi')
                                        <i class="bi bi-heart text-danger"></i>
                                        @break
                                    @case('Bantuan Pemerintah')
                                        <i class="bi bi-building text-info"></i>
                                        @break
                                    @default
                                        <i class="bi bi-box text-secondary"></i>
                                @endswitch
                                {{ $barang->sumber_barang }}
                            </small>
                        @else
                            <small class="text-muted fst-italic">-</small>
                        @endif
                    </td>
                    <td>
                        <small class="text-muted">
                            <i class="bi bi-geo-alt"></i> {{ $barang->lokasi->nama_lokasi }}
                        </small>
                    </td>
                    <td class="text-center">
                        <div class="btn-group" role="group">
                            <a href="{{ route('barang.show', $barang->id) }}" 
                               class="btn btn-sm btn-info" 
                               title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('barang.edit', $barang->id) }}" 
                               class="btn btn-sm btn-warning" 
                               title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('barang.destroy', $barang->id) }}" 
                                  method="POST" 
                                  class="d-inline"
                                  onsubmit="return confirm('Yakin ingin menghapus barang ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>

                {{-- DETAIL DROPDOWN/COLLAPSE UNTUK ASSET --}}
                @if($barang->jenis == 'asset' && $barang->assets->isNotEmpty())
                    <tr>
                        <td colspan="9" class="p-0 border-0">
                            <div class="collapse" id="detail-{{ $barang->id }}">
                                <div class="p-3 bg-light">
                                    <h6 class="mb-3">
                                        <i class="bi bi-list-ul"></i> 
                                        Detail Unit Asset - {{ $barang->nama_barang }}
                                    </h6>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped table-bordered mb-3">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th width="4%" class="text-center">No</th>
                                                    <th width="13%">Kode Asset</th>
                                                    <th width="11%">Status</th>
                                                    <th width="11%">Kondisi</th>
                                                    <th width="15%">Lokasi</th>
                                                    <th width="13%">Sumber Barang</th>
                                                    <th width="12%">Tgl Pengadaan</th>
                                                    <th width="11%" class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($barang->assets as $assetIndex => $asset)
                                                    <tr>
                                                        <td class="text-center">{{ $assetIndex + 1 }}</td>
                                                        <td>
                                                            <strong class="text-primary">{{ $asset->kode_asset }}</strong>
                                                        </td>
                                                        <td>
                                                            @switch($asset->status)
                                                                @case('tersedia')
                                                                    <span class="badge bg-success">
                                                                        <i class="bi bi-check-circle"></i> Tersedia
                                                                    </span>
                                                                    @break
                                                                @case('dipinjam')
                                                                    <span class="badge bg-warning text-dark">
                                                                        <i class="bi bi-arrow-right-circle"></i> Dipinjam
                                                                    </span>
                                                                    @break
                                                                @case('maintenance')
                                                                    <span class="badge bg-info text-dark">
                                                                        <i class="bi bi-tools"></i> Maintenance
                                                                    </span>
                                                                    @break
                                                                @case('rusak')
                                                                    <span class="badge bg-danger">
                                                                        <i class="bi bi-exclamation-triangle"></i> Rusak
                                                                    </span>
                                                                    @break
                                                                @default
                                                                    <span class="badge bg-secondary">{{ ucfirst($asset->status) }}</span>
                                                            @endswitch
                                                        </td>
                                                        <td>
                                                            @switch($asset->kondisi)
                                                                @case('Baik')
                                                                    <span class="badge bg-success">Baik</span>
                                                                    @break
                                                                @case('Rusak Ringan')
                                                                    <span class="badge bg-warning text-dark">Rusak Ringan</span>
                                                                    @break
                                                                @case('Rusak Berat')
                                                                    <span class="badge bg-danger">Rusak Berat</span>
                                                                    @break
                                                                @default
                                                                    <span class="badge bg-secondary">{{ $asset->kondisi }}</span>
                                                            @endswitch
                                                        </td>
                                                        <td>
                                                            <small>
                                                                <i class="bi bi-geo-alt"></i> 
                                                                {{ $asset->lokasi->nama_lokasi ?? '-' }}
                                                            </small>
                                                        </td>
                                                        <td>
                                                            @if($barang->sumber_barang)
                                                                <small>
                                                                    @switch($barang->sumber_barang)
                                                                        @case('Pembelian')
                                                                            <i class="bi bi-cart-check text-primary"></i>
                                                                            @break
                                                                        @case('Hibah')
                                                                            <i class="bi bi-gift text-success"></i>
                                                                            @break
                                                                        @case('Donasi')
                                                                            <i class="bi bi-heart text-danger"></i>
                                                                            @break
                                                                        @case('Bantuan Pemerintah')
                                                                            <i class="bi bi-building text-info"></i>
                                                                            @break
                                                                        @default
                                                                            <i class="bi bi-box text-secondary"></i>
                                                                    @endswitch
                                                                    {{ $barang->sumber_barang }}
                                                                </small>
                                                            @else
                                                                <small class="text-muted fst-italic">-</small>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <small>{{ \Carbon\Carbon::parse($asset->tanggal_pengadaan)->format('d M Y') }}</small>
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="btn-group btn-group-sm" role="group">
                                                                {{-- Tombol Detail --}}
                                                                <button class="btn btn-info" 
                                                                        data-bs-toggle="modal" 
                                                                        data-bs-target="#assetModal{{ $asset->id }}"
                                                                        title="Detail">
                                                                    <i class="bi bi-eye"></i>
                                                                </button>
                                                                
                                                                {{-- Tombol Edit --}}
                                                                <a href="{{ route('barang-asset.edit', $asset->id) }}" 
                                                                   class="btn btn-warning" 
                                                                   title="Edit Asset">
                                                                    <i class="bi bi-pencil"></i>
                                                                </a>
                                                                
                                                                {{-- Tombol Hapus --}}
                                                                @if($asset->status != 'dipinjam')
                                                                    <form action="{{ route('barang-asset.destroy', $asset->id) }}" 
                                                                          method="POST" 
                                                                          class="d-inline"
                                                                          onsubmit="return confirm('Yakin ingin menghapus unit {{ $asset->kode_asset }}?')">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" 
                                                                                class="btn btn-danger" 
                                                                                title="Hapus">
                                                                            <i class="bi bi-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                @else
                                                                    <button class="btn btn-secondary" 
                                                                            disabled 
                                                                            title="Tidak bisa hapus, sedang dipinjam">
                                                                        <i class="bi bi-lock"></i>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- Summary Info --}}
                                    <div class="mt-2 p-3 bg-white rounded border">
                                        <div class="row text-center">
                                            <div class="col-3">
                                                <small class="text-muted d-block">Total Unit</small>
                                                <h5 class="mb-0">{{ $barang->assets->count() }}</h5>
                                            </div>
                                            <div class="col-3">
                                                <small class="text-muted d-block">Tersedia</small>
                                                <h5 class="mb-0 text-success">{{ $barang->assets->where('status', 'tersedia')->count() }}</h5>
                                            </div>
                                            <div class="col-3">
                                                <small class="text-muted d-block">Dipinjam</small>
                                                <h5 class="mb-0 text-warning">{{ $barang->assets->where('status', 'dipinjam')->count() }}</h5>
                                            </div>
                                            <div class="col-3">
                                                <small class="text-muted d-block">Rusak</small>
                                                <h5 class="mb-0 text-danger">{{ $barang->assets->where('status', 'rusak')->count() }}</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="9" class="text-center py-4">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                        <p class="text-muted mt-2">Tidak ada data barang</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- SEMUA MODAL DILETAKKAN DI LUAR TABEL --}}
@foreach($barangs as $barang)
    @if($barang->jenis == 'asset' && $barang->assets->isNotEmpty())
        @foreach($barang->assets as $asset)
            <div class="modal fade" id="assetModal{{ $asset->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Detail Asset - {{ $asset->kode_asset }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            @if($asset->gambar)
                                <div class="text-center mb-3">
                                    <img src="{{ asset('gambar-barang/' . $asset->gambar) }}" 
                                         alt="{{ $asset->kode_asset }}" 
                                         class="img-fluid rounded" 
                                         style="max-height: 200px;">
                                </div>
                            @endif
                            <table class="table table-sm table-bordered">
                                <tr>
                                    <th width="40%" class="bg-light">Kode Asset</th>
                                    <td><strong>{{ $asset->kode_asset }}</strong></td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Nama Barang</th>
                                    <td>{{ $barang->nama_barang }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Status</th>
                                    <td>
                                        @switch($asset->status)
                                            @case('tersedia')
                                                <span class="badge bg-success">Tersedia</span>
                                                @break
                                            @case('dipinjam')
                                                <span class="badge bg-warning text-dark">Dipinjam</span>
                                                @break
                                            @case('maintenance')
                                                <span class="badge bg-info text-dark">Maintenance</span>
                                                @break
                                            @case('rusak')
                                                <span class="badge bg-danger">Rusak</span>
                                                @break
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Kondisi</th>
                                    <td>
                                        @switch($asset->kondisi)
                                            @case('Baik')
                                                <span class="badge bg-success">Baik</span>
                                                @break
                                            @case('Rusak Ringan')
                                                <span class="badge bg-warning text-dark">Rusak Ringan</span>
                                                @break
                                            @case('Rusak Berat')
                                                <span class="badge bg-danger">Rusak Berat</span>
                                                @break
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Lokasi</th>
                                    <td>{{ $asset->lokasi->nama_lokasi ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Sumber Barang</th>
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
                                                @default
                                                    {{ $barang->sumber_barang }}
                                            @endswitch
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Tanggal Pengadaan</th>
                                    <td>{{ \Carbon\Carbon::parse($asset->tanggal_pengadaan)->format('d F Y') }}</td>
                                </tr>
                                @if($asset->keterangan)
                                    <tr>
                                        <th class="bg-light">Keterangan</th>
                                        <td>{{ $asset->keterangan }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                        <div class="modal-footer">
                            <a href="{{ route('barang-asset.edit', $asset->id) }}" class="btn btn-warning">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
@endforeach

{{-- Pagination --}}
@if($barangs->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted">
            Menampilkan {{ $barangs->firstItem() }} - {{ $barangs->lastItem() }} dari {{ $barangs->total() }} data
        </div>
        <div>
            {{ $barangs->links() }}
        </div>
    </div>
@endif

<style>
/* Collapse animation */
.collapse {
    transition: height 0.35s ease;
}

/* Table hover effect */
.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}

/* Badge styling */
.badge {
    padding: 0.35em 0.65em;
    font-size: 0.85em;
}

/* Nested table styling */
.collapse .table {
    background-color: white;
    margin-bottom: 0;
}

.table-dark th {
    background-color: #343a40 !important;
    color: white !important;
}

/* Button group */
.btn-group .btn {
    border-radius: 0;
}

.btn-group .btn:first-child {
    border-top-left-radius: 0.25rem;
    border-bottom-left-radius: 0.25rem;
}

.btn-group .btn:last-child {
    border-top-right-radius: 0.25rem;
    border-bottom-right-radius: 0.25rem;
}

/* Icon alignment */
.bi {
    vertical-align: middle;
}

/* Chevron rotation animation */
[data-bs-toggle="collapse"] i.bi-chevron-down {
    transition: transform 0.3s ease;
}

[data-bs-toggle="collapse"][aria-expanded="true"] i.bi-chevron-down {
    transform: rotate(180deg);
}

/* Summary box */
.bg-white.rounded.border {
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

/* Remove border from collapse row */
.collapse-row {
    border: none !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle collapse toggle
    const collapseButtons = document.querySelectorAll('[data-bs-toggle="collapse"]');
    
    collapseButtons.forEach(button => {
        const targetId = button.getAttribute('data-bs-target');
        const collapseElement = document.querySelector(targetId);
        
        if (collapseElement) {
            collapseElement.addEventListener('show.bs.collapse', function() {
                button.setAttribute('aria-expanded', 'true');
            });
            
            collapseElement.addEventListener('hide.bs.collapse', function() {
                button.setAttribute('aria-expanded', 'false');
            });
        }
    });
});
</script>