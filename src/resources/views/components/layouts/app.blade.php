@php
    use App\Support\Navigation;

    $route = request()->route()?->getName();
    $meta = Navigation::meta($route);
    $user = auth()->user();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $meta['title'] }} · MMSU FinSys</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-canvas">
    {{-- Sidebar --}}
    <aside data-sidebar
           class="fixed inset-y-0 left-0 z-40 w-64 bg-surface border-r border-line flex flex-col transition-transform lg:translate-x-0 -translate-x-full">
        <div class="h-16 flex items-center gap-2.5 px-5 border-b border-line shrink-0">
            <div class="size-9 rounded-full overflow-hidden shrink-0 border border-brand-600">
                <img src="{{ asset('images/mmsu-seal.png') }}" alt="Mariano Marcos State University seal"
                     class="size-full object-cover">
            </div>
            <div class="leading-tight">
                <div class="text-[14px] font-semibold text-ink font-display tracking-wide">MMSU FinSys</div>
                <div class="text-[11px] text-ink-mute">Budget &amp; Finance</div>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6">
            @foreach (Navigation::NAV as $group)
                <div>
                    <div class="px-3 mb-1.5 text-[11px] font-semibold uppercase tracking-wider text-ink-mute">
                        {{ $group['label'] }}
                    </div>
                    <div class="space-y-0.5">
                        @foreach ($group['items'] as $item)
                            @php $active = Navigation::isActive($item['route'], $route); @endphp
                            <a href="{{ route($item['route']) }}"
                               @if ($active) aria-current="page" @endif
                               class="w-full flex items-center gap-2.5 px-3 h-9 rounded-lg text-[13px] font-medium transition-colors focus-ring {{ $active ? 'bg-brand-600 text-white' : 'text-ink-soft hover:bg-warn-bg hover:text-ink' }}">
                                <x-icon :name="$item['icon']"
                                        class="size-4 shrink-0 {{ $active ? 'text-white' : 'text-ink-mute' }}" />
                                <span class="truncate">{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>

        <div class="px-3 py-3 border-t border-line">
            <div class="flex items-center gap-2.5 px-2 py-1.5">
                <div class="size-8 rounded-full bg-surface border border-brand-600 text-brand-700 flex items-center justify-center text-[12px] font-semibold shrink-0">
                    {{ $user?->initials() ?? '—' }}
                </div>
                <div class="leading-tight min-w-0 flex-1">
                    <div class="text-[13px] font-medium text-ink truncate">{{ $user?->name }}</div>
                    <div class="text-[11px] text-ink-mute truncate">{{ $user?->position ?? 'Budget & Finance' }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Sign out"
                            class="text-ink-mute hover:text-danger p-1.5 rounded-lg hover:bg-canvas focus-ring">
                        <x-icon name="log-out" class="size-4" />
                        <span class="sr-only">Sign out</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div data-sidebar-scrim hidden class="fixed inset-0 z-30 bg-ink/30 lg:hidden"></div>

    {{-- Main --}}
    <div class="lg:pl-64">
        <header class="sticky top-0 z-20 h-16 bg-surface/90 backdrop-blur border-b border-line flex items-center gap-3 px-4 lg:px-8">
            <button type="button" data-sidebar-open class="lg:hidden text-ink-soft focus-ring rounded p-1">
                <x-icon name="menu" class="size-5" />
                <span class="sr-only">Open navigation</span>
            </button>
            <div class="hidden sm:flex items-center gap-1.5 text-[13px]">
                <span class="text-ink-mute">{{ $meta['module'] }}</span>
                <span class="text-line">/</span>
                <span class="text-ink font-medium">{{ $meta['title'] }}</span>
            </div>
            <div class="flex-1"></div>
            <button type="button" class="relative text-ink-soft hover:text-ink p-2 rounded-lg hover:bg-canvas focus-ring">
                <x-icon name="bell" class="size-5" />
                <span class="absolute top-1.5 right-1.5 size-2 rounded-full bg-brand-500 ring-2 ring-surface"></span>
                <span class="sr-only">Notifications</span>
            </button>
            <div class="hidden sm:flex items-center gap-1.5 text-[12px] text-brand-700 bg-surface border border-brand-600 px-2.5 h-8 rounded-lg font-medium">
                <x-icon name="shield-check" class="size-3.5" />
                FY {{ now()->year }}
                <x-icon name="chevron-down" class="size-3.5" />
            </div>
        </header>

        <main class="p-4 lg:p-8 max-w-[1400px] mx-auto animate-fade">
            {{ $slot }}
        </main>
    </div>

    <x-toast />
</body>
</html>
