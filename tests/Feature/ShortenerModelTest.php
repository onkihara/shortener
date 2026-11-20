<?php

use App\Models\Shortener;
use App\Models\User;

describe('Shortener Model', function () {
    beforeEach(function () {
        // Create test user
        $this->user = User::factory()->create();
    });

    describe('Hash Generation', function () {
        test('generates unique hash automatically', function () {
            $shortener = new Shortener;
            $hash = $shortener->generateHash();

            expect($hash)
                ->toBeString()
                ->not->toBeEmpty()
                ->toHaveLength(6, 7, 8); // Should be one of these lengths
        });

        test('validates custom hash and returns it if unique', function () {
            $customHash = 'my-custom-link';
            $shortener = new Shortener;
            $hash = $shortener->generateHash($customHash);

            expect($hash)->toBe('my-custom-link');
        });

        test('throws exception when custom hash is taken', function () {
            Shortener::factory()->create(['Hash' => 'taken']);

            $shortener = new Shortener;
            $shortener->generateHash('taken');
        })->throws(Exception::class, 'already taken');

        test('converts custom hash to slug format', function () {
            $customHash = 'My_Custom-Link';
            $shortener = new Shortener;
            $hash = $shortener->generateHash($customHash);

            // Str::slug converts to lowercase
            expect($hash)->toBe('my-custom-link');
        });
    });

    describe('Relationships', function () {
        test('belongs to user', function () {
            $shortener = Shortener::factory()->create(['UserID' => $this->user->id]);

            expect($shortener->user)->toBeTruthy();
            expect($shortener->user->id)->toBe($this->user->id);
        });

        test('can have null user (anonymous)', function () {
            $shortener = Shortener::factory()->create(['UserID' => null]);

            expect($shortener->user)->toBeNull();
            expect($shortener->UserID)->toBeNull();
        });
    });

    describe('Scopes', function () {
        test('active scope excludes deleted shorteners', function () {
            $active = Shortener::factory()->create();
            $deleted = Shortener::factory()->create();
            $deleted->delete();

            $result = Shortener::active()->get();

            expect($result)->toHaveCount(1);
            expect($result->first()->ID)->toBe($active->ID);
        });

        test('published scope excludes future publish dates', function () {
            $past = now()->subDays(2);
            $future = now()->addDays(2);
            $published = Shortener::factory()->create(['PublishDate' => $past]);
            $unpublished = Shortener::factory()->create(['PublishDate' => $future]);
            $noDate = Shortener::factory()->create(['PublishDate' => null]);

            $result = Shortener::published()->get();

            // Verify that future published is NOT in result
            expect($result->pluck('ID'))->not->toContain($unpublished->ID);
            // Verify that published and no date ARE in result
            expect($result->pluck('ID'))->toContain($published->ID, $noDate->ID);
        });

        test('byUser scope filters by user id', function () {
            $user2 = User::factory()->create();
            $shortener1 = Shortener::factory()->create(['UserID' => $this->user->id]);
            $shortener2 = Shortener::factory()->create(['UserID' => $user2->id]);

            $result = Shortener::byUser($this->user->id)->get();

            expect($result)->toHaveCount(1);
            expect($result->first()->ID)->toBe($shortener1->ID);
        });
    });

    describe('Methods', function () {
        test('isPublished returns correct value', function () {
            $published = Shortener::factory()->create(['PublishDate' => now()->subDay()]);
            $future = Shortener::factory()->create(['PublishDate' => now()->addDay()]);
            $noDate = Shortener::factory()->create(['PublishDate' => null]);

            expect($published->isPublished())->toBeTrue();
            expect($future->isPublished())->toBeFalse();
            expect($noDate->isPublished())->toBeTrue();
        });

        test('recordAccess increments frequency', function () {
            $shortener = Shortener::factory()->create(['Frequency' => 5]);

            $shortener->recordAccess();

            expect($shortener->fresh()->Frequency)->toBe(6);
        });
    });

    describe('Validation', function () {
        test('fillable array includes required fields', function () {
            $fillable = (new Shortener)->getFillable();

            expect($fillable)->toContain('UserID', 'Hash', 'Url', 'Description', 'Frequency', 'PublishDate');
        });
    });
});
