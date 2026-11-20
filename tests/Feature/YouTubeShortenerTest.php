<?php

use App\Models\Shortener;

it('redirects youtube links to ad-free embed version', function () {
    // Create a shortener with a YouTube URL
    $shortener = Shortener::factory()->create([
        'Url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    ]);

    // Follow the redirect
    $response = $this->get('/'.$shortener->Hash);

    // Should redirect to youtube-nocookie.com
    $response->assertRedirect('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?autoplay=1');
});

it('redirects short youtube links to ad-free embed version', function () {
    $shortener = Shortener::factory()->create([
        'Url' => 'https://youtu.be/dQw4w9WgXcQ',
    ]);

    $response = $this->get('/'.$shortener->Hash);

    $response->assertRedirect('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?autoplay=1');
});

it('keeps original URL in database unchanged', function () {
    $originalUrl = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
    $shortener = Shortener::factory()->create([
        'Url' => $originalUrl,
    ]);

    // Access the shortener
    $this->get('/'.$shortener->Hash);

    // Verify original URL is still in database
    $shortener->refresh();
    expect($shortener->Url)->toBe($originalUrl);
});

it('redirects non-youtube links normally', function () {
    $url = 'https://www.example.com';
    $shortener = Shortener::factory()->create([
        'Url' => $url,
    ]);

    $response = $this->get('/'.$shortener->Hash);

    $response->assertRedirect($url);
});
