<x-main-layout title-page="{{ __('User') }}">
    <div class="card">
        <div class="card-body">
            @include('user.partials.toolbar')
            
            <!-- Alert Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>

        <!-- Tabel tanpa padding dan border -->
        <div class="table-responsive">
            @include('user.partials.list-user')
        </div>
        
        <!-- Pagination tanpa border atas -->
        <div class="card-body">
            {{ $users->links() }}
        </div>
    </div>
</x-main-layout>