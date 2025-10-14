<x-main-layout :title-page="__('Peminjaman Barang')">
    <div class="card">
        <div class="card-body">
            @include('peminjaman.partials.toolbar')
            <x-notif-alert class="mt-4" />
        </div>
        @include('peminjaman.partials.list-peminjaman')
        <div class="card-body">
            {{ $peminjaman->links() }}
        </div>
    </div>
</x-main-layout>
