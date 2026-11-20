<?php

namespace App\Services;

class YouTubeConverter
{
    /**
     * Check if URL is a YouTube link
     */
    public function isYouTubeUrl(string $url): bool
    {
        return preg_match(
            '/^https?:\/\/(www\.)?(youtube\.com\/watch|youtu\.be\/)/i',
            $url
        ) === 1;
    }

    /**
     * Convert YouTube URL to ad-free embed URL
     */
    public function convertToAdFreeEmbed(string $url): string
    {
        // Extract video ID
        $videoId = $this->extractVideoId($url);

        if (! $videoId) {
            return $url;
        }

        // Return ad-free embed URL using youtube-nocookie.com
        return 'https://www.youtube-nocookie.com/embed/'.$videoId.'?autoplay=1';
    }

    /**
     * Extract YouTube video ID from various YouTube URL formats
     */
    private function extractVideoId(string $url): ?string
    {
        $patterns = [
            // youtube.com/watch?v=...
            '/youtube\.com\/watch\?.*v=([a-zA-Z0-9_-]{11})/i',
            // youtu.be/...
            '/youtu\.be\/([a-zA-Z0-9_-]{11})/i',
            // youtube.com/embed/...
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/i',
            // youtube-nocookie.com/embed/...
            '/youtube-nocookie\.com\/embed\/([a-zA-Z0-9_-]{11})/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }
}
