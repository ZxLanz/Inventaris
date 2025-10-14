<x-table-list>
    <x-slot name="header">
        <tr>
            <th>#</th>
            <th>Kode</th>
            <th>Peminjam</th>
            <th>Barang</th>
            <th>Kode Unit</th>
            <th>Jumlah</th>
            <th>Tanggal Pinjam</th>
            <th>Jatuh Tempo</th>
            <th>Status</th>
            <th>Denda</th>
            <th>Aksi</th>
        </tr>
    </x-slot>

    @forelse ($peminjaman as $index => $item)
        <tr>
            <td>{{ $peminjaman->firstItem() + $index }}</td>
            <td><strong>{{ $item->kode_peminjaman }}</strong></td>
            <td>
                {{ $item->nama_peminjam }}<br>
                <small class="text-muted">{{ $item->kontak_peminjam }}</small>
            </td>
            <td>
                {{ $item->barang->nama_barang }}
                @if($item->barang->is_asset)
                    <span class="badge bg-info text-dark">Asset</span>
                @else
                    <span class="badge bg-warning text-dark">Consumable</span>
                @endif
            </td>
            <td>
                @if($item->barang->is_asset && $item->barangAsset)
                    <span class="badge bg-primary">{{ $item->barangAsset->kode_asset }}</span>
                @else
                    <span class="text-muted">-</span>
                @endif
            </td>
            <td>{{ $item->jumlah_pinjam }} {{ $item->barang->satuan }}</td>
            <td>{{ $item->tanggal_pinjam->format('d/m/Y') }}</td>
            <td>{{ $item->tanggal_jatuh_tempo->format('d/m/Y') }}</td>
            <td>
                @if($item->status == 'Menunggu Approval')
                    <span class="badge bg-warning">{{ $item->status }}</span>
                @elseif($item->status == 'Ditolak')
                    <span class="badge bg-danger">{{ $item->status }}</span>
                @elseif($item->status == 'Dipinjam')
                    <span class="badge bg-primary">{{ $item->status }}</span>
                @elseif($item->status == 'Terlambat')
                    <span class="badge bg-danger">{{ $item->status }}</span>
                @elseif($item->status == 'Dikembalikan')
                    <span class="badge bg-secondary">{{ $item->status }}</span>
                @endif
            </td>
            <td>
                @if($item->total_denda > 0)
                    <span class="text-danger">Rp {{ number_format($item->total_denda, 0, ',', '.') }}</span>
                @else
                    -
                @endif
            </td>
            <td>
                <div class="btn-group btn-group-sm" role="group">
                    <!-- Detail -->
                    <a href="{{ route('peminjaman.show', $item->id) }}" class="btn btn-info btn-sm">
                        Detail
                    </a>
                    
                    @can('approve peminjaman')
                        @if($item->status == 'Menunggu Approval')
                            <!-- Approve -->
                            <form action="{{ route('peminjaman.approve', $item->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Setujui peminjaman ini?')">
                                    Setujui
                                </button>
                            </form>
                            
                            <!-- Reject -->
                            <button type="button" class="btn btn-danger btn-sm" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#rejectModal{{ $item->id }}">
                                Tolak
                            </button>
                        @endif
                        
                        {{-- ✅ TOMBOL KEMBALIKAN (untuk status Dipinjam/Terlambat) --}}
                        @if(in_array($item->status, ['Dipinjam', 'Terlambat']))
                            <a href="{{ route('peminjaman.return', $item->id) }}" class="btn btn-warning btn-sm">
                                <i class="bi bi-arrow-return-left"></i> Kembalikan
                            </a>
                        @endif
                    @endcan
                </div>
            </td>
        </tr>
        
        <!-- Modal Reject -->
        @can('approve peminjaman')
        <div class="modal fade" id="rejectModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('peminjaman.reject', $item->id) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Tolak Peminjaman</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Kode: <strong>{{ $item->kode_peminjaman }}</strong></p>
                            <p>Peminjam: <strong>{{ $item->nama_peminjam }}</strong></p>
                            <div class="mb-3">
                                <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                <textarea name="alasan_ditolak" class="form-control" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Tolak Peminjaman</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endcan
        
    @empty
        <tr>
            <td colspan="11" class="text-center">
                <div class="alert alert-warning">
                    Data peminjaman belum tersedia.
                </div>
            </td>
        </tr>
    @endforelse
</x-table-list>
