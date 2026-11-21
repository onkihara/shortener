<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShortenerRequest;
use App\Http\Requests\UpdateShortenerRequest;
use App\Models\Shortener;
use App\Services\QrCodeGenerator;
use App\Services\YouTubeConverter;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ShortenerController extends Controller
{
    /**
     * Redirect to the full URL by hash
     * Public endpoint - no authentication required
     */
    public function redirect(string $hash, YouTubeConverter $youtubeConverter): RedirectResponse
    {
        $shortener = Shortener::where('Hash', $hash)->firstOrFail();

        // Check if published
        if (! $shortener->isPublished()) {
            abort(409);
        }

        // Check if expired
        if ($shortener->isExpired()) {
            abort(410);
        }

        // Record access
        $shortener->recordAccess();

        $targetUrl = $shortener->Url;

        // Convert YouTube URLs to ad-free embed variant
        if ($youtubeConverter->isYouTubeUrl($targetUrl)) {
            $targetUrl = $youtubeConverter->convertToAdFreeEmbed($targetUrl);
        }

        return redirect($targetUrl, 301);
    }

    /**
     * Get list of shorteners for authenticated user
     * With pagination and filtering
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();

        $query = Shortener::byUser($user->id)
            ->active()
            ->orderBy('CreationDate', 'desc');

        // Optional search/filter by hash or URL
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('Hash', 'like', "%{$search}%")
                    ->orWhere('Url', 'like', "%{$search}%")
                    ->orWhere('Description', 'like', "%{$search}%");
            });
        }

        $shorteners = $query->paginate(config('shortener.pagination.per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $shorteners->items(),
            'pagination' => [
                'total' => $shorteners->total(),
                'per_page' => $shorteners->perPage(),
                'current_page' => $shorteners->currentPage(),
                'last_page' => $shorteners->lastPage(),
            ],
        ]);
    }

    /**
     * Create a new shortener
     * Can be used by authenticated or unauthenticated users
     */
    public function store(StoreShortenerRequest $request): JsonResponse
    {
        $shortener = new Shortener;

        // Assign user ID if authenticated
        if ($user = auth()->user()) {
            $shortener->UserID = $user->id;
        }

        try {
            // Generate or validate hash
            $hash = $shortener->generateHash($request->input('hash'));

            // Fill in the data
            $shortener->fill([
                'Hash' => $hash,
                'Url' => $request->input('url'),
                'Description' => $request->input('description', ''),
                'PublishDate' => $request->input('publish_date') ? Carbon::createFromFormat('Y-m-d H:i', $request->input('publish_date')) : null,
                'ExpirationDate' => $request->input('expiration_date') ? Carbon::createFromFormat('Y-m-d H:i:s', $request->input('expiration_date')) : null,
                'Frequency' => 0,
                'CreationDate' => now(),
                'UpdateDate' => now(),
            ]);

            $shortener->save();

            return response()->json([
                'success' => true,
                'message' => 'Shortener erfolgreich erstellt.',
                'data' => [
                    'id' => $shortener->ID,
                    'hash' => $shortener->Hash,
                    'url' => $shortener->Url,
                    'description' => $shortener->Description,
                    'publish_date' => $shortener->PublishDate?->format('Y-m-d H:i'),
                    'expiration_date' => $shortener->ExpirationDate?->format('Y-m-d H:i:s'),
                    'short_link' => url($shortener->Hash),
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Erstellen des Shorteners: '.$e->getMessage(),
            ], 422);
        }
    }

    /**
     * Show a single shortener
     */
    public function show(Shortener $shortener): JsonResponse
    {
        // Check authorization
        if ($shortener->UserID && $shortener->UserID !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $shortener->ID,
                'hash' => $shortener->Hash,
                'url' => $shortener->Url,
                'description' => $shortener->Description,
                'frequency' => $shortener->Frequency,
                'publish_date' => $shortener->PublishDate?->format('Y-m-d H:i'),
                'expiration_date' => $shortener->ExpirationDate?->format('Y-m-d H:i:s'),
                'created_at' => $shortener->CreationDate,
                'updated_at' => $shortener->UpdateDate,
                'short_link' => url($shortener->Hash),
            ],
        ]);
    }

    /**
     * Update a shortener (description, publish_date, and expiration_date can be changed)
     */
    public function update(UpdateShortenerRequest $request, Shortener $shortener): JsonResponse
    {
        // Check authorization
        if ($shortener->UserID !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        try {
            $updateData = [
                'Description' => $request->input('description', ''),
            ];

            if ($request->has('publish_date')) {
                $updateData['PublishDate'] = $request->input('publish_date') ? Carbon::createFromFormat('Y-m-d H:i', $request->input('publish_date')) : null;
            }

            if ($request->has('expiration_date')) {
                $updateData['ExpirationDate'] = $request->input('expiration_date') ? Carbon::createFromFormat('Y-m-d H:i:s', $request->input('expiration_date')) : null;
            }

            $shortener->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Shortener erfolgreich aktualisiert.',
                'data' => [
                    'id' => $shortener->ID,
                    'hash' => $shortener->Hash,
                    'url' => $shortener->Url,
                    'description' => $shortener->Description,
                    'frequency' => $shortener->Frequency,
                    'publish_date' => $shortener->PublishDate?->format('Y-m-d H:i'),
                    'expiration_date' => $shortener->ExpirationDate?->format('Y-m-d H:i:s'),
                    'created_at' => $shortener->CreationDate,
                    'updated_at' => $shortener->UpdateDate,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Aktualisieren: '.$e->getMessage(),
            ], 422);
        }
    }

    /**
     * Delete a shortener (soft delete by default)
     */
    public function destroy(Request $request, Shortener $shortener): JsonResponse
    {
        // Check authorization
        if ($shortener->UserID !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        try {
            // Check if hard delete is requested
            if ($request->boolean('hard')) {
                $shortener->forceDelete();
                $message = 'Shortener vollständig gelöscht.';
            } else {
                $shortener->delete();
                $message = 'Shortener gelöscht.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'hash' => $shortener->Hash,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Löschen: '.$e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get full URL from hash (API endpoint, same as show but by hash)
     */
    public function getByHash(Request $request): JsonResponse
    {
        $hash = $request->input('hash');

        if (! $hash) {
            return response()->json([
                'success' => false,
                'message' => 'Hash parameter is required.',
            ], 400);
        }

        $shortener = Shortener::where('Hash', $hash)->published()->first();

        if (! $shortener) {
            return response()->json([
                'success' => false,
                'message' => 'Shortener nicht gefunden.',
            ], 404);
        }

        // Check if expired
        if ($shortener->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'Shortener ist abgelaufen.',
            ], 410);
        }

        // Record access
        $shortener->recordAccess();

        return response()->json([
            'success' => true,
            'data' => [
                'hash' => $shortener->Hash,
                'url' => $shortener->Url,
                'description' => $shortener->Description,
            ],
        ]);
    }

    /**
     * Generate QR code for a shortener
     */
    public function generateQrCode(Request $request, QrCodeGenerator $qrCodeGenerator): JsonResponse
    {
        $request->validate([
            'text' => 'required|string|max:2953',
            'format' => 'in:png,jpg,jpeg',
            'foreground_color' => 'regex:/^#[0-9A-Fa-f]{6}$/',
            'background_color' => 'regex:/^#[0-9A-Fa-f]{6}$/',
            'size' => 'integer|min:100|max:1000',
        ]);

        try {
            $base64Image = $qrCodeGenerator->generate(
                text: $request->input('text'),
                format: $request->input('format', 'png'),
                foregroundColor: $request->input('foreground_color', '#000000'),
                backgroundColor: $request->input('background_color', '#FFFFFF'),
                size: $request->input('size', 300)
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'image' => 'data:image/'.($request->input('format') === 'jpg' ? 'jpeg' : 'png').';base64,'.$base64Image,
                    'format' => $request->input('format', 'png'),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Generieren des QR-Codes: '.$e->getMessage(),
            ], 422);
        }
    }
}
