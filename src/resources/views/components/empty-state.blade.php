@props([
    'icon' => 'inbox',
    'title' => 'No data available',
    'message' => 'Import or add records to display them.',
    'action' => null,
    'compact' => false,
])

<div class="flex flex-col items-center justify-center text-center {{ $compact ? 'py-12 px-6' : 'py-20 px-6' }}">
    <div class="size-11 rounded-xl bg-surface border border-brand-600 text-brand-700 flex items-center justify-center mb-4">
        <x-icon :name="$icon" class="size-5" />
    </div>
    <div class="text-[15px] font-semibold text-ink">{{ $title }}</div>
    <p class="text-[13px] text-ink-mute mt-1 max-w-sm">{{ $message }}</p>
    @if ($action)
        <div class="mt-5">{{ $action }}</div>
    @endif
</div>
