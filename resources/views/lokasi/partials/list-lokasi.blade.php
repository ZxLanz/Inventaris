<x-table-list>
    <x-slot name="header">
        <tr>
            <th>#</th>
            <th>Nama Lokasi</th>
            @can('manage lokasi')
                <th>Aksi</th>
            @endcan
        </tr>
    </x-slot>

    @forelse ($lokasis as $index => $lokasi)
        <tr>
            <td>{{ $lokasis->firstItem() + $index }}</td>
            <td>{{ $lokasi->nama_lokasi }}</td>
            @can('manage lokasi')
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                        <!-- Edit -->
                        <x-tombol-aksi :href="route('lokasi.edit', $lokasi->id)" type="edit" />

                        <!-- Delete pakai modal -->
                        <button 
                            type="button"
                            class="btn btn-danger btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#deleteModal"
                            data-url="{{ route('lokasi.destroy', $lokasi->id) }}"
                        >
                            Hapus
                        </button>
                    </div>
                </td>
            @endcan
        </tr>
    @empty
        <tr>
            @can('manage lokasi')
                <td colspan="3" class="text-center">
            @else
                <td colspan="2" class="text-center">
            @endcan
                <div class="alert alert-warning">
                    Data lokasi belum tersedia.
                </div>
            </td>
        </tr>
    @endforelse
</x-table-list>

<!-- Modal Konfirmasi Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="deleteForm" method="POST">
        @csrf
        @method('DELETE')
        <div class="modal-header">
          <h5 class="modal-title">Konfirmasi Hapus</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          Yakin ingin menghapus data ini?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Hapus</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Script isi action ke form -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    document.getElementById('deleteModal')
        .addEventListener('show.bs.modal', event => {
            document.getElementById('deleteForm')
                .setAttribute('action', event.relatedTarget.getAttribute('data-url'));
        });
});
</script>