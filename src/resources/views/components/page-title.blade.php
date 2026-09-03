@props(['title', 'description' => null, 'action' => null])

<div class="flex items-start justify-between gap-4 flex-wrap">
    <div>
        <h1 class="text-xl font-semibold text-ink font-serif">{{ $title }}</h1>
        @if ($description)
            <p class="text-sm text-ink-mute mt-1 max-w-2xl">{{ $description }}</p>
        @endif
    </div>
    {{ $action }}
</div>
