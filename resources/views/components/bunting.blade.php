@props(['flip' => false, 'class' => ''])
<svg class="{{ $class }} block h-5 w-full {{ $flip ? 'rotate-180' : '' }}" preserveAspectRatio="none" viewBox="0 0 120 14" fill="none" aria-hidden="true">
    <defs>
        <pattern id="bunting-{{ $flip ? 'b' : 'a' }}" width="20" height="14" patternUnits="userSpaceOnUse">
            <line x1="0" y1="1" x2="20" y2="1" stroke="#E3C077" stroke-width="1"></line>
            <polygon points="2,1 10,1 6,13" fill="#C13515"></polygon>
            <polygon points="12,1 20,1 16,13" fill="#E8912B"></polygon>
        </pattern>
    </defs>
    <rect width="120" height="14" fill="url(#bunting-{{ $flip ? 'b' : 'a' }})"></rect>
</svg>
