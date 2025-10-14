<x-main-layout title-page="{{ __('Edit User') }}">
    <div class="row">
        <div class="col-lg-6">
            <form class="card" action="{{ route('user.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    @include('user.partials._form', ['update' => true])
                </div>
            </form>
        </div>
    </div>
</x-main-layout>
