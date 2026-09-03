<x-layouts.guest title="Sign in">
    <div class="min-h-screen bg-canvas flex items-start justify-center px-4 py-12">
        <div class="w-full max-w-md border border-line rounded-xl bg-surface px-8 py-10">
            {{-- Seal --}}
            <div class="flex justify-center">
                <div class="size-28 rounded-full overflow-hidden">
                    <img src="{{ asset('images/mmsu-seal.png') }}" alt="Mariano Marcos State University seal"
                         class="size-full object-cover">
                </div>
            </div>

            <div class="text-center mt-4 mb-8">
                <h1 class="text-lg font-semibold text-ink font-display tracking-wide">
                    Mariano Marcos State University
                </h1>
                <p class="text-[13px] text-ink-mute mt-0.5">Financial Management System</p>
            </div>

            <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
                @csrf

                @if ($errors->any())
                    <div class="border border-danger rounded-lg px-3 py-2 text-[13px] text-danger" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <input
                    name="email"
                    value="{{ old('email') }}"
                    autocomplete="username"
                    required
                    placeholder="Username"
                    aria-label="Username"
                    class="w-full h-11 px-4 rounded-lg border border-line bg-surface text-sm text-ink placeholder:text-ink-mute focus-ring focus:border-brand-600">
                <input
                    type="password"
                    name="password"
                    autocomplete="current-password"
                    required
                    placeholder="Password"
                    aria-label="Password"
                    class="w-full h-11 px-4 rounded-lg border border-line bg-surface text-sm text-ink placeholder:text-ink-mute focus-ring focus:border-brand-600">
                <x-button type="submit" class="h-11 px-6">Log in</x-button>
            </form>

            <a href="#" class="inline-block mt-4 text-[13px] text-brand-700 hover:underline cursor-pointer">
                Lost password?
            </a>

            <hr class="my-6 border-line">

            <div class="text-[13px] leading-relaxed">
                <h2 class="text-base font-semibold text-ink font-serif mb-2">Is this your first time here?</h2>
                <p class="text-ink-soft">
                    <span class="font-semibold text-ink">For budget &amp; finance staff:</span> use your MMSU
                    employee credentials to sign in to the Financial Management System.
                </p>
                <p class="text-ink-soft mt-2">
                    <span class="font-semibold text-ink">Need access?</span> Request an authentication credential
                    from ITC.
                </p>
            </div>

            <p class="text-[12px] text-ink-mute mt-8 text-center">
                © {{ now()->year }} MMSU · Budget and Finance Management Services Division
            </p>
        </div>
    </div>
</x-layouts.guest>
