@props(['label', 'hint' => null, 'required' => false, 'for' => null, 'error' => null])

<label @if ($for) for="{{ $for }}" @endif class="block">
    <span class="block text-[13px] font-medium text-ink-soft mb-1.5">
        {{ $label }}
        @if ($required)<span class="text-danger"> *</span>@endif
    </span>
    {{ $slot }}
    @if ($error)
        <span class="block text-[12px] text-danger mt-1">{{ $error }}</span>
    @elseif ($hint)
        <span class="block text-[12px] text-ink-mute mt-1">{{ $hint }}</span>
    @endif
</label>
