{{-- resources/views/maintenance/partials/_item_row.blade.php --}}

@php
    // Convert array to object if needed
    if (is_array($item)) {
        $item = (object) $item;
    }
@endphp

<div class="maintenance-item-row border rounded p-3 mb-3 position-relative" data-index="{{ $index }}">
    @if($index > 0)
        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 remove-item-btn">
            <i class="bi bi-x"></i>
        </button>
    @endif
    
    {{-- Hidden ID for existing items (edit mode) --}}
    @if($item && isset($item->id))
        <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
    @endif
    
    <div class="row g-3">
        {{-- Nama Item --}}
        <div class="col-md-6">
            <label class="form-label">Nama Item Pekerjaan <span class="text-danger">*</span></label>
            <input type="text" 
                   name="items[{{ $index }}][nama_item]" 
                   class="form-control @error('items.'.$index.'.nama_item') is-invalid @enderror" 
                   placeholder="Contoh: Ganti RAM"
                   value="{{ old('items.'.$index.'.nama_item', $item->nama_item ?? '') }}"
                   required>
            @error('items.'.$index.'.nama_item')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        {{-- Kategori --}}
        <div class="col-md-6">
            <label class="form-label">Kategori <span class="text-danger">*</span></label>
            <select name="items[{{ $index }}][kategori]" 
                    class="form-select @error('items.'.$index.'.kategori') is-invalid @enderror" 
                    required>
                <option value="">-- Pilih Kategori --</option>
                <option value="persiapan" {{ old('items.'.$index.'.kategori', $item->kategori ?? '') == 'persiapan' ? 'selected' : '' }}>
                    Persiapan
                </option>
                <option value="perbaikan" {{ old('items.'.$index.'.kategori', $item->kategori ?? '') == 'perbaikan' ? 'selected' : '' }}>
                    Perbaikan
                </option>
                <option value="penggantian" {{ old('items.'.$index.'.kategori', $item->kategori ?? '') == 'penggantian' ? 'selected' : '' }}>
                    Penggantian
                </option>
                <option value="pembersihan" {{ old('items.'.$index.'.kategori', $item->kategori ?? '') == 'pembersihan' ? 'selected' : '' }}>
                    Pembersihan
                </option>
                <option value="upgrade" {{ old('items.'.$index.'.kategori', $item->kategori ?? '') == 'upgrade' ? 'selected' : '' }}>
                    Upgrade
                </option>
                <option value="testing" {{ old('items.'.$index.'.kategori', $item->kategori ?? '') == 'testing' ? 'selected' : '' }}>
                    Testing
                </option>
            </select>
            @error('items.'.$index.'.kategori')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        {{-- Deskripsi --}}
        <div class="col-12">
            <label class="form-label">Deskripsi Pekerjaan</label>
            <textarea name="items[{{ $index }}][deskripsi]" 
                      rows="2" 
                      class="form-control @error('items.'.$index.'.deskripsi') is-invalid @enderror" 
                      placeholder="Jelaskan detail pekerjaan dan spare parts yang digunakan...">{{ old('items.'.$index.'.deskripsi', $item->deskripsi ?? '') }}</textarea>
            @error('items.'.$index.'.deskripsi')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        {{-- Biaya Material --}}
        <div class="col-md-4">
            <label class="form-label">Biaya Material (Rp) <span class="text-danger">*</span></label>
            <input type="number" 
                   name="items[{{ $index }}][biaya_material]" 
                   class="form-control biaya-input @error('items.'.$index.'.biaya_material') is-invalid @enderror" 
                   min="0" 
                   step="1000" 
                   value="{{ old('items.'.$index.'.biaya_material', $item->biaya_material ?? 0) }}"
                   required>
            @error('items.'.$index.'.biaya_material')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        {{-- Biaya Jasa --}}
        <div class="col-md-4">
            <label class="form-label">Biaya Jasa (Rp) <span class="text-danger">*</span></label>
            <input type="number" 
                   name="items[{{ $index }}][biaya_jasa]" 
                   class="form-control biaya-input @error('items.'.$index.'.biaya_jasa') is-invalid @enderror" 
                   min="0" 
                   step="1000" 
                   value="{{ old('items.'.$index.'.biaya_jasa', $item->biaya_jasa ?? 0) }}"
                   required>
            @error('items.'.$index.'.biaya_jasa')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        {{-- Subtotal (Display) --}}
        <div class="col-md-4">
            <label class="form-label">Subtotal (Rp)</label>
            <input type="text" 
                   class="form-control subtotal-display bg-light" 
                   readonly 
                   value="Rp {{ number_format(($item->biaya_material ?? 0) + ($item->biaya_jasa ?? 0), 0, ',', '.') }}">
        </div>

        {{-- Status Completed (untuk edit mode ONLY) --}}
        @if($item && is_object($item) && property_exists($item, 'id') && $item->id)
            <div class="col-12">
                <div class="form-check">
                    <input type="hidden" name="items[{{ $index }}][is_completed]" value="0">
                    <input type="checkbox" 
                           name="items[{{ $index }}][is_completed]" 
                           class="form-check-input" 
                           id="completed_{{ $index }}"
                           value="1"
                           {{ old('items.'.$index.'.is_completed', $item->is_completed ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="completed_{{ $index }}">
                        <i class="bi bi-check-circle me-1"></i> Pekerjaan ini sudah selesai
                    </label>
                </div>
            </div>
        @endif
    </div>
</div>