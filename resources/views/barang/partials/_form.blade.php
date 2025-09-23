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