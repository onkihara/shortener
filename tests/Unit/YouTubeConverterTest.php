<?php

use App\Services\YouTubeConverter;

it('detects youtube.com URLs', function () {
    $converter = new YouTubeConverter;
    $url = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
    expect($converter->isYouTubeUrl($url))->toBeTrue();
});

it('detects youtu.be URLs', function () {
    $converter = new YouTubeConverter;
    $url = 'https://youtu.be/dQw4w9WgXcQ';
    expect($converter->isYouTubeUrl($url))->toBeTrue();
});

it('rejects non-youtube URLs', function () {
    $converter = new YouTubeConverter;
    $url = 'https://www.example.com';
    expect($converter->isYouTubeUrl($url))->toBeFalse();
});

it('converts youtube.com watch URL to ad-free embed', function () {
    $converter = new YouTubeConverter;
    $url = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
    $result = $converter->convertToAdFreeEmbed($url);
    expect($result)->toContain('youtube-nocookie.com/embed/dQw4w9WgXcQ');
});

it('converts youtu.be URL to ad-free embed', function () {
    $converter = new YouTubeConverter;
    $url = 'https://youtu.be/dQw4w9WgXcQ';
    $result = $converter->convertToAdFreeEmbed($url);
    expect($result)->toContain('youtube-nocookie.com/embed/dQw4w9WgXcQ');
});

it('handles URLs with additional parameters', function () {
    $converter = new YouTubeConverter;
    $url = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ&t=10s&list=PLxxx';
    $result = $converter->convertToAdFreeEmbed($url);
    expect($result)->toContain('youtube-nocookie.com/embed/dQw4w9WgXcQ');
});
