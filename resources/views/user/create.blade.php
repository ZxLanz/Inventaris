<x-main-layout title-page="{{ __('Tambah User') }}">
    <div class="row">
        <div class="col-lg-6">
            <form class="card" action="{{ route('user.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    @include('user.partials._form')
                </div>
            </form>
        </div>
    </div>
</x-main-layout>