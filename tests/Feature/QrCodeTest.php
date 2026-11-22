<?php

use App\Services\QrCodeGenerator;

it('returns a QR code with the requested image format and MIME type', function (string $format, string $mimeType) {
    $response = $this->postJson(route('qr-code.generate'), [
        'text' => 'https://example.com',
        'format' => $format,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.format', $format);

    $image = $response->json('data.image');
    $prefix = "data:{$mimeType};base64,";
    expect($image)->toStartWith($prefix);

    $contents = base64_decode(substr($image, strlen($prefix)), true);
    expect($contents)->not->toBeFalse();
    expect((new finfo(FILEINFO_MIME_TYPE))->buffer($contents))->toBe($mimeType);
})->with([
    'PNG' => ['png', 'image/png'],
    'GIF' => ['gif', 'image/gif'],
    'SVG' => ['svg', 'image/svg+xml'],
    'WebP' => ['webp', 'image/webp'],
]);

it('defaults to PNG when no format is supplied', function () {
    $response = $this->postJson(route('qr-code.generate'), ['text' => 'https://example.com']);

    $response->assertOk()->assertJsonPath('data.format', 'png');
    expect($response->json('data.image'))->toStartWith('data:image/png;base64,');
});

it('returns 422 for unsupported image formats', function (string $format) {
    $this->postJson(route('qr-code.generate'), [
        'text' => 'https://example.com',
        'format' => $format,
    ])->assertUnprocessable()->assertJsonValidationErrors('format');
})->with(['jpg', 'jpeg', 'invalid']);

it('rejects unsupported image formats in the generator', function () {
    app(QrCodeGenerator::class)->generate('https://example.com', 'jpeg');
})->throws(InvalidArgumentException::class, 'Unsupported QR code format: jpeg');
