<form action="{{ route('language.update') }}" method="POST" class="flex items-center gap-2">
    @csrf
    <label for="locale" class="text-sm font-semibold">{{ __('shortener.language') }}</label>
    <select id="locale" name="locale" onchange="this.form.requestSubmit()" class="rounded border border-slate-400 bg-white px-2 py-1 text-sm">
        @foreach (config('app.supported_locales') as $locale => $name)
            <option value="{{ $locale }}" @selected(app()->getLocale() === $locale)>{{ $name }}</option>
        @endforeach
    </select>
    <noscript><button type="submit">{{ __('shortener.change') }}</button></noscript>
</form>
