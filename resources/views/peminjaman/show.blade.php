<x-main-layout :title-page="'Detail Peminjaman: ' . $peminjaman->kode_peminjaman">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Informasi Peminjaman</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="200">Kode Peminjaman</th>
                            <td>: <strong>{{ $peminjaman->kode_peminjaman }}</strong></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>: 
                                @if($peminjaman->status == 'Menunggu Approval')
                                    <span class="badge bg-warning">{{ $peminjaman->status }}</span>
                                @elseif($peminjaman->status == 'Ditolak')
                                    <span class="badge bg-danger">{{ $peminjaman->status }}</span>
                                @elseif($peminjaman->status == 'Dipinjam')
                                    <span class="badge bg-primary">{{ $peminjaman->status }}</span>
                                @elseif($peminjaman->status == 'Terlambat')
                                    <span class="badge bg-danger">{{ $peminjaman->status }}</span>
                                @elseif($peminjaman->status == 'Dikembalikan')
                                    <span class="badge bg-secondary">{{ $peminjaman->status }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Nama Peminjam</th>
                            <td>: {{ $peminjaman->nama_peminjam }}</td>
                        </tr>
                        <tr>
                            <th>Kontak Peminjam</th>
                            <td>: {{ $peminjaman->kontak_peminjam ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Dipinjam Ke</th>
                            <td>: <strong class="text-primary">{{ $peminjaman->lokasi_tujuan ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <th>Barang</th>
                            <td>: {{ $peminjaman->barang->nama_barang }}
                                @if($peminjaman->barang->is_asset)
                                    <span class="badge bg-info text-dark">Asset</span>
                                @else
                                    <span class="badge bg-warning text-dark">Consumable</span>
                                @endif
                            </td>
                        </tr>
                        @if($peminjaman->barang->is_asset && $peminjaman->barangAsset)
                        <tr>
                            <th>Kode Unit</th>
                            <td>: <span class="badge bg-primary">{{ $peminjaman->barangAsset->kode_asset }}</span></td>
                        </tr>
                        <tr>
                            <th>Kondisi Unit</th>
                            <td>: 
                                @switch($peminjaman->barangAsset->kondisi)
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
                            <th>Lokasi Unit Saat Ini</th>
                            <td>: {{ $peminjaman->barangAsset->lokasi->nama_lokasi ?? '-' }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Kategori</th>
                            <td>: {{ $peminjaman->barang->kategori->nama_kategori }}</td>
                        </tr>
                        <tr>
                            <th>Lokasi Asal</th>
                            <td>: {{ $peminjaman->barang->lokasi->nama_lokasi }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah Pinjam</th>
                            <td>: {{ $peminjaman->jumlah_pinjam }} {{ $peminjaman->barang->satuan }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Pinjam</th>
                            <td>: {{ $peminjaman->tanggal_pinjam->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Jatuh Tempo</th>
                            <td>: {{ $peminjaman->tanggal_jatuh_tempo->format('d F Y') }}</td>
                        </tr>
                        @if($peminjaman->tanggal_kembali)
                        <tr>
                            <th>Tanggal Dikembalikan</th>
                            <td>: {{ $peminjaman->tanggal_kembali->format('d F Y') }}</td>
                        </tr>
                        @endif
                        @if($peminjaman->hari_terlambat > 0)
                        <tr>
                            <th>Keterlambatan</th>
                            <td>: <span class="text-danger">{{ $peminjaman->hari_terlambat }} hari</span></td>
                        </tr>
                        @endif
                        @if($peminjaman->total_denda > 0)
                        <tr>
                            <th>Total Denda</th>
                            <td>: <span class="text-danger fw-bold">Rp {{ number_format($peminjaman->total_denda, 0, ',', '.') }}</span></td>
                        </tr>
                        @endif
                        @if($peminjaman->keterangan)
                        <tr>
                            <th>Keterangan</th>
                            <td>: {{ $peminjaman->keterangan }}</td>
                        </tr>
                        @endif
                        @if($peminjaman->status == 'Ditolak' && $peminjaman->alasan_ditolak)
                        <tr>
                            <th>Alasan Ditolak</th>
                            <td>: <span class="text-danger">{{ $peminjaman->alasan_ditolak }}</span></td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Tracking</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="200">Diinput Oleh</th>
                            <td>: {{ $peminjaman->user->name }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Input</th>
                            <td>: {{ $peminjaman->created_at->format('d F Y H:i') }}</td>
                        </tr>
                        @if($peminjaman->approved_by)
                        <tr>
                            <th>Di-{{ $peminjaman->status == 'Ditolak' ? 'tolak' : 'approve' }} Oleh</th>
                            <td>: {{ $peminjaman->approver->name }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal {{ $peminjaman->status == 'Ditolak' ? 'Penolakan' : 'Approval' }}</th>
                            <td>: {{ $peminjaman->approved_at->format('d F Y H:i') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
            
            <div class="mt-3">
                @can('approve peminjaman')
                    @if($peminjaman->status == 'Menunggu Approval')
                        <form action="{{ route('peminjaman.approve', $peminjaman->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-success" onclick="return confirm('Setujui peminjaman ini?')">
                                <i class="bi bi-check-circle"></i> Setujui
                            </button>
                        </form>
                        
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="bi bi-x-circle"></i> Tolak
                        </button>
                    @endif
                    
                    @if(in_array($peminjaman->status, ['Dipinjam', 'Terlambat']))
                        <a href="{{ route('peminjaman.return', $peminjaman->id) }}" class="btn btn-warning">
                            <i class="bi bi-arrow-return-left"></i> Proses Pengembalian
                        </a>
                    @endif
                @endcan
                
                <x-tombol-kembali :href="route('peminjaman.index')" />
            </div>
        </div>
    </div>
    
    <!-- Modal Reject -->
    @can('approve peminjaman')
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('peminjaman.reject', $peminjaman->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tolak Peminjaman</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
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
</x-main-layout>