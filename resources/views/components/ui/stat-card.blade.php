@props([
    'value',
    'label',
    'trend' => null,
    'trendUp' => true,
    'icon' => null,
])

<div class="stat-card">
    <div class="flex items-start justify-between">
        <div>
            <p class="stat-value">{{ $value }}</p>
            <p class="stat-label">{{ $label }}</p>
            @if($trend)
                <p class="stat-trend {{ $trendUp ? 'stat-trend-up' : 'stat-trend-down' }}">
                    @if($trendUp)
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                        </svg>
                    @else
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                    @endif
                    {{ $trend }}
                </p>
            @endif
        </div>
        @if($icon)
            <div class="w-12 h-12 bg-primary-50 rounded-xl flex items-center justify-center text-primary-600">
                {!! $icon !!}
            </div>
        @endif
    </div>
</div>
