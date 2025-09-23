<x-table-list>
    <x-slot name="header">
        <tr>
            <th>#</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Role</th>
            <th>Aksi</th>
        </tr>
    </x-slot>

    @forelse ($users as $index => $user)
        @php
        $role = $user->getRoleNames();
        @endphp
        <tr>
            <td>{{ $users->firstItem() + $index }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>
                <span class="badge bg-primary">
                    {{ ucwords($role->first()) }}
                </span>
            </td>
            <td>
                <div class="btn-group btn-group-sm" role="group">
                    <!-- Tombol Edit -->
                    <a href="{{ route('user.edit', $user->id) }}" 
                       class="btn btn-warning btn-sm" 
                       title="Edit">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    
                    <!-- Tombol Hapus -->
                    <button type="button" 
                            class="btn btn-danger btn-sm" 
                            title="Hapus"
                            data-bs-toggle="modal" 
                            data-bs-target="#deleteModal"
                            data-url="{{ route('user.destroy', $user->id) }}">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="text-center">
                <div class="alert alert-danger">
                    Data user belum tersedia.
                </div>
            </td>
        </tr>
    @endforelse
</x-table-list>