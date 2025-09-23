<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">Tambah Barang</h2>
    </x-slot>

    <div class="py-5">
        <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <form action="{{ route('barang.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="card-body">
                        @include('barang.partials._form')
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>