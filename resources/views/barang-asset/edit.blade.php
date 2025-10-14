<x-main-layout :title-page="'Edit Asset - ' . $barangAsset->kode_asset">
    <form class="card" action="{{ route('barang-asset.update', $barangAsset->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="card-body">
            <x-notif-alert class="mb-4" />

            {{-- Info Asset Header --}}
            <div class="alert alert-info mb-4">
                <div class="row">
                    <div class="col-md-4">
                        <strong>Kode Asset:</strong><br>
                        <span class="badge bg-primary fs-6">{{ $barangAsset->kode_asset }}</span>
                    </div>
                    <div class="col-md-8">
                        <strong>Nama Barang:</strong><br>
                        {{ $barangAsset->barang->nama_barang }}
                    </div>
                </div>
            </div>

            {{-- Form Fields --}}
            <div class="row g-3">
                {{-- Lokasi --}}
                <div class="col-md-6">
                    <label for="lokasi_id" class="form-label">
                        Lokasi <span class="text-danger">*</span>
                    </label>
                    <select name="lokasi_id" 
                            id="lokasi_id" 
                            class="form-select @error('lokasi_id') is-invalid @enderror" 
                            required>
                        <option value="">-- Pilih Lokasi --</option>
                        @foreach($lokasis as $lokasi)
                            <option value="{{ $lokasi->id }}" 
                                {{ old('lokasi_id', $barangAsset->lokasi_id) == $lokasi->id ? 'selected' : '' }}>
                                {{ $lokasi->nama_lokasi }}
                            </option>
                        @endforeach
                    </select>
                    @error('lokasi_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Kondisi --}}
                <div class="col-md-6">
                    <label for="kondisi" class="form-label">
                        Kondisi <span class="text-danger">*</span>
                    </label>
                    <select name="kondisi" 
                            id="kondisi" 
                            class="form-select @error('kondisi') is-invalid @enderror" 
                            required>
                        <option value="">-- Pilih Kondisi --</option>
                        <option value="Baik" 
                            {{ old('kondisi', $barangAsset->kondisi) == 'Baik' ? 'selected' : '' }}>
                            Baik
                        </option>
                        <option value="Rusak Ringan" 
                            {{ old('kondisi', $barangAsset->kondisi) == 'Rusak Ringan' ? 'selected' : '' }}>
                            Rusak Ringan
                        </option>
                        <option value="Rusak Berat" 
                            {{ old('kondisi', $barangAsset->kondisi) == 'Rusak Berat' ? 'selected' : '' }}>
                            Rusak Berat
                        </option>
                    </select>
                    @error('kondisi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="col-md-6">
                    <label for="status" class="form-label">
                        Status <span class="text-danger">*</span>
                    </label>
                    <select name="status" 
                            id="status" 
                            class="form-select @error('status') is-invalid @enderror" 
                            required>
                        <option value="">-- Pilih Status --</option>
                        <option value="tersedia" 
                            {{ old('status', $barangAsset->status) == 'tersedia' ? 'selected' : '' }}>
                            Tersedia
                        </option>
                        <option value="dipinjam" 
                            {{ old('status', $barangAsset->status) == 'dipinjam' ? 'selected' : '' }}
                            disabled>
                            Dipinjam (Diatur otomatis)
                        </option>
                        <option value="maintenance" 
                            {{ old('status', $barangAsset->status) == 'maintenance' ? 'selected' : '' }}>
                            Maintenance
                        </option>
                        <option value="rusak" 
                            {{ old('status', $barangAsset->status) == 'rusak' ? 'selected' : '' }}>
                            Rusak
                        </option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i> Status "Dipinjam" diatur otomatis oleh sistem
                    </small>
                </div>

                {{-- Tanggal Pengadaan --}}
                <div class="col-md-6">
                    <label for="tanggal_pengadaan" class="form-label">
                        Tanggal Pengadaan
                    </label>
                    <input type="text" 
                           class="form-control" 
                           value="{{ \Carbon\Carbon::parse($barangAsset->tanggal_pengadaan)->format('d F Y') }}" 
                           readonly>
                    <small class="text-muted">
                        <i class="bi bi-lock"></i> Tanggal tidak dapat diubah
                    </small>
                </div>

                {{-- Gambar --}}
                <div class="col-md-12">
                    <label for="gambar" class="form-label">Gambar Asset</label>
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
                        @if($barangAsset->gambar)
                            <img src="{{ asset('gambar-barang/' . $barangAsset->gambar) }}" 
                                 alt="Current Image" 
                                 id="current-image" 
                                 class="img-thumbnail" 
                                 style="max-height: 200px;">
                        @endif
                        <img src="" 
                             alt="Preview" 
                             id="image-preview" 
                             class="img-thumbnail" 
                             style="max-height: 200px; display: none;">
                    </div>
                </div>

                {{-- Keterangan --}}
                <div class="col-md-12">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <textarea name="keterangan" 
                              id="keterangan" 
                              class="form-control @error('keterangan') is-invalid @enderror" 
                              rows="3" 
                              placeholder="Tambahkan keterangan atau catatan...">{{ old('keterangan', $barangAsset->keterangan) }}</textarea>
                    @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="row mt-4">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update
                    </button>
                    <a href="{{ route('barang.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Batal
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
    </form>

    {{-- Hidden Delete Form --}}
    <form id="delete-form" 
          action="{{ route('barang-asset.destroy', $barangAsset->id) }}" 
          method="POST" 
          style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</x-main-layout>

{{-- JavaScript --}}
<script>
// Preview gambar
function previewImage(event) {
    const reader = new FileReader();
    const imagePreview = document.getElementById('image-preview');
    const currentImage = document.getElementById('current-image');
    
    reader.onload = function() {
        imagePreview.src = reader.result;
        imagePreview.style.display = 'block';
        
        if (currentImage) {
            currentImage.style.display = 'none';
        }
    }
    
    if (event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    }
}

// Confirm delete
function confirmDelete() {
    if (confirm('Yakin ingin menghapus asset {{ $barangAsset->kode_asset }}?\n\nTindakan ini tidak dapat dibatalkan!')) {
        document.getElementById('delete-form').submit();
    }
}

// Auto-suggest status based on kondisi
document.getElementById('kondisi').addEventListener('change', function() {
    const statusSelect = document.getElementById('status');
    
    if (this.value === 'Rusak Berat' && statusSelect.value === 'tersedia') {
        if (confirm('Kondisi "Rusak Berat" sebaiknya menggunakan status "Rusak" atau "Maintenance".\n\nUbah status menjadi "Rusak"?')) {
            statusSelect.value = 'rusak';
        }
    }
});
</script>

<style>
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

.img-thumbnail {
    border-radius: 8px;
}

.alert-info {
    background-color: #e7f3ff;
    border-color: #b3d9ff;
}

.badge {
    font-weight: 500;
}
</style>