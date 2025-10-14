<x-table-list>
    <x-slot name="header">
        <tr>
            <th>#</th>
            <th>Nama Kategori</th>
            @can('manage kategori')
                <th>Aksi</th>
            @endcan
        </tr>
    </x-slot>

    @forelse ($kategoris as $index => $kategori)
        <tr>
            <td>{{ $kategoris->firstItem() + $index }}</td>
            <td>{{ $kategori->nama_kategori }}</td>
            @can('manage kategori')
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                        <!-- Edit -->
                        <x-tombol-aksi :href="route('kategori.edit', $kategori->id)" type="edit" />

                        <!-- Delete pakai modal -->
                        <button 
                            type="button"
                            class="btn btn-danger btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#deleteModal"
                            data-url="{{ route('kategori.destroy', $kategori->id) }}"
                        >
                            Hapus
                        </button>
                    </div>
                </td>
            @endcan
        </tr>
    @empty
        <tr>
            <td colspan="3" class="text-center">
                <div class="alert alert-danger">
                    Data kategori belum tersedia.
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
