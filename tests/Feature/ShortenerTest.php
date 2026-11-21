<?php

use App\Models\Shortener;
use App\Models\User;

describe('Shortener API', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
    });

    describe('POST /api/shorten - Create Shortener', function () {
        test('creates shortener with valid URL', function () {
            $response = $this->post('/api/shorten', [
                'url' => 'https://example.com/very/long/url',
            ]);

            $response->assertStatus(201);
            $response->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'hash', 'url', 'short_link'],
            ]);
            expect($response->json('success'))->toBeTrue();
            $this->assertDatabaseHas('shortcuts', ['Url' => 'https://example.com/very/long/url']);
        });

        test('creates shortener with custom hash', function () {
            $response = $this->post('/api/shorten', [
                'url' => 'https://example.com/long',
                'hash' => 'my-link',
            ]);

            $response->assertStatus(201);
            expect($response->json('data.hash'))->toBe('my-link');
            $this->assertDatabaseHas('shortcuts', ['Hash' => 'my-link']);
        });

        test('creates shortener with description', function () {
            $response = $this->post('/api/shorten', [
                'url' => 'https://example.com/long',
                'description' => 'My awesome link',
            ]);

            $response->assertStatus(201);
            $this->assertDatabaseHas('shortcuts', ['Description' => 'My awesome link']);
        });

        test('rejects duplicate custom hash', function () {
            Shortener::factory()->create(['Hash' => 'taken']);

            $response = $this->post('/api/shorten', [
                'url' => 'https://example.com/long',
                'hash' => 'taken',
            ]);

            $response->assertStatus(422);
            expect($response->json('success'))->toBeFalse();
        });

        test('rejects invalid URL', function () {
            $response = $this->post('/api/shorten', [
                'url' => 'not-a-url',
            ]);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors('url');
        });

        test('rejects missing URL', function () {
            $response = $this->post('/api/shorten', []);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors('url');
        });

        test('associates with authenticated user', function () {
            $response = $this->actingAs($this->user)->post('/api/shorten', [
                'url' => 'https://example.com/long',
            ]);

            $response->assertStatus(201);
            $this->assertDatabaseHas('shortcuts', ['UserID' => $this->user->id]);
        });

        test('allows unauthenticated users to create', function () {
            $response = $this->post('/api/shorten', [
                'url' => 'https://example.com/long',
            ]);

            $response->assertStatus(201);
            $this->assertDatabaseHas('shortcuts', ['UserID' => null]);
        });
    });

    describe('GET /{hash} - Redirect', function () {
        test('redirects to full URL', function () {
            $shortener = Shortener::factory()->create(['Hash' => 'test', 'Url' => 'https://example.com']);

            $response = $this->get('/test');

            $response->assertRedirect('https://example.com');
        });

        test('increments frequency on redirect', function () {
            $shortener = Shortener::factory()->create(['Hash' => 'test', 'Frequency' => 0]);

            $this->get('/test');

            expect($shortener->fresh()->Frequency)->toBe(1);
        });

        test('returns 404 for non-existent hash', function () {
            $response = $this->get('/nonexistent');

            $response->assertStatus(404);
        });

        test('respects publish date', function () {
            $future = now()->addDay();
            $shortener = Shortener::factory()->create([
                'Hash' => 'future',
                'PublishDate' => $future,
            ]);

            $response = $this->get('/future');

            $response->assertStatus(409);
        });
    });

    describe('GET /api/shorteners - List', function () {
        test('requires authentication', function () {
            $response = $this->get('/api/shorteners');

            $response->assertStatus(401);
        });

        test('returns user shorteners with pagination', function () {
            $shorteners = Shortener::factory(3)->create(['UserID' => $this->user->id]);

            $response = $this->actingAs($this->user)->get('/api/shorteners');

            $response->assertStatus(200);
            $response->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['ID', 'Hash', 'Url', 'Description', 'Frequency'],
                ],
                'pagination' => ['total', 'per_page', 'current_page', 'last_page'],
            ]);
            expect($response->json('pagination.total'))->toBe(3);
        });

        test('filters by search query', function () {
            $user2 = User::factory()->create();
            Shortener::factory()->create(['UserID' => $this->user->id, 'Hash' => 'found', 'Url' => 'https://found.com']);
            Shortener::factory()->create(['UserID' => $this->user->id, 'Hash' => 'other', 'Url' => 'https://other.com']);
            Shortener::factory()->create(['UserID' => $user2->id, 'Hash' => 'found-user2', 'Url' => 'https://found.com']);

            $response = $this->actingAs($this->user)->get('/api/shorteners?search=found');

            expect($response->json('pagination.total'))->toBe(1);
            expect($response->json('data.0.Hash'))->toBe('found');
        });

        test('excludes deleted shorteners', function () {
            $active = Shortener::factory()->create(['UserID' => $this->user->id]);
            $deleted = Shortener::factory()->create(['UserID' => $this->user->id]);
            $deleted->delete();

            $response = $this->actingAs($this->user)->get('/api/shorteners');

            expect($response->json('pagination.total'))->toBe(1);
        });
    });

    describe('PATCH /api/shorteners/{id} - Update', function () {
        test('requires authentication', function () {
            $shortener = Shortener::factory()->create();

            $response = $this->patch("/api/shorteners/{$shortener->ID}", ['description' => 'new']);

            $response->assertStatus(401);
        });

        test('requires ownership', function () {
            $other = User::factory()->create();
            $shortener = Shortener::factory()->create(['UserID' => $other->id]);

            $response = $this->actingAs($this->user)
                ->patch("/api/shorteners/{$shortener->ID}", ['description' => 'new']);

            $response->assertStatus(403);
        });

        test('updates description', function () {
            $shortener = Shortener::factory()->create(['UserID' => $this->user->id, 'Description' => 'old']);

            $response = $this->actingAs($this->user)
                ->patch("/api/shorteners/{$shortener->ID}", ['description' => 'new']);

            $response->assertStatus(200);
            expect($response->json('success'))->toBeTrue();
            $this->assertDatabaseHas('shortcuts', ['ID' => $shortener->ID, 'Description' => 'new']);
        });

        test('allows an expiration date on the publish date', function () {
            $this->travelTo('2026-11-20 10:00:00');
            $shortener = Shortener::factory()->create([
                'UserID' => $this->user->id,
                'PublishDate' => '2026-11-21 00:00',
                'ExpirationDate' => '2026-11-22 23:59:59',
            ]);

            $response = $this->actingAs($this->user)
                ->patch("/api/shorteners/{$shortener->ID}", [
                    'publish_date' => '2026-11-21 00:00',
                    'expiration_date' => '2026-11-21 23:59:59',
                ]);

            $response->assertOk();
            expect($shortener->fresh()->ExpirationDate->format('Y-m-d H:i:s'))->toBe('2026-11-21 23:59:59');
        });

        test('rejects an expiration date before the existing publish date', function () {
            $this->travelTo('2026-11-20 10:00:00');
            $shortener = Shortener::factory()->create([
                'UserID' => $this->user->id,
                'PublishDate' => '2026-11-21 00:00',
                'ExpirationDate' => '2026-11-22 23:59:59',
            ]);

            $response = $this->actingAs($this->user)
                ->patch("/api/shorteners/{$shortener->ID}", [
                    'expiration_date' => '2026-11-20 23:59:59',
                ]);

            $response->assertUnprocessable()
                ->assertJsonValidationErrors('expiration_date');
        });

        test('allows an expiration date after the existing publish date', function () {
            $this->travelTo('2026-11-20 10:00:00');
            $shortener = Shortener::factory()->create([
                'UserID' => $this->user->id,
                'PublishDate' => '2026-11-21 00:00',
                'ExpirationDate' => '2026-11-22 23:59:59',
            ]);

            $response = $this->actingAs($this->user)
                ->patch("/api/shorteners/{$shortener->ID}", [
                    'expiration_date' => '2026-11-23 23:59:59',
                ]);

            $response->assertOk();
            expect($shortener->fresh()->ExpirationDate->format('Y-m-d H:i:s'))->toBe('2026-11-23 23:59:59');
        });

        test('allows clearing the publish date while an expiration date remains', function () {
            $shortener = Shortener::factory()->create([
                'UserID' => $this->user->id,
                'PublishDate' => '2026-11-21 00:00',
                'ExpirationDate' => '2026-11-22 23:59:59',
            ]);

            $response = $this->actingAs($this->user)
                ->patch("/api/shorteners/{$shortener->ID}", [
                    'publish_date' => null,
                ]);

            $response->assertOk();
            expect($shortener->fresh()->PublishDate)->toBeNull();
        });
    });

    describe('DELETE /api/shorteners/{id} - Delete', function () {
        test('requires authentication', function () {
            $shortener = Shortener::factory()->create();

            $response = $this->delete("/api/shorteners/{$shortener->ID}");

            $response->assertStatus(401);
        });

        test('requires ownership', function () {
            $other = User::factory()->create();
            $shortener = Shortener::factory()->create(['UserID' => $other->id]);

            $response = $this->actingAs($this->user)->delete("/api/shorteners/{$shortener->ID}");

            $response->assertStatus(403);
        });

        test('soft deletes by default', function () {
            $shortener = Shortener::factory()->create(['UserID' => $this->user->id]);

            $response = $this->actingAs($this->user)->delete("/api/shorteners/{$shortener->ID}");

            $response->assertStatus(200);
            expect($shortener->fresh()->trashed())->toBeTrue();
        });

        test('force deletes with hard parameter', function () {
            $shortener = Shortener::factory()->create(['UserID' => $this->user->id]);

            $response = $this->actingAs($this->user)
                ->delete("/api/shorteners/{$shortener->ID}", ['hard' => true]);

            $response->assertStatus(200);
            $this->assertDatabaseMissing('shortcuts', ['ID' => $shortener->ID]);
        });
    });

    describe('GET /api/shorten - Get by Hash', function () {
        test('returns shortener by hash', function () {
            $shortener = Shortener::factory()->create(['Hash' => 'test', 'Url' => 'https://example.com']);

            $response = $this->get('/api/shorten?hash=test');

            $response->assertStatus(200);
            expect($response->json('data.url'))->toBe('https://example.com');
        });

        test('increments frequency', function () {
            $shortener = Shortener::factory()->create(['Hash' => 'test', 'Frequency' => 0]);

            $this->get('/api/shorten?hash=test');

            expect($shortener->fresh()->Frequency)->toBe(1);
        });

        test('returns 404 for non-existent hash', function () {
            $response = $this->get('/api/shorten?hash=nonexistent');

            $response->assertStatus(404);
        });

        test('requires hash parameter', function () {
            $response = $this->get('/api/shorten');

            $response->assertStatus(400);
        });
    });
});
