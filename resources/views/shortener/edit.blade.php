<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('shortener.edit_title') }}</title>
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-gray-50">
        <!-- Header -->
        <div class="bg-white shadow-sm">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ __('shortener.edit_link') }}</h1>
                </div>
                <div class="flex flex-wrap items-center gap-4">
                    @include('language-selector')
                    <a href="{{ route('shortener.dashboard') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold">
                        {{ __('shortener.back') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white rounded-lg shadow-sm p-8 max-w-2xl">
                <!-- Error Messages -->
                <div id="errorContainer" class="hidden mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-red-700" id="errorMessage"></p>
                </div>

                <!-- Success Messages -->
                <div id="successContainer" class="hidden mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-green-700" id="successMessage"></p>
                </div>

                <form id="editForm" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <!-- Hash (Read-only) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hash</label>
                        <div class="p-3 bg-gray-50 border border-gray-300 rounded-lg">
                            <code class="font-mono text-lg text-indigo-600 font-semibold">{{ $shortener->Hash ?? 'N/A' }}</code>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            Short-Link: <a href="{{ url($shortener->Hash ?? '') }}" target="_blank" class="text-blue-600 hover:underline">
                                {{ url($shortener->Hash ?? '') }}
                            </a>
                        </p>
                    </div>

                    <!-- URL (Read-only) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('shortener.target_url') }}</label>
                        <div class="p-3 bg-gray-50 border border-gray-300 rounded-lg break-all text-sm text-gray-700">
                            {{ $shortener->Url ?? 'N/A' }}
                        </div>
                    </div>

                    <!-- Description (Editable) -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('shortener.description') }}
                        </label>
                        <textarea
                            id="description"
                            name="description"
                            rows="6"
                            maxlength="1024"
                            placeholder="{{ __('shortener.describe_link') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none resize-none"
                        >{{ $shortener->Description ?? '' }}</textarea>
                        <p class="text-xs text-gray-500 mt-2">
                            <span id="charCount">{{ strlen($shortener->Description ?? '') }}</span> / 1024
                        </p>
                    </div>

                    <!-- Statistics -->
                    <div class="grid grid-cols-2 gap-4 p-4 bg-indigo-50 rounded-lg">
                        <div>
                            <p class="text-xs text-gray-600">{{ __('shortener.views') }}</p>
                            <p class="text-2xl font-bold text-indigo-600">{{ $shortener->Frequency ?? 0 }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600">{{ __('shortener.created_at') }}</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $shortener->CreationDate?->format('d.m.Y H:i') ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex gap-4 pt-6">
                        <button
                            type="submit"
                            id="saveBtn"
                            class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ __('shortener.save_icon') }}
                        </button>
                        <a href="{{ route('shortener.dashboard') }}" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-semibold text-center">
                            {{ __('shortener.cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>

        @php($translations = __('shortener'))
        <script>
            const translations = {{ Illuminate\Support\Js::from($translations) }};
            // Character counter
            const descriptionInput = document.getElementById('description');
            descriptionInput.addEventListener('input', function () {
                document.getElementById('charCount').textContent = this.value.length;
            });

            // Form submission
            document.getElementById('editForm').addEventListener('submit', async function (e) {
                e.preventDefault();

                const saveBtn = document.getElementById('saveBtn');
                saveBtn.disabled = true;
                saveBtn.textContent = translations.saving_long;

                const errorContainer = document.getElementById('errorContainer');
                const successContainer = document.getElementById('successContainer');
                errorContainer.classList.add('hidden');
                successContainer.classList.add('hidden');

                try {
                    const response = await fetch(window.location.pathname, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        },
                        body: JSON.stringify({
                            description: document.getElementById('description').value.trim(),
                        }),
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || translations.error);
                    }

                    document.getElementById('successMessage').textContent = data.message;
                    successContainer.classList.remove('hidden');

                    // Redirect after success
                    setTimeout(() => {
                        window.location.href = '{{ route('shortener.dashboard') }}';
                    }, 1500);
                } catch (error) {
                    document.getElementById('errorMessage').textContent = error.message || translations.unknown_error;
                    errorContainer.classList.remove('hidden');
                } finally {
                    saveBtn.disabled = false;
                    saveBtn.textContent = translations.save_icon;
                }
            });
        </script>
    </body>
</html>
