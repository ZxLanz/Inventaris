<div class="card-body">
    @php
    $kondisis = [
        ['judul' => 'Baik', 'jumlah' => $kondisiBaik, 'kondisi' => $kondisiBaik,
         'color' => 'success'],
        [
            'judul' => 'Rusak Ringan',
            'jumlah' => $kondisiRusakRingan, 
            'kondisi' => $kondisiRusakRingan,
            'color' => 'warning',
        ],
        ['judul' => 'Rusak Berat', 'jumlah' => $kondisiRusakBerat, 'kondisi' => $kondisiRusakBerat,
         'color' => 'danger'],
    ];
    @endphp

    @foreach ($kondisis as $kondisi)
        @php
        extract($kondisi);
        @endphp
        
        <x-progress-kondisi :judul="$judul" :jumlah="$jumlah" :kondisi="$kondisi" :color="$color" />
    @endforeach
</div>