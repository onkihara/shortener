<?php

use App\Models\Shortener;
use Carbon\Carbon;

describe('Shortener Expiration and Publish Date', function () {
    it('returns 404 before the publish date and redirects at the publish date', function () {
        $this->travelTo(Carbon::parse('2026-11-20 12:00:00'));

        $response = $this->postJson(route('shortener.store'), [
            'url' => 'https://example.com',
            'hash' => 'scheduled',
            'publish_date' => '2026-11-20 12:01',
        ])->assertCreated();

        $shortener = Shortener::where('Hash', $response->json('data.hash'))->firstOrFail();

        $this->get(route('shortener.redirect', $shortener->Hash))->assertStatus(409);
        expect($shortener->fresh()->Frequency)->toBe(0);

        $this->travelTo(Carbon::parse('2026-11-20 12:01:00'));

        $this->get(route('shortener.redirect', $shortener->Hash))
            ->assertStatus(301)
            ->assertRedirect('https://example.com');
        expect($shortener->fresh()->Frequency)->toBe(1);
    });

    it('redirects immediately when no publish date is set', function () {
        $shortener = Shortener::factory()->create([
            'Hash' => 'immediate',
            'Url' => 'https://example.com',
            'PublishDate' => null,
        ]);

        $this->get(route('shortener.redirect', $shortener->Hash))
            ->assertStatus(301)
            ->assertRedirect('https://example.com');
    });

    it('allows a publish date in the past', function () {
        $this->travelTo(Carbon::parse('2026-11-20 12:00:00'));

        $this->postJson(route('shortener.store'), [
            'url' => 'https://example.com',
            'hash' => 'past-publish',
            'publish_date' => '2026-11-19 00:00',
            'expiration_date' => '2026-11-21 23:59:59',
        ])
            ->assertCreated();

        $shortener = Shortener::where('Hash', 'past-publish')->firstOrFail();
        expect($shortener->PublishDate->format('Y-m-d H:i'))->toBe('2026-11-19 00:00');
        expect($shortener->ExpirationDate->format('Y-m-d H:i:s'))->toBe('2026-11-21 23:59:59');
    });

    it('treats a link as expired at the exact expiration time', function () {
        $this->travelTo(Carbon::parse('2026-11-20 12:00:00'));
        $shortener = Shortener::factory()->create([
            'Hash' => 'expires-now',
            'Url' => 'https://example.com',
            'ExpirationDate' => '2026-11-20 12:00:00',
        ]);

        expect($shortener->isExpired())->toBeTrue();
        $this->get(route('shortener.redirect', $shortener->Hash))->assertStatus(410);
    });

    describe('scopePublished', function () {
        it('includes links without publish date', function () {
            $shortener = Shortener::create([
                'Hash' => 'test123',
                'Url' => 'https://example.com',
                'PublishDate' => null,
                'ExpirationDate' => null,
            ]);

            expect(Shortener::published()->where('ID', $shortener->ID)->exists())->toBeTrue();
        });

        it('includes links with past publish date', function () {
            $shortener = Shortener::create([
                'Hash' => 'test456',
                'Url' => 'https://example.com',
                'PublishDate' => now()->subDay(),
                'ExpirationDate' => null,
            ]);

            expect(Shortener::published()->where('ID', $shortener->ID)->exists())->toBeTrue();
        });

        it('excludes links with future publish date', function () {
            $shortener = Shortener::create([
                'Hash' => 'test789',
                'Url' => 'https://example.com',
                'PublishDate' => now()->addDay(),
                'ExpirationDate' => null,
            ]);

            expect(Shortener::published()->where('ID', $shortener->ID)->exists())->toBeFalse();
        });

        it('includes links without expiration date', function () {
            $shortener = Shortener::create([
                'Hash' => 'test111',
                'Url' => 'https://example.com',
                'PublishDate' => null,
                'ExpirationDate' => null,
            ]);

            expect(Shortener::published()->where('ID', $shortener->ID)->exists())->toBeTrue();
        });

        it('includes links with future expiration date', function () {
            $shortener = Shortener::create([
                'Hash' => 'test222',
                'Url' => 'https://example.com',
                'PublishDate' => null,
                'ExpirationDate' => now()->addDay(),
            ]);

            expect(Shortener::published()->where('ID', $shortener->ID)->exists())->toBeTrue();
        });

        it('excludes expired links', function () {
            $shortener = Shortener::create([
                'Hash' => 'test333',
                'Url' => 'https://example.com',
                'PublishDate' => null,
                'ExpirationDate' => now()->subDay(),
            ]);

            expect(Shortener::published()->where('ID', $shortener->ID)->exists())->toBeFalse();
        });

        it('excludes links that are not published yet and are expired', function () {
            $shortener = Shortener::create([
                'Hash' => 'test444',
                'Url' => 'https://example.com',
                'PublishDate' => now()->addDay(),
                'ExpirationDate' => now()->subDay(),
            ]);

            expect(Shortener::published()->where('ID', $shortener->ID)->exists())->toBeFalse();
        });
    });

    describe('isExpired', function () {
        it('returns false if no expiration date is set', function () {
            $shortener = Shortener::create([
                'Hash' => 'test555',
                'Url' => 'https://example.com',
                'ExpirationDate' => null,
            ]);

            expect($shortener->isExpired())->toBeFalse();
        });

        it('returns false if expiration date is in the future', function () {
            $shortener = Shortener::create([
                'Hash' => 'test666',
                'Url' => 'https://example.com',
                'ExpirationDate' => now()->addDay(),
            ]);

            expect($shortener->isExpired())->toBeFalse();
        });

        it('returns true if expiration date is in the past', function () {
            $shortener = Shortener::create([
                'Hash' => 'test777',
                'Url' => 'https://example.com',
                'ExpirationDate' => now()->subDay(),
            ]);

            expect($shortener->isExpired())->toBeTrue();
        });
    });

    describe('fillable fields', function () {
        it('includes ExpirationDate in fillable array', function () {
            $fillable = (new Shortener)->getFillable();
            expect($fillable)->toContain('ExpirationDate');
        });

        it('includes PublishDate in fillable array', function () {
            $fillable = (new Shortener)->getFillable();
            expect($fillable)->toContain('PublishDate');
        });
    });
});
