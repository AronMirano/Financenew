@props(['title', 'subtitle' => null, 'action' => null])

<div class="flex items-start justify-between gap-4 px-5 py-4 border-b border-line">
    <div>
        <h3 class="text-[15px] font-semibold text-ink font-serif">{{ $title }}</h3>
        @if ($subtitle)
            <p class="text-[13px] text-ink-mute mt-0.5">{{ $subtitle }}</p>
        @endif
    </div>
    {{ $action }}
</div>
