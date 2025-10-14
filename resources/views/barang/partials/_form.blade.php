
{{-- resources/views/barang/_form.blade.php --}}

<div class="row g-3">
    {{-- Jenis Barang --}}
    <div class="col-md-6">
        <label for="jenis" class="form-label">Jenis Barang <span class="text-danger">*</span></label>
        <select name="jenis" id="jenis" class="form-select @error('jenis') is-invalid @enderror" 
                {{ isset($barang) ? 'disabled' : '' }} required>
            <option value="">-- Pilih Jenis --</option>
            <option value="asset" {{ old('jenis', $barang->jenis ?? '') == 'asset' ? 'selected' : '' }}>
                Asset (dengan kode unik)
            </option>
            <option value="consumable" {{ old('jenis', $barang->jenis ?? '') == 'consumable' ? 'selected' : '' }}>
                Consumable (tanpa kode)
            </option>
        </select>
        @error('jenis')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="text-muted">
            <i class="bi bi-info-circle"></i> 
            Asset: Barang dengan kode serial (Laptop, Proyektor, dll). 
            Consumable: Barang habis pakai (Kertas, Tinta, dll)
        </small>
    </div>

    {{-- PREFIX - Hanya untuk Asset --}}
    <div class="col-md-6" id="prefix-wrapper" style="display: none;">
        <label for="prefix" class="form-label">Prefix Kode <span class="text-danger">*</span></label>
        <input type="text" 
               name="prefix" 
               id="prefix" 
               class="form-control text-uppercase @error('prefix') is-invalid @enderror" 
               value="{{ old('prefix', $barang->prefix ?? '') }}"
               placeholder="Contoh: LP, PR, MJ"
               maxlength="10"
               style="text-transform: uppercase;">
        @error('prefix')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="text-muted">
            <i class="bi bi-info-circle"></i> 
            Contoh: LP untuk Laptop, PR untuk Proyektor. 
            Sistem akan auto-generate: <strong id="preview-code">LP-001, LP-002, LP-003</strong>
        </small>
    </div>

    {{-- Nama Barang --}}
    <div class="col-md-6">
        <label for="nama_barang" class="form-label">Nama Barang <span class="text-danger">*</span></label>
        <input type="text" 
               name="nama_barang" 
               id="nama_barang" 
               class="form-control @error('nama_barang') is-invalid @enderror" 
               value="{{ old('nama_barang', $barang->nama_barang ?? '') }}" 
               placeholder="Contoh: Laptop Asus ROG"

<!-- File: resources/views/barang/partials/_form.blade.php -->

<div class="row">
    <!-- Kode Barang -->
    <div class="col-md-6 mb-3">
        <label for="kode_barang" class="form-label">Kode Barang</label>
        <input type="text" 
               class="form-control @error('kode_barang') is-invalid @enderror" 
               id="kode_barang" 
               name="kode_barang" 
               value="{{ old('kode_barang', $barang->kode_barang ?? '') }}" 
               required>
        @error('kode_barang')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Nama Barang -->
    <div class="col-md-6 mb-3">
        <label for="nama_barang" class="form-label">Nama Barang</label>
        <input type="text" 
               class="form-control @error('nama_barang') is-invalid @enderror" 
               id="nama_barang" 
               name="nama_barang" 
               value="{{ old('nama_barang', $barang->nama_barang ?? '') }}" 

               required>
        @error('nama_barang')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


    {{-- Kategori --}}
    <div class="col-md-6">
        <label for="kategori_id" class="form-label">Kategori <span class="text-danger">*</span></label>
        <select name="kategori_id" id="kategori_id" class="form-select @error('kategori_id') is-invalid @enderror" required>
            <option value="">-- Pilih Kategori --</option>
            @foreach($kategoris as $kategori)
                <option value="{{ $kategori->id }}" 
                    {{ old('kategori_id', $barang->kategori_id ?? '') == $kategori->id ? 'selected' : '' }}>

    <!-- Kategori -->
    <div class="col-md-6 mb-3">
        <label for="kategori_id" class="form-label">Kategori</label>
        <select class="form-select @error('kategori_id') is-invalid @enderror" 
                id="kategori_id" 
                name="kategori_id" 
                required>
            <option value="">Pilih Kategori</option>
            @foreach($kategoris as $kategori)
                <option value="{{ $kategori->id }}" 
                        {{ old('kategori_id', $barang->kategori_id ?? '') == $kategori->id ? 'selected' : '' }}>

                    {{ $kategori->nama_kategori }}
                </option>
            @endforeach
        </select>
        @error('kategori_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


    {{-- Lokasi --}}
    <div class="col-md-6">
        <label for="lokasi_id" class="form-label">Lokasi <span class="text-danger">*</span></label>
        <select name="lokasi_id" id="lokasi_id" class="form-select @error('lokasi_id') is-invalid @enderror" required>
            <option value="">-- Pilih Lokasi --</option>
            @foreach($lokasis as $lokasi)
                <option value="{{ $lokasi->id }}" 
                    {{ old('lokasi_id', $barang->lokasi_id ?? '') == $lokasi->id ? 'selected' : '' }}>

    <!-- Lokasi -->
    <div class="col-md-6 mb-3">
        <label for="lokasi_id" class="form-label">Lokasi</label>
        <select class="form-select @error('lokasi_id') is-invalid @enderror" 
                id="lokasi_id" 
                name="lokasi_id" 
                required>
            <option value="">Pilih Lokasi</option>
            @foreach($lokasis as $lokasi)
                <option value="{{ $lokasi->id }}" 
                        {{ old('lokasi_id', $barang->lokasi_id ?? '') == $lokasi->id ? 'selected' : '' }}>

                    {{ $lokasi->nama_lokasi }}
                </option>
            @endforeach
        </select>
        @error('lokasi_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


    {{-- Jumlah --}}
    <div class="col-md-6">
        <label for="jumlah" class="form-label">
            Jumlah <span class="text-danger">*</span>
            <span id="jumlah-label-info" style="display: none;">(Unit yang akan dibuat)</span>
        </label>
        <input type="number" 
               name="jumlah" 
               id="jumlah" 
               class="form-control @error('jumlah') is-invalid @enderror" 
               value="{{ old('jumlah', $barang->jumlah ?? 1) }}" 
               min="1" 

    <!-- Jumlah -->
    <div class="col-md-6 mb-3">
        <label for="jumlah" class="form-label">Jumlah</label>
        <input type="number" 
               class="form-control @error('jumlah') is-invalid @enderror" 
               id="jumlah" 
               name="jumlah" 
               value="{{ old('jumlah', $barang->jumlah ?? '') }}" 
               min="0" 

               required>
        @error('jumlah')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <small class="text-muted" id="jumlah-hint">
            <i class="bi bi-info-circle"></i> 
            <span id="hint-asset" style="display: none;">
                Jumlah unit yang akan dibuat. Contoh: 5 = akan membuat 5 kode unik
            </span>
            <span id="hint-consumable" style="display: none;">
                Jumlah stok barang consumable
            </span>
        </small>
    </div>

    {{-- Satuan --}}
    <div class="col-md-6">
        <label for="satuan" class="form-label">Satuan <span class="text-danger">*</span></label>
        <input type="text" 
               name="satuan" 
               id="satuan" 
               class="form-control @error('satuan') is-invalid @enderror" 
               value="{{ old('satuan', $barang->satuan ?? 'Unit') }}" 
               placeholder="Contoh: Unit, Pcs, Buah"

    </div>

    <!-- Satuan -->
    <div class="col-md-6 mb-3">
        <label for="satuan" class="form-label">Satuan</label>
        <input type="text" 
               class="form-control @error('satuan') is-invalid @enderror" 
               id="satuan" 
               name="satuan" 
               value="{{ old('satuan', $barang->satuan ?? '') }}" 
               placeholder="Unit, Pcs, Set, dll"

               required>
        @error('satuan')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


    {{-- Kondisi - Hanya untuk Asset --}}
    <div class="col-md-6" id="kondisi-wrapper" style="display: none;">
        <label for="kondisi" class="form-label">Kondisi <span class="text-danger">*</span></label>
        <select name="kondisi" id="kondisi" class="form-select @error('kondisi') is-invalid @enderror">
            <option value="">-- Pilih Kondisi --</option>
            <option value="Baik" {{ old('kondisi', $barang->kondisi ?? '') == 'Baik' ? 'selected' : '' }}>
                Baik
            </option>
            <option value="Rusak Ringan" {{ old('kondisi', $barang->kondisi ?? '') == 'Rusak Ringan' ? 'selected' : '' }}>
                Rusak Ringan
            </option>
            <option value="Rusak Berat" {{ old('kondisi', $barang->kondisi ?? '') == 'Rusak Berat' ? 'selected' : '' }}>
                Rusak Berat
            </option>

    <!-- Kondisi -->
    <div class="col-md-6 mb-3">
        <label for="kondisi" class="form-label">Kondisi</label>
        <select class="form-select @error('kondisi') is-invalid @enderror" 
                id="kondisi" 
                name="kondisi" 
                required>
            <option value="">Pilih Kondisi</option>
            @foreach(['Baik', 'Rusak Ringan', 'Rusak Berat'] as $kondisiOption)
                <option value="{{ $kondisiOption }}" 
                        {{ old('kondisi', $barang->kondisi ?? '') == $kondisiOption ? 'selected' : '' }}>
                    {{ $kondisiOption }}
                </option>
            @endforeach

        </select>
        @error('kondisi')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


    {{-- Tanggal Pengadaan --}}
    <div class="col-md-6">
        <label for="tanggal_pengadaan" class="form-label">Tanggal Pengadaan <span class="text-danger">*</span></label>
        <input type="date" 
               name="tanggal_pengadaan" 
               id="tanggal_pengadaan" 
               class="form-control @error('tanggal_pengadaan') is-invalid @enderror" 
               value="{{ old('tanggal_pengadaan', $barang->tanggal_pengadaan ?? date('Y-m-d')) }}" 

    <!-- Tanggal Pengadaan -->
    <div class="col-md-6 mb-3">
        <label for="tanggal_pengadaan" class="form-label">Tanggal Pengadaan</label>
        <input type="date" 
               class="form-control @error('tanggal_pengadaan') is-invalid @enderror" 
               id="tanggal_pengadaan" 
               name="tanggal_pengadaan" 
               value="{{ old('tanggal_pengadaan', isset($barang) && $barang->tanggal_pengadaan ? $barang->tanggal_pengadaan->format('Y-m-d') : '') }}" 

               required>
        @error('tanggal_pengadaan')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


    {{-- Sumber Barang --}}
    <div class="col-md-6">
        <label for="sumber_barang" class="form-label">Sumber Barang</label>
        <select name="sumber_barang" id="sumber_barang" class="form-select @error('sumber_barang') is-invalid @enderror">
            <option value="">-- Pilih Sumber --</option>
            <option value="Pembelian" {{ old('sumber_barang', $barang->sumber_barang ?? '') == 'Pembelian' ? 'selected' : '' }}>
                Pembelian
            </option>
            <option value="Hibah" {{ old('sumber_barang', $barang->sumber_barang ?? '') == 'Hibah' ? 'selected' : '' }}>
                Hibah
            </option>
            <option value="Donasi" {{ old('sumber_barang', $barang->sumber_barang ?? '') == 'Donasi' ? 'selected' : '' }}>
                Donasi
            </option>
            <option value="Bantuan Pemerintah" {{ old('sumber_barang', $barang->sumber_barang ?? '') == 'Bantuan Pemerintah' ? 'selected' : '' }}>
                Bantuan Pemerintah
            </option>
            <option value="Lainnya" {{ old('sumber_barang', $barang->sumber_barang ?? '') == 'Lainnya' ? 'selected' : '' }}>
                Lainnya
            </option>
        </select>
        @error('sumber_barang')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="text-muted">
            <i class="bi bi-info-circle"></i> Opsional - Asal usul barang
        </small>
    </div>

    {{-- Gambar --}}
    <div class="col-md-12">
        <label for="gambar" class="form-label">Gambar Barang</label>
        <input type="file" 
               name="gambar" 
               id="gambar" 
               class="form-control @error('gambar') is-invalid @enderror" 
               accept="image/*"
               onchange="previewImage(event)">
        @error('gambar')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        
        {{-- Preview Gambar --}}
        <div class="mt-3" id="image-preview-wrapper">
            @if(isset($barang) && $barang->gambar)
                <img src="{{ asset('gambar-barang/' . $barang->gambar) }}" 
                     alt="Preview" 
                     id="image-preview" 
                     class="img-thumbnail" 
                     style="max-height: 200px;">
            @else
                <img src="" 
                     alt="Preview" 
                     id="image-preview" 
                     class="img-thumbnail" 
                     style="max-height: 200px; display: none;">
            @endif
        </div>
    </div>

    {{-- Deskripsi --}}
    <div class="col-md-12">
        <label for="keterangan" class="form-label">Deskripsi</label>
        <textarea name="keterangan" 
                  id="keterangan" 
                  class="form-control @error('keterangan') is-invalid @enderror" 
                  rows="3" 
                  placeholder="Tambahkan deskripsi atau catatan tambahan...">{{ old('keterangan', $barang->keterangan ?? '') }}</textarea>
        @error('keterangan')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- Action Buttons --}}
<div class="row mt-4">
    <div class="col-md-12">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> {{ isset($barang) ? 'Update' : 'Simpan' }}
        </button>
        <a href="{{ route('barang.index') }}" class="btn btn-secondary">
            <i class="bi bi-x-circle"></i> Batal
        </a>
    </div>
</div>

{{-- JavaScript untuk Dynamic Form --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const jenisSelect = document.getElementById('jenis');
    const prefixWrapper = document.getElementById('prefix-wrapper');
    const kondisiWrapper = document.getElementById('kondisi-wrapper');
    const prefixInput = document.getElementById('prefix');
    const kondisiSelect = document.getElementById('kondisi');
    const jumlahLabelInfo = document.getElementById('jumlah-label-info');
    const hintAsset = document.getElementById('hint-asset');
    const hintConsumable = document.getElementById('hint-consumable');
    const previewCode = document.getElementById('preview-code');
    const jumlahInput = document.getElementById('jumlah');

    // Fungsi toggle field berdasarkan jenis
    function toggleFields() {
        const jenis = jenisSelect.value;
        
        if (jenis === 'asset') {
            // Show asset fields
            prefixWrapper.style.display = 'block';
            kondisiWrapper.style.display = 'block';
            jumlahLabelInfo.style.display = 'inline';
            hintAsset.style.display = 'inline';
            hintConsumable.style.display = 'none';
            
            // Set required
            prefixInput.setAttribute('required', 'required');
            kondisiSelect.setAttribute('required', 'required');
            
        } else if (jenis === 'consumable') {
            // Hide asset fields
            prefixWrapper.style.display = 'none';
            kondisiWrapper.style.display = 'none';
            jumlahLabelInfo.style.display = 'none';
            hintAsset.style.display = 'none';
            hintConsumable.style.display = 'inline';
            
            // Remove required
            prefixInput.removeAttribute('required');
            kondisiSelect.removeAttribute('required');
            
            // Clear values
            prefixInput.value = '';
            kondisiSelect.value = '';
        }
    }

    // Fungsi update preview kode
    function updatePreview() {
        const prefix = prefixInput.value.toUpperCase() || 'XXX';
        const jumlah = parseInt(jumlahInput.value) || 1;
        
        let preview = '';
        if (jumlah <= 3) {
            // Show all if ≤ 3
            for (let i = 1; i <= jumlah; i++) {
                preview += prefix + '-' + String(i).padStart(3, '0');
                if (i < jumlah) preview += ', ';
            }
        } else {
            // Show first 3 with ellipsis
            preview = prefix + '-001, ' + prefix + '-002, ' + prefix + '-003, ...';
        }
        
        previewCode.textContent = preview;
    }

    // Event listeners
    jenisSelect.addEventListener('change', toggleFields);
    prefixInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
        updatePreview();
    });
    jumlahInput.addEventListener('input', updatePreview);

    // Initial state
    toggleFields();
    updatePreview();
});

// Preview gambar
function previewImage(event) {
    const reader = new FileReader();
    const imagePreview = document.getElementById('image-preview');
    
    reader.onload = function() {
        imagePreview.src = reader.result;
        imagePreview.style.display = 'block';
    }
    
    if (event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    }
}
</script>

<style>
/* Styling untuk form */
.form-label {
    font-weight: 600;
    color: #495057;
}

.text-danger {
    color: #dc3545;
}

.form-control:focus,
.form-select:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

.text-uppercase {
    text-transform: uppercase !important;
}

#preview-code {
    color: #007bff;
    font-family: monospace;
}

.img-thumbnail {
    border-radius: 8px;
}
</style>

    <!-- Gambar Barang -->
    <div class="col-12 mb-3">
        <label for="gambar" class="form-label">Gambar Barang</label>
        <input type="file" 
               class="form-control @error('gambar') is-invalid @enderror" 
               id="gambar" 
               name="gambar" 
               accept="image/*">
        <div class="form-text">Format: JPG, PNG, GIF. Maksimal 2MB</div>
        @error('gambar')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mt-4">
<x-primary-button>
{{ isset ($update) ? _ ('Update') : _ ('Simpan') }}
</x-primary-button>
<x-tombol-kembali : href="{{ route('barang.index') }}" />
</div>

