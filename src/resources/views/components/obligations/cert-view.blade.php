{{-- Read-only certification block on the document detail page. --}}
@props(['title', 'officerId' => null, 'date' => null])

@php
    use App\Support\Reference;
    $officer = Reference::lookupPersonnel($officerId);
@endphp

<div class="border border-line rounded-lg p-4">
    <div class="font-semibold text-ink mb-2">{{ $title }}</div>
    @if ($officer)
        <div>
            <div class="border-t border-ink pt-1 inline-block">
                <div class="font-semibold text-ink uppercase text-[13px]">{{ $officer['name'] }}</div>
                <div class="text-[12px] text-ink-mute">{{ $officer['position'] }}</div>
            </div>
            <div class="text-[12px] text-ink-mute mt-1">Date signed: {{ $date }}</div>
        </div>
    @else
        <div class="text-ink-mute">Not yet certified</div>
    @endif
</div>
