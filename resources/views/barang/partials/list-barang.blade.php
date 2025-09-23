{{-- File: resources/views/barang/partials/list-barang.blade.php --}}

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Lokasi</th>
                        <th>Jumlah</th>
                        <th>Kondisi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($barangs as $index => $barang)
                    <tr>
                        <td>{{ $barangs->firstItem() + $index }}</td>
                        <td>{{ $barang->kode_barang }}</td>
                        <td>{{ $barang->nama_barang }}</td>
                        <td>{{ $barang->kategori->nama_kategori ?? '-' }}</td>
                        <td>{{ $barang->lokasi->nama_lokasi ?? '-' }}</td>
                        <td>{{ $barang->jumlah }} {{ $barang->satuan }}</td>
                        <td>
                            @if($barang->kondisi == 'Baik')
                                <span class="badge bg-success">{{ $barang->kondisi }}</span>
                            @elseif($barang->kondisi == 'Rusak Ringan')
                                <span class="badge bg-warning text-dark">{{ $barang->kondisi }}</span>
                            @else
                                <span class="badge bg-danger">{{ $barang->kondisi }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <!-- Tombol Lihat Detail -->
                                <a href="{{ route('barang.show', $barang->id) }}" 
                                   class="btn btn-info btn-sm" 
                                   title="Lihat Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                
                                <!-- Tombol Edit -->
                                <a href="{{ route('barang.edit', $barang->id) }}" 
                                   class="btn btn-warning btn-sm" 
                                   title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                
                                <!-- Tombol Hapus -->
                                <button type="button" 
                                        class="btn btn-danger btn-sm" 
                                        title="Hapus"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteModal"
                                        data-url="{{ route('barang.destroy', $barang->id) }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            <div class="py-4">
                                <i class="bi bi-box-seam fa-3x mb-3"></i>
                                <p class="mb-0">Tidak ada data barang ditemukan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>