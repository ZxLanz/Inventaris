<x-main-layout :title-page="'Pengembalian Barang: ' . $peminjaman->kode_peminjaman">
    <div class="row">
        <div class="col-lg-8">
            <!-- Info Peminjaman -->
            <div class="card mb-3">
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
                            <th>Peminjam</th>
                            <td>: {{ $peminjaman->nama_peminjam }}</td>
                        </tr>
                        {{-- Lokasi Tujuan --}}
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
                        {{-- Kode Unit Asset --}}
                        @if($peminjaman->barang->is_asset && $peminjaman->barangAsset)
                        <tr>
                            <th>Kode Unit</th>
                            <td>: <span class="badge bg-primary">{{ $peminjaman->barangAsset->kode_asset }}</span></td>
                        </tr>
                        @endif
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
                    </table>
                </div>
            </div>

            <!-- Form Pengembalian -->
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">Form Pengembalian</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('peminjaman.processReturn', $peminjaman->id) }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Tanggal Kembali <span class="text-danger">*</span></label>
                            <input type="date" 
                                   name="tanggal_kembali" 
                                   class="form-control @error('tanggal_kembali') is-invalid @enderror" 
                                   value="{{ old('tanggal_kembali', date('Y-m-d')) }}"
                                   min="{{ $peminjaman->tanggal_pinjam->format('Y-m-d') }}"
                                   id="tanggalKembali"
                                   required>
                            @error('tanggal_kembali')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan (Kondisi Barang)</label>
                            <textarea name="keterangan" 
                                      class="form-control @error('keterangan') is-invalid @enderror" 
                                      rows="3" 
                                      placeholder="Misal: Barang dikembalikan dalam kondisi baik">{{ old('keterangan', $peminjaman->keterangan) }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Info Denda -->
                        <div class="alert alert-info">
                            <h6 class="alert-heading">Perhitungan Denda:</h6>
                            <div id="dendaInfo">
                                <p class="mb-1">Tanggal Jatuh Tempo: <strong>{{ $peminjaman->tanggal_jatuh_tempo->format('d F Y') }}</strong></p>
                                <p class="mb-1">Denda per Hari: <strong>Rp 5.000</strong></p>
                                <hr>
                                <p class="mb-0" id="hitungDenda">Pilih tanggal kembali untuk menghitung denda...</p>
                            </div>
                        </div>
                        
                        {{-- Info Asset --}}
                        @if($peminjaman->barang->is_asset && $peminjaman->barangAsset)
                        <div class="alert alert-success">
                            <h6 class="alert-heading">
                                <i class="bi bi-info-circle"></i> Informasi Asset
                            </h6>
                            <p class="mb-1">Setelah pengembalian diproses:</p>
                            <ul class="mb-0">
                                <li>Status asset <strong>{{ $peminjaman->barangAsset->kode_asset }}</strong> akan otomatis berubah menjadi <span class="badge bg-success">Tersedia</span></li>
                                <li>Asset dapat dipinjam kembali oleh orang lain</li>
                            </ul>
                        </div>
                        @endif

                        <div class="mt-4">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-arrow-return-left"></i> Proses Pengembalian
                            </button>
                            <x-tombol-kembali :href="route('peminjaman.show', $peminjaman->id)" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto calculate denda
        document.getElementById('tanggalKembali').addEventListener('change', function() {
            const tanggalKembali = new Date(this.value);
            const tanggalJatuhTempo = new Date('{{ $peminjaman->tanggal_jatuh_tempo->format('Y-m-d') }}');
            const hitungDendaEl = document.getElementById('hitungDenda');
            
            // Hitung selisih hari
            const diffTime = tanggalKembali - tanggalJatuhTempo;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if (diffDays > 0) {
                // Terlambat
                const denda = diffDays * 5000;
                hitungDendaEl.innerHTML = `
                    <strong class="text-danger">Terlambat ${diffDays} hari</strong><br>
                    <strong class="text-danger">Total Denda: Rp ${denda.toLocaleString('id-ID')}</strong>
                `;
            } else {
                // Tepat waktu
                hitungDendaEl.innerHTML = `
                    <strong class="text-success">
                    <strong class="text-success">Dikembalikan tepat waktu</strong><br>
                    <strong class="text-success">Tidak ada denda</strong>
                `;
            }
        });
        
        // Trigger calculation on page load if date is set
        window.addEventListener('load', function() {
            const tanggalKembaliInput = document.getElementById('tanggalKembali');
            if (tanggalKembaliInput.value) {
                tanggalKembaliInput.dispatchEvent(new Event('change'));
            }
        });
    </script>
</x-main-layout>