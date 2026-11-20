@extends('frame')

@section('main')
<!-- Header -->
<div class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">📊 Dashboard</h1>
            <p class="text-sm text-gray-600 mt-1">Verwalte deine Shortlinks</p>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Create New Shortener Button -->
    <div class="mb-8">
        <a href="{{ route('shortener.create-form') }}" class="inline-block px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold">
            + Neuen Short-Link erstellen
        </a>
    </div>

    <!-- Error Messages -->
    <div id="errorContainer" class="hidden mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
        <p class="text-red-700" id="errorMessage"></p>
    </div>

    <!-- Success Messages -->
    <div id="successContainer" class="hidden mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
        <p class="text-green-700" id="successMessage"></p>
    </div>

    <!-- Search & Filter -->
    <div class="mb-6">
        <input
            type="text"
            id="searchInput"
            placeholder="🔍 Suche nach Hash, URL oder Beschreibung..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none"
        >
    </div>

    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
        <p class="text-gray-600 mt-4">Lädt Shortcuts...</p>
    </div>

    <!-- Shortcuts Table -->
    <div id="tableContainer" class="hidden overflow-x-auto bg-white shadow-sm">
        <table class="w-full">
            <thead class="bg-gray-100 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Hash</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">URL</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Beschreibung</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Häufigkeit</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Erstellt</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Aktionen</th>
                </tr>
            </thead>
            <tbody id="tableBody" class="divide-y divide-gray-200">
                <!-- Will be populated by JavaScript -->
            </tbody>
        </table>
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="text-center py-12 bg-white shadow-sm">
        <p class="text-gray-600 mb-4">📭 Keine Shortcuts vorhanden</p>
        <a href="{{ route('shortener.create-form') }}" class="inline-block px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold">
            Jetzt erstellen
        </a>
    </div>

    <!-- Pagination -->
    <div id="paginationContainer" class="hidden mt-6 flex justify-center gap-2">
        <!-- Will be populated by JavaScript -->
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full max-h-full overflow-y-auto p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Shortener bearbeiten</h2>

        <div id="editErrorContainer" class="hidden mb-4 p-4 bg-red-50 border border-red-200 rounded-lg" role="alert">
            <p class="text-red-700" id="editErrorMessage"></p>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Hash</label>
            <input type="text" id="editHash" readonly class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50" />
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">URL</label>
            <input type="text" id="editUrl" readonly class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm break-all" />
        </div>

        <div class="mb-4">
            <label for="editDescription" class="block text-sm font-medium text-gray-700 mb-2">Beschreibung</label>
            <textarea
                id="editDescription"
                rows="4"
                maxlength="1024"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none resize-none"
            ></textarea>
            <p class="text-xs text-gray-500 mt-1">
                <span id="editCharCount">0</span> / 1024
            </p>
        </div>

        <div class="mb-4">
            <label for="editPublishDate" class="block text-sm font-medium text-gray-700 mb-2">
                Veröffentlichungsdatum <span class="text-gray-500 text-xs">(optional)</span>
            </label>
            <input type="date" id="editPublishDate" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
            <p class="text-xs text-gray-500 mt-1">Link wird ab diesem Datum um 00:00 Uhr aktiv</p>
        </div>

        <div class="mb-6">
            <label for="editExpirationDate" class="block text-sm font-medium text-gray-700 mb-2">
                Ablaufdatum <span class="text-gray-500 text-xs">(optional)</span>
            </label>
            <input type="date" id="editExpirationDate" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
            <p class="text-xs text-gray-500 mt-1">Link läuft an diesem Datum um 00:00 Uhr ab</p>
        </div>

        <div class="flex gap-3">
            <button onclick="closeEditModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-semibold">
                Abbrechen
            </button>
            <button onclick="saveChanges()" id="saveBtn" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold disabled:opacity-50">
                Speichern
            </button>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">🗑️ Shortener löschen?</h2>
        <p class="text-gray-700 mb-6">
            Dieser Shortener wird gelöscht und kann später nicht wiederhergestellt werden.
        </p>

        <div class="flex gap-3">
            <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-semibold">
                Abbrechen
            </button>
            <button onclick="confirmDelete()" id="deleteBtn" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold disabled:opacity-50">
                Löschen
            </button>
        </div>
    </div>
</div>

<script>
    let currentPage = 1;
    let currentSearch = '';
    let editingId = null;
    let deletingId = null;
    let originalPublishDate = '';
    let originalExpirationDate = '';

    function formatEditDate(value) {
        if (!value) return '';

        const parts = new Intl.DateTimeFormat('en-CA', {
            timeZone: @json(config('app.timezone')),
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
        }).formatToParts(new Date(value));
        const dateParts = Object.fromEntries(parts.map(part => [part.type, part.value]));

        return `${dateParts.year}-${dateParts.month}-${dateParts.day}`;
    }

    // Character counter for edit description
    const editDescription = document.getElementById('editDescription');
    if (editDescription) {
        editDescription.addEventListener('input', function () {
            document.getElementById('editCharCount').textContent = this.value.length;
        });
    }

    // Load shorteners on page load
    document.addEventListener('DOMContentLoaded', function () {
        loadShorteners();
    });

    // Search with debounce
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', function () {
        clearTimeout(searchTimeout);
        currentSearch = this.value;
        currentPage = 1;
        searchTimeout = setTimeout(() => {
            loadShorteners();
        }, 300);
    });

    async function loadShorteners() {
        const loadingSpinner = document.getElementById('loadingSpinner');
        const tableContainer = document.getElementById('tableContainer');
        const emptyState = document.getElementById('emptyState');

        loadingSpinner.classList.remove('hidden');
        tableContainer.classList.add('hidden');
        emptyState.classList.add('hidden');

        try {
            const params = new URLSearchParams({
                page: currentPage,
            });

            if (currentSearch) {
                params.append('search', currentSearch);
            }

            const response = await fetch(`/api/shorteners?${params}`, {
                headers: {
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error('Failed to load shorteners');
            }

            const data = await response.json();

            if (data.data.length === 0) {
                emptyState.classList.remove('hidden');
            } else {
                renderTable(data.data);
                tableContainer.classList.remove('hidden');
                renderPagination(data.pagination);
            }
        } catch (error) {
            showError('Fehler beim Laden der Shortcuts: ' + error.message);
        } finally {
            loadingSpinner.classList.add('hidden');
        }
    }

    function renderTable(shorteners) {
        const tableBody = document.getElementById('tableBody');
        tableBody.innerHTML = shorteners.map(shortener => `
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 text-sm font-mono font-semibold text-indigo-600">
                    <a href="/${shortener.Hash}" target="_blank" class="hover:underline">
                        ${shortener.Hash}
                    </a>
                </td>
                <td class="px-6 py-4 text-sm text-gray-700 max-w-xs truncate">
                    <a href="${shortener.Url}" target="_blank" class="text-blue-600 hover:underline" title="${shortener.Url}">
                        ${shortener.Url}
                    </a>
                </td>
                <td class="px-6 py-4 text-sm text-gray-700 max-w-xs truncate">
                    ${shortener.Description || '-'}
                </td>
                <td class="px-6 py-4 text-sm text-gray-700 font-semibold">
                    📊 ${shortener.Frequency}
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    ${new Date(shortener.CreationDate).toLocaleDateString('de-DE')}
                </td>
                <td class="px-6 py-4 text-right text-sm space-x-2">
                    <button onclick="openEditModal(${shortener.ID}, '${shortener.Hash}', '${shortener.Url.replace(/'/g, "\\'")}', '${(shortener.Description || '').replace(/'/g, "\\'")}', '${formatEditDate(shortener.PublishDate)}', '${formatEditDate(shortener.ExpirationDate)}')"
                        class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-xs font-semibold">
                        ✏️ Bearbeiten
                    </button>
                    <button onclick="openDeleteModal(${shortener.ID}, '${shortener.Hash}')"
                        class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition text-xs font-semibold">
                        🗑️ Löschen
                    </button>
                </td>
            </tr>
        `).join('');
    }

    function renderPagination(pagination) {
        const container = document.getElementById('paginationContainer');

        if (pagination.last_page === 1) {
            container.classList.add('hidden');
            return;
        }

        let html = '';

        if (pagination.current_page > 1) {
            html += `<button onclick="goToPage(1)" class="px-3 py-2 border border-gray-300 rounded hover:bg-gray-100">« Erste</button>`;
            html += `<button onclick="goToPage(${pagination.current_page - 1})" class="px-3 py-2 border border-gray-300 rounded hover:bg-gray-100">‹ Zurück</button>`;
        }

        html += `<span class="px-3 py-2 text-gray-700">Seite ${pagination.current_page} von ${pagination.last_page}</span>`;

        if (pagination.current_page < pagination.last_page) {
            html += `<button onclick="goToPage(${pagination.current_page + 1})" class="px-3 py-2 border border-gray-300 rounded hover:bg-gray-100">Weiter ›</button>`;
            html += `<button onclick="goToPage(${pagination.last_page})" class="px-3 py-2 border border-gray-300 rounded hover:bg-gray-100">Letzte »</button>`;
        }

        container.innerHTML = html;
        container.classList.remove('hidden');
    }

    function goToPage(page) {
        currentPage = page;
        loadShorteners();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function openEditModal(id, hash, url, description, publishDate, expirationDate) {
        editingId = id;
        originalPublishDate = publishDate;
        originalExpirationDate = expirationDate;
        document.getElementById('editHash').value = hash;
        document.getElementById('editUrl').value = url;
        document.getElementById('editDescription').value = description;
        document.getElementById('editPublishDate').value = publishDate;
        document.getElementById('editExpirationDate').value = expirationDate;
        document.getElementById('editCharCount').textContent = description.length;
        document.getElementById('editErrorContainer').classList.add('hidden');
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        editingId = null;
    }

    async function saveChanges() {
        const saveBtn = document.getElementById('saveBtn');
        saveBtn.disabled = true;

        const description = document.getElementById('editDescription').value;
        const publishDate = document.getElementById('editPublishDate').value;
        const expirationDate = document.getElementById('editExpirationDate').value;
        const errorContainer = document.getElementById('editErrorContainer');
        errorContainer.classList.add('hidden');

        try {
            const publishDateChanged = publishDate !== originalPublishDate;
            const expirationDateChanged = expirationDate !== originalExpirationDate;

            if ((publishDateChanged || expirationDateChanged) && publishDate && expirationDate && expirationDate <= publishDate) {
                throw new Error('Das Ablaufdatum muss nach dem Veröffentlichungsdatum liegen.');
            }

            const formData = { description };
            if (publishDateChanged) {
                formData.publish_date = publishDate ? `${publishDate} 00:00` : null;
            }
            if (expirationDateChanged) {
                formData.expiration_date = expirationDate ? `${expirationDate} 00:00` : null;
            }

            const response = await fetch(`/api/shorteners/${editingId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify(formData),
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Fehler beim Speichern');
            }

            showSuccess(data.message);
            closeEditModal();
            loadShorteners();
        } catch (error) {
            document.getElementById('editErrorMessage').textContent = 'Fehler: ' + error.message;
            errorContainer.classList.remove('hidden');
            errorContainer.scrollIntoView({ block: 'nearest' });
        } finally {
            saveBtn.disabled = false;
        }
    }

    function openDeleteModal(id, hash) {
        deletingId = id;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        deletingId = null;
    }

    async function confirmDelete() {
        const deleteBtn = document.getElementById('deleteBtn');
        deleteBtn.disabled = true;

        try {
            const response = await fetch(`/api/shorteners/${deletingId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Fehler beim Löschen');
            }

            showSuccess(data.message);
            closeDeleteModal();
            loadShorteners();
        } catch (error) {
            showError('Fehler: ' + error.message);
        } finally {
            deleteBtn.disabled = false;
        }
    }

    function showError(message) {
        const errorContainer = document.getElementById('errorContainer');
        const errorMessage = document.getElementById('errorMessage');
        errorMessage.textContent = message;
        errorContainer.classList.remove('hidden');
        setTimeout(() => {
            errorContainer.classList.add('hidden');
        }, 5000);
    }

    function showSuccess(message) {
        const successContainer = document.getElementById('successContainer');
        const successMessage = document.getElementById('successMessage');
        successMessage.textContent = message;
        successContainer.classList.remove('hidden');
        setTimeout(() => {
            successContainer.classList.add('hidden');
        }, 5000);
    }
</script>
@endsection