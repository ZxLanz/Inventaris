@props(['text', 'total', 'route', 'icon', 'color'])

<div class="col-xl-3 col-md-6 mb-4">
    <div class="card shadow py-2 border-{{ $color }}">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <div class="fw-bold text-{{ $color }} mb-1">
                        {{ $text }}
                    </div>
                    <div class="h5">{{ $total }}</div>
                </div>
                <div class="col-auto">
                    <i class="bi {{ $icon }} text-{{ $color }}" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
        
        <div class="card-footer">
            <a href="{{ route($route) }}" class="text-secondary text-decoration-none">
                Lihat Selengkapnya
                <i class="bi bi-box-arrow-up-right"></i>
            </a>
        </div>
    </div>
</div>