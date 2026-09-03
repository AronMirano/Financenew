{{-- Tabs — server-rendered as links so each tab is a real URL. --}}
@props(['tabs', 'active', 'param' => 'tab'])

<div class="flex items-center gap-1 border-b border-line overflow-x-auto">
    @foreach ($tabs as $tab)
        @php $isActive = $tab['id'] === $active; @endphp
        <a href="{{ request()->fullUrlWithQuery([$param => $tab['id']]) }}"
           @if ($isActive) aria-current="page" @endif
           class="px-3.5 h-9 inline-flex items-center text-sm font-medium whitespace-nowrap border-b-2 -mb-px transition-colors focus-ring rounded-t {{ $isActive ? 'border-brand-600 text-brand-700' : 'border-transparent text-ink-mute hover:text-ink' }}">
            {{ $tab['label'] }}
        </a>
    @endforeach
</div>
