{{-- resources/views/maintenance/partials/_form.blade.php --}}

<div class="row g-3">
    {{-- Asset Selection --}}
    <div class="col-md-6">
        <label for="barang_asset_id" class="form-label">
            Pilih Asset <span class="text-danger">*</span>
        </label>
        <select name="barang_asset_id" id="barang_asset_id" 
                class="form-select @error('barang_asset_id') is-invalid @enderror"
                {{ isset($maintenance) ? 'disabled' : '' }}
                required>
            <option value="">-- Pilih Asset --</option>
            @foreach ($assets as $asset)
                <option value="{{ $asset->id }}" 
                        {{ (old('barang_asset_id', $maintenance->barang_asset_id ?? '') == $asset->id) ? 'selected' : '' }}>
                    {{ $asset->kode_asset }} - {{ $asset->barang->nama_barang }} 
                    ({{ $asset->lokasi->nama_lokasi }})
                </option>
            @endforeach
        </select>
        @error('barang_asset_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        @if(isset($maintenance))
            <input type="hidden" name="barang_asset_id" value="{{ $maintenance->barang_asset_id }}">
            <small class="text-muted">Asset tidak bisa diubah saat edit</small>
        @endif
    </div>

    {{-- Maintenance Type --}}
    <div class="col-md-6">
        <label for="maintenance_type" class="form-label">
            Tipe Maintenance <span class="text-danger">*</span>
        </label>
        <select name="maintenance_type" id="maintenance_type" 
                class="form-select @error('maintenance_type') is-invalid @enderror" 
                required>
            <option value="">-- Pilih Tipe --</option>
            <option value="preventive" 
                    {{ old('maintenance_type', $maintenance->maintenance_type ?? '') == 'preventive' ? 'selected' : '' }}>
                Preventive (Pemeliharaan Rutin)
            </option>
            <option value="corrective" 
                    {{ old('maintenance_type', $maintenance->maintenance_type ?? '') == 'corrective' ? 'selected' : '' }}>
                Corrective (Perbaikan)
            </option>
        </select>
        @error('maintenance_type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Teknisi (Internal) --}}
    <div class="col-md-6">
        <label for="teknisi_id" class="form-label">Teknisi Internal</label>
        <select name="teknisi_id" id="teknisi_id" 
                class="form-select @error('teknisi_id') is-invalid @enderror">
            <option value="">-- Pilih Teknisi (Optional) --</option>
            @foreach ($teknisis as $teknisi)
                <option value="{{ $teknisi->id }}" 
                        {{ old('teknisi_id', $maintenance->teknisi_id ?? '') == $teknisi->id ? 'selected' : '' }}>
                    {{ $teknisi->name }}
                </option>
            @endforeach
        </select>
        @error('teknisi_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="text-muted">Kosongkan jika menggunakan vendor external</small>
    </div>

    {{-- Vendor Name (External) --}}
    <div class="col-md-6">
        <label for="vendor_name" class="form-label">Nama Vendor External</label>
        <input type="text" 
               name="vendor_name" 
               id="vendor_name" 
               class="form-control @error('vendor_name') is-invalid @enderror"
               value="{{ old('vendor_name', $maintenance->vendor_name ?? '') }}"
               placeholder="Contoh: CV. Teknik Jaya">
        @error('vendor_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="text-muted">Kosongkan jika menggunakan teknisi internal</small>
    </div>

    {{-- Tanggal Mulai --}}
    <div class="col-md-6">
        <label for="tanggal_mulai" class="form-label">
            Tanggal Mulai <span class="text-danger">*</span>
        </label>
        <input type="date" 
               name="tanggal_mulai" 
               id="tanggal_mulai" 
               class="form-control @error('tanggal_mulai') is-invalid @enderror"
               value="{{ old('tanggal_mulai', isset($maintenance) ? $maintenance->tanggal_mulai->format('Y-m-d') : date('Y-m-d')) }}"
               required>
        @error('tanggal_mulai')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Foto Sebelum --}}
    <div class="col-md-6">
        <label for="foto_sebelum" class="form-label">Foto Sebelum Maintenance</label>
        <input type="file" 
               name="foto_sebelum" 
               id="foto_sebelum" 
               class="form-control @error('foto_sebelum') is-invalid @enderror"
               accept="image/*">
        @error('foto_sebelum')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        @if(isset($maintenance) && $maintenance->foto_sebelum)
            <small class="text-muted d-block mt-1">
                <a href="{{ asset('storage/' . $maintenance->foto_sebelum) }}" target="_blank">
                    Lihat foto saat ini
                </a>
            </small>
        @endif
    </div>

    {{-- Masalah Ditemukan --}}
    <div class="col-12">
        <label for="masalah_ditemukan" class="form-label">Masalah/Keluhan</label>
        <textarea name="masalah_ditemukan" 
                  id="masalah_ditemukan" 
                  rows="3" 
                  class="form-control @error('masalah_ditemukan') is-invalid @enderror"
                  placeholder="Jelaskan masalah atau keluhan yang ditemukan...">{{ old('masalah_ditemukan', $maintenance->masalah_ditemukan ?? '') }}</textarea>
        @error('masalah_ditemukan')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror>
    </div>
</div>

{{-- Maintenance Items Section --}}
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="bi bi-list-check me-2"></i>Detail Pekerjaan / Items
        </h5>
    </div>
    <div class="card-body">
        <div id="maintenance-items-container">
            @php
                $oldItems = old('items', isset($maintenance) ? $maintenance->items : []);
                $itemCount = is_array($oldItems) && count($oldItems) > 0 ? count($oldItems) : 1;
            @endphp

            @if(is_array($oldItems) && count($oldItems) > 0)
                @foreach($oldItems as $index => $item)
                    @include('maintenance.partials._item_row', [
                        'index' => $index, 
                        'item' => is_array($item) ? (object)$item : $item
                    ])
                @endforeach
            @else
                @include('maintenance.partials._item_row', ['index' => 0, 'item' => null])
            @endif
        </div>

        <button type="button" class="btn btn-sm btn-outline-primary mt-3" id="add-item-btn">
            <i class="bi bi-plus-circle me-1"></i> Tambah Item Pekerjaan
        </button>
    </div>
</div>

{{-- Action Buttons --}}
<div class="mt-4">
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-save me-1"></i>
        {{ isset($maintenance) ? 'Update' : 'Simpan' }}
    </button>
    <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">
        <i class="bi bi-x-circle me-1"></i> Batal
    </a>
</div>

{{-- JavaScript for Dynamic Items --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = {{ $itemCount }};
    
    // Add new item row
    document.getElementById('add-item-btn').addEventListener('click', function() {
        const container = document.getElementById('maintenance-items-container');
        const template = `
            <div class="maintenance-item-row border rounded p-3 mb-3 position-relative" data-index="${itemIndex}">
                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 remove-item-btn">
                    <i class="bi bi-x"></i>
                </button>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Item Pekerjaan <span class="text-danger">*</span></label>
                        <input type="text" name="items[${itemIndex}][nama_item]" class="form-control" 
                               placeholder="Contoh: Ganti RAM" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select name="items[${itemIndex}][kategori]" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="persiapan">Persiapan</option>
                            <option value="perbaikan">Perbaikan</option>
                            <option value="penggantian">Penggantian</option>
                            <option value="pembersihan">Pembersihan</option>
                            <option value="upgrade">Upgrade</option>
                            <option value="testing">Testing</option>
                        </select>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label">Deskripsi Pekerjaan</label>
                        <textarea name="items[${itemIndex}][deskripsi]" rows="2" class="form-control" 
                                  placeholder="Jelaskan detail pekerjaan..."></textarea>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Biaya Material (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="items[${itemIndex}][biaya_material]" 
                               class="form-control biaya-input" min="0" step="1000" value="0" required>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Biaya Jasa (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="items[${itemIndex}][biaya_jasa]" 
                               class="form-control biaya-input" min="0" step="1000" value="0" required>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Subtotal (Rp)</label>
                        <input type="text" class="form-control subtotal-display bg-light" readonly value="Rp 0">
                    </div>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', template);
        itemIndex++;
        
        // Attach event listeners to new row
        attachEventListeners();
    });
    
    // Remove item row
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item-btn')) {
            const row = e.target.closest('.maintenance-item-row');
            const container = document.getElementById('maintenance-items-container');
            
            // Prevent removing last item
            if (container.children.length > 1) {
                row.remove();
                calculateTotalBiaya();
            } else {
                alert('Minimal harus ada 1 item pekerjaan!');
            }
        }
    });
    
    // Calculate subtotal for each item
    function attachEventListeners() {
        document.querySelectorAll('.biaya-input').forEach(input => {
            input.addEventListener('input', function() {
                const row = this.closest('.maintenance-item-row');
                const biayaMaterial = parseFloat(row.querySelector('[name*="[biaya_material]"]').value) || 0;
                const biayaJasa = parseFloat(row.querySelector('[name*="[biaya_jasa]"]').value) || 0;
                const subtotal = biayaMaterial + biayaJasa;
                
                row.querySelector('.subtotal-display').value = 'Rp ' + subtotal.toLocaleString('id-ID');
                calculateTotalBiaya();
            });
        });
    }
    
    // Calculate total biaya
    function calculateTotalBiaya() {
        let totalMaterial = 0;
        let totalJasa = 0;
        
        document.querySelectorAll('.maintenance-item-row').forEach(row => {
            const biayaMaterial = parseFloat(row.querySelector('[name*="[biaya_material]"]').value) || 0;
            const biayaJasa = parseFloat(row.querySelector('[name*="[biaya_jasa]"]').value) || 0;
            
            totalMaterial += biayaMaterial;
            totalJasa += biayaJasa;
        });
        
        // You can display total here if needed
        console.log('Total Material:', totalMaterial);
        console.log('Total Jasa:', totalJasa);
        console.log('Grand Total:', totalMaterial + totalJasa);
    }
    
    // Initial attachment
    attachEventListeners();
});
</script>