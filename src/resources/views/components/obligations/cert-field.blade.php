{{-- Certification picker — selecting an officer stamps today's date on save. --}}
@props(['title', 'note', 'label', 'name', 'selected' => null])

@php
    use App\Support\Reference;
    $officer = Reference::lookupPersonnel($selected);
@endphp

<div class="border border-line rounded-lg p-4">
    <div class="font-semibold text-ink">{{ $title }}</div>
    <p class="text-[12px] text-ink-mute mt-0.5 mb-3">{{ $note }}</p>

    <x-field :label="$label">
        <x-select :name="$name">
            <option value="">Select personnel…</option>
            @foreach (Reference::PERSONNEL as $p)
                <option value="{{ $p['id'] }}" @selected($selected === $p['id'])>{{ $p['name'] }}</option>
            @endforeach
        </x-select>
    </x-field>

    @if ($officer)
        <div class="mt-2 text-[12px] text-ink-mute">
            <div>{{ $officer['position'] }}</div>
            <div>Date signed: <span class="text-ink-soft">stamped on save</span></div>
        </div>
    @endif
</div>
