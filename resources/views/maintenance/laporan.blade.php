<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    @include('maintenance.partials._style-laporan')
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>{{ $title }}</h1>
        <div class="info">Tanggal Cetak: {{ $date }}</div>
        
        @if(!empty($filterInfo))
            <div class="filter-info">
                @foreach($filterInfo as $value)
                    {{ $value }}
                    @if(!$loop->last) | @endif
                @endforeach
            </div>
        @endif
    </div>

    {{-- Data Table --}}
    @include('maintenance.partials._list-laporan')

    {{-- Footer --}}
    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh Sistem Inventaris</p>
        <p>&copy; {{ date('Y') }} - Sistem Manajemen Inventaris</p>
    </div>
</body>
</html>