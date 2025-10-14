<div class="row">
    <div class="col-md-4">
        @can('manage peminjaman')
            <x-tombol-tambah label="Tambah Peminjaman" href="{{ route('peminjaman.create') }}" />
        @endcan
        
        @can('approve peminjaman')
            <a href="{{ route('peminjaman.laporan') }}" class="btn btn-secondary btn-sm" target="_blank">
                <i class="bi bi-file-pdf"></i> Cetak Laporan
            </a>
        @endcan
    </div>
    
    <div class="col-md-4">
        <!-- Filter Status -->
        <form method="GET" action="{{ route('peminjaman.index') }}">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">-- Semua Status --</option>
                <option value="Menunggu Approval" {{ request('status') == 'Menunggu Approval' ? 'selected' : '' }}>
                    Menunggu Approval
                </option>
                <option value="Disetujui" {{ request('status') == 'Disetujui' ? 'selected' : '' }}>
                    Disetujui
                </option>
                <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>
                    Ditolak
                </option>
                <option value="Dipinjam" {{ request('status') == 'Dipinjam' ? 'selected' : '' }}>
                    Dipinjam
                </option>
                <option value="Terlambat" {{ request('status') == 'Terlambat' ? 'selected' : '' }}>
                    Terlambat
                </option>
                <option value="Dikembalikan" {{ request('status') == 'Dikembalikan' ? 'selected' : '' }}>
                    Dikembalikan
                </option>
            </select>
        </form>
    </div>
    
    <div class="col-md-4">
        <x-form-search placeholder="Cari kode/nama peminjam..." />
    </div>
</div>
