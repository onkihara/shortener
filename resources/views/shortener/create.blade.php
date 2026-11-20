@extends('frame')

@section('main')
<div class="max-w-xl w-full m-auto">
    <div class="bg-white shadow-xl p-8">

        <!-- Error Messages -->
        <div id="errorContainer" class="hidden mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-700" id="errorMessage"></p>
        </div>

        <!-- Success Messages -->
        <div id="successContainer" class="hidden mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-green-700 font-semibold mb-3" id="successMessage"></p>
            <div class="flex gap-2">
                <input type="text" id="shortLinkInput" class="flex-1 px-3 py-2 border border-green-300 rounded-lg bg-white text-sm font-mono" readonly>
                <button onclick="copyToClipboard()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-semibold">
                    Kopieren
                </button>
            </div>
            @auth
                <div class="mt-3">
                    <a href="{{ route('shortener.dashboard') }}" class="inline-block px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm font-semibold">
                        Zum Dashboard
                    </a>
                </div>
            @endauth
        </div>

        <!-- Form -->
        <form id="shortenerForm" class="space-y-4">
            @csrf

            <!-- URL Input -->
            <div>
                <label for="url" class="block text-sm font-medium text-gray-700 mb-2">
                    URL <span class="text-red-500">*</span>
                </label>
                <input
                    type="url"
                    id="url"
                    name="url"
                    placeholder="https://example.com/very/long/url"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition"
                    required
                >
                <p class="text-sm text-gray-500 mt-1" id="urlError"></p>
            </div>

            <!-- Hash Input -->
            @auth
            <div>
                <label for="hash" class="block text-sm font-medium text-gray-700 mb-2">
                    Benutzerdefinierter Hash <span class="text-gray-500 text-xs">(optional)</span>
                </label>
                <input
                    type="text"
                    id="hash"
                    name="hash"
                    placeholder="mein-link"
                    pattern="{{ config('shortener.hash.allowed') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition"
                >
                <p class="text-xs text-gray-500 mt-1">
                    Nur Buchstaben, Zahlen, Bindestrich und Unterstrich (max. 16 Zeichen)
                </p>
            </div>
            @endauth

            <!-- Description Input -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Beschreibung <span class="text-gray-500 text-xs">(optional)</span>
                </label>
                <textarea
                    id="description"
                    name="description"
                    placeholder="Beschreibe diesen Link..."
                    rows="3"
                    maxlength="1024"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition resize-none"
                ></textarea>
                <p class="text-xs text-gray-500 mt-1">
                    <span id="charCount">0</span> / 1024
                </p>
            </div>

            <!-- Published Date Input -->
            <div>
                <label for="publish_date" class="block text-sm font-medium text-gray-700 mb-2">
                    Veröffentlichungsdatum <span class="text-gray-500 text-xs">(optional)</span>
                </label>
                <input
                    type="date"
                    id="publish_date"
                    name="publish_date"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition"
                >
                <p class="text-xs text-gray-500 mt-1">
                    Link wird ab diesem Datum aktiv
                </p>
            </div>

            <!-- Expiration Date Input -->
            <div>
                <label for="expiration_date" class="block text-sm font-medium text-gray-700 mb-2">
                    Ablaufdatum <span class="text-gray-500 text-xs">(optional)</span>
                </label>
                <input
                    type="date"
                    id="expiration_date"
                    name="expiration_date"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition"
                >
                <p class="text-xs text-gray-500 mt-1">
                    Link wird nach diesem Datum nicht mehr funktionieren
                </p>
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                id="submitBtn"
                class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold mt-6 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                🔗 Short-Link erstellen
            </button>
        </form>

        <!-- Login Link -->
        @guest
            <div class="mt-6 p-4 bg-indigo-50 rounded-lg text-center">
                <p class="text-sm text-gray-700 mb-2">
                    Melde dich an, um deine Links zu verwalten
                </p>
                <a href="{{ route('login') }}" class="inline-block px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm font-semibold">
                    Anmelden
                </a>
            </div>
        @endguest

        <!-- Dashboard Link -->
        @auth
            <div class="mt-6 p-4 bg-indigo-50 rounded-lg text-center">
                <a href="{{ route('shortener.dashboard') }}" class="inline-block px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm font-semibold">
                    Verwalte deine Shortlinks
                </a>
            </div>
        @endauth
    </div>
</div>

<script>
    // Character counter for description
    const descriptionInput = document.getElementById('description');
    descriptionInput.addEventListener('input', function () {
        document.getElementById('charCount').textContent = this.value.length;
    });

    // Form submission
    document.getElementById('shortenerForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Wird erstellt...';

        const errorContainer = document.getElementById('errorContainer');
        const successContainer = document.getElementById('successContainer');
        errorContainer.classList.add('hidden');
        successContainer.classList.add('hidden');

        const urlInput = document.getElementById('url');
        const hashInput = document.getElementById('hash'); // Can be null for guests
        const descInput = document.getElementById('description');
        const publishDateInput = document.getElementById('publish_date');
        const expirationDateInput = document.getElementById('expiration_date');

        const formData = {
            url: urlInput.value.trim(),
            hash: hashInput ? hashInput.value.trim() || null : null,
            description: descInput.value.trim(),
            publish_date: publishDateInput.value ? `${publishDateInput.value} 00:00` : null,
            expiration_date: expirationDateInput.value ? `${expirationDateInput.value} 00:00` : null,
        };

        try {
            const response = await fetch('/api/shorten', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                },
                body: JSON.stringify(formData),
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Ein Fehler ist aufgetreten.');
            }

            // Show success message
            document.getElementById('successMessage').textContent = data.message;
            document.getElementById('shortLinkInput').value = data.data.short_link;
            successContainer.classList.remove('hidden');

            // Reset form
            document.getElementById('shortenerForm').reset();
            document.getElementById('charCount').textContent = '0';
        } catch (error) {
            document.getElementById('errorMessage').textContent = error.message || 'Ein unbekannter Fehler ist aufgetreten.';
            errorContainer.classList.remove('hidden');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = '🔗 Short-Link erstellen';
        }
    });

    // Copy to clipboard
    function copyToClipboard() {
        const input = document.getElementById('shortLinkInput');
        if (!input) return;

        input.select();
        document.execCommand('copy');

        const btn = event.target;
        const originalText = btn.textContent;
        btn.textContent = '✅ Kopiert!';
        setTimeout(() => {
            btn.textContent = originalText;
        }, 2000);
    }
</script>
@endsection