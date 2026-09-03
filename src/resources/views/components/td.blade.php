@props(['right' => false, 'mono' => false])

<td {{ $attributes->merge([
    'class' => 'px-3 py-2.5 border-b border-line-soft text-ink align-middle '
        . ($right ? 'text-right tnum ' : '')
        . ($mono ? 'font-mono text-[13px]' : ''),
]) }}>{{ $slot }}</td>
