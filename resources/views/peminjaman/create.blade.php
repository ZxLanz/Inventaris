<x-main-layout :title-page="__('Tambah Peminjaman')">
    <div class="row">
        <form class="card col-lg-8" action="{{ route('peminjaman.store') }}" method="POST">
            <div class="card-body">
                @csrf
                
                <div class="row">
                    <!-- Pilih Barang -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Barang <span class="text-danger">*</span></label>
                        <select name="barang_id" id="barang_id" class="form-select @error('barang_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach($barangs as $barang)
                                <option value="{{ $barang->id }}" 
                                        data-jenis="{{ $barang->jenis }}"
                                        data-stok="{{ $barang->is_asset ? $barang->assets->count() : $barang->stok_tersedia }}"
                                        data-satuan="{{ $barang->satuan }}"
                                        {{ old('barang_id') == $barang->id ? 'selected' : '' }}>
                                    {{ $barang->nama_barang }} 
                                    @if($barang->is_asset)
                                        (Tersedia: {{ $barang->assets->count() }} Unit)
                                    @else
                                        (Stok: {{ $barang->stok_tersedia }} {{ $barang->satuan }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('barang_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted" id="barangInfo"></small>
                    </div>
                    
                    <!-- Pilih Unit Asset (Dropdown 2) - Hidden by default -->
                    <div class="col-md-6 mb-3" id="assetWrapper" style="display: none;">
                        <label class="form-label">Pilih Unit <span class="text-danger">*</span></label>
                        <select name="barang_asset_id" id="barang_asset_id" class="form-select @error('barang_asset_id') is-invalid @enderror">
                            <option value="">-- Pilih Unit --</option>
                        </select>
                        @error('barang_asset_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted" id="assetInfo"></small>
                    </div>
                    
                    <!-- Jumlah Pinjam (hanya untuk consumable) -->
                    <div class="col-md-6 mb-3" id="jumlahWrapper" style="display: none;">
                        <x-form-input 
                            label="Jumlah Pinjam" 
                            name="jumlah_pinjam" 
                            type="number" 
                            :value="old('jumlah_pinjam', 1)" 
                            min="1"
                            id="jumlah_pinjam"
                        />
                    </div>
                </div>
                
                <div class="row">
                    <!-- Nama Peminjam -->
                    <div class="col-md-6 mb-3">
                        <x-form-input 
                            label="Nama Peminjam" 
                            name="nama_peminjam" 
                            :value="old('nama_peminjam')" 
                            required 
                            placeholder="Nama orang/instansi yang meminjam"
                        />
                    </div>
                    
                    <!-- Kontak Peminjam -->
                    <div class="col-md-6 mb-3">
                        <x-form-input 
                            label="Kontak Peminjam" 
                            name="kontak_peminjam" 
                            :value="old('kontak_peminjam')" 
                            placeholder="No. HP / Email"
                        />
                    </div>
                </div>
                
                <div class="row">
                    <!-- Lokasi Tujuan (Dipinjam Ke Mana) -->
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Dipinjam Ke Mana <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="lokasi_tujuan" 
                               class="form-control @error('lokasi_tujuan') is-invalid @enderror" 
                               value="{{ old('lokasi_tujuan') }}" 
                               placeholder="Contoh: Kantor Cabang Jakarta, Rumah, Meeting Client, dll"
                               required>
                        @error('lokasi_tujuan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i> Sebutkan lokasi atau tujuan peminjaman barang
                        </small>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Tanggal Pinjam -->
                    <div class="col-md-6 mb-3">
                        <x-form-input 
                            label="Tanggal Pinjam" 
                            name="tanggal_pinjam" 
                            type="date" 
                            :value="old('tanggal_pinjam', date('Y-m-d'))" 
                            required 
                        />
                    </div>
                    
                    <!-- Tanggal Jatuh Tempo -->
                    <div class="col-md-6 mb-3">
                        <x-form-input 
                            label="Tanggal Jatuh Tempo" 
                            name="tanggal_jatuh_tempo" 
                            type="date" 
                            :value="old('tanggal_jatuh_tempo')" 
                            required 
                        />
                    </div>
                </div>
                
                <!-- Keterangan -->
                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3" placeholder="Keperluan peminjaman atau catatan tambahan...">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="alert alert-info">
                    <strong>Catatan:</strong>
                    <ul class="mb-0">
                        <li>Peminjaman akan masuk ke status "Menunggu Approval"</li>
                        <li>Admin akan melakukan approval sebelum barang dapat dipinjam</li>
                        <li>Denda keterlambatan: Rp 5.000/hari</li>
                        <li id="assetNote" style="display: none;">Untuk barang Asset, pilih unit spesifik yang akan dipinjam</li>
                    </ul>
                </div>
                
                <div class="mt-4">
                    <x-primary-button>Ajukan Peminjaman</x-primary-button>
                    <x-tombol-kembali :href="route('peminjaman.index')" />
                </div>
            </div>
        </form>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const barangSelect = document.getElementById('barang_id');
            const assetWrapper = document.getElementById('assetWrapper');
            const jumlahWrapper = document.getElementById('jumlahWrapper');
            const assetSelect = document.getElementById('barang_asset_id');
            const jumlahInput = document.getElementById('jumlah_pinjam');
            const barangInfo = document.getElementById('barangInfo');
            const assetInfo = document.getElementById('assetInfo');
            const assetNote = document.getElementById('assetNote');
            
            // Event: Saat barang dipilih
            barangSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const barangId = this.value;
                const jenis = selectedOption.getAttribute('data-jenis');
                const stok = selectedOption.getAttribute('data-stok');
                const satuan = selectedOption.getAttribute('data-satuan');
                
                // Reset
                assetSelect.innerHTML = '<option value="">-- Pilih Unit --</option>';
                assetWrapper.style.display = 'none';
                jumlahWrapper.style.display = 'none';
                barangInfo.textContent = '';
                assetInfo.textContent = '';
                assetNote.style.display = 'none';
                
                if (!barangId) return;
                
                if (jenis === 'asset') {
                    // ASSET: Show dropdown unit, hide jumlah
                    assetWrapper.style.display = 'block';
                    jumlahWrapper.style.display = 'none';
                    assetNote.style.display = 'block';
                    jumlahInput.value = 1; // Set default
                    
                    // Fetch available assets via AJAX
                    fetch(`{{ route('peminjaman.getAssets') }}?barang_id=${barangId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.is_asset) {
                                if (data.assets.length > 0) {
                                    data.assets.forEach(asset => {
                                        const option = new Option(
                                            `${asset.kode_asset} - ${asset.kondisi} (${asset.lokasi})`,
                                            asset.id
                                        );
                                        assetSelect.add(option);
                                    });
                                    assetInfo.textContent = `${data.assets.length} unit tersedia`;
                                    assetInfo.style.color = 'green';
                                } else {
                                    assetInfo.textContent = 'Tidak ada unit tersedia';
                                    assetInfo.style.color = 'red';
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching assets:', error);
                            assetInfo.textContent = 'Gagal memuat data unit';
                            assetInfo.style.color = 'red';
                        });
                    
                } else {
                    // CONSUMABLE: Show jumlah, hide asset dropdown
                    assetWrapper.style.display = 'none';
                    jumlahWrapper.style.display = 'block';
                    assetNote.style.display = 'none';
                    
                    if (stok) {
                        barangInfo.textContent = `Stok tersedia: ${stok} ${satuan}`;
                        barangInfo.style.color = stok > 0 ? 'green' : 'red';
                        jumlahInput.max = stok;
                    }
                }
            });
            
            // Trigger on page load if old value exists
            if (barangSelect.value) {
                barangSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
</x-main-layout>