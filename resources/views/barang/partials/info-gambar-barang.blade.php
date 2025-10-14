@if ($barang->gambar)
    <img src="{{ asset('gambar-barang/' . $barang->gambar) }}" 
         alt="{{ $barang->nama_barang }}" 
         class="img-fluid rounded border"
         style="max-height: 300px;">
@else
    <div class="d-flex align-items-center justify-content-center bg-light rounded border" 
         style="height: 300px;">
        <span class="text-muted">Tidak Ada Gambar</span>
    </div>
@endif
