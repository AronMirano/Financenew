{{-- Session-flash toast — the Blade equivalent of the prototype's ToastProvider. --}}
@if (session('status'))
    <div class="fixed bottom-6 right-6 z-[60] flex flex-col gap-2 items-end">
        <div data-toast role="status"
             class="animate-toast bg-surface border border-line rounded-lg shadow-lg px-4 py-3 flex items-center gap-2.5 min-w-[280px]">
            <span class="size-6 rounded-full flex items-center justify-center shrink-0 bg-brand-50 text-brand-600">
                <x-icon name="check" class="size-3.5" />
            </span>
            <span class="text-sm text-ink">{{ session('status') }}</span>
        </div>
    </div>
@endif
