<?php

namespace App\Services;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\GifWriter;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Writer\WebPWriter;
use Endroid\QrCode\Writer\WriterInterface;
use InvalidArgumentException;

class QrCodeGenerator
{
    /**
     * Generate a QR code from text/URL
     *
     * @param  string  $text  The text to encode
     * @param  string  $format  The output format ('png', 'gif', 'svg' or 'webp')
     * @param  string  $foregroundColor  Hex color for QR code (e.g., '#000000')
     * @param  string  $backgroundColor  Hex color for background (e.g., '#FFFFFF')
     * @param  int  $size  Size of the QR code (default: 300)
     * @return string Base64 encoded image data
     */
    public function generate(
        string $text,
        string $format = 'png',
        string $foregroundColor = '#000000',
        string $backgroundColor = '#FFFFFF',
        int $size = 300
    ): string {
        // Convert hex colors to RGB
        $fgColor = $this->hexToRgb($foregroundColor);
        $bgColor = $this->hexToRgb($backgroundColor);

        // Create the QR code
        $qrCode = new QrCode(
            data: $text,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: $size,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(
                $fgColor['r'],
                $fgColor['g'],
                $fgColor['b']
            ),
            backgroundColor: new Color(
                $bgColor['r'],
                $bgColor['g'],
                $bgColor['b']
            )
        );

        $writer = $this->getWriter($format);
        $result = $writer->write($qrCode);

        // Return base64 encoded image
        return base64_encode($result->getString());
    }

    /**
     * Generate a QR code and save it to a file
     *
     * @param  string  $text  The text to encode
     * @param  string  $filePath  Path where to save the file
     * @param  string  $format  The output format ('png', 'gif', 'svg' or 'webp')
     * @param  string  $foregroundColor  Hex color for QR code
     * @param  string  $backgroundColor  Hex color for background
     * @param  int  $size  Size of the QR code
     * @return string The file path
     */
    public function generateAndSave(
        string $text,
        string $filePath,
        string $format = 'png',
        string $foregroundColor = '#000000',
        string $backgroundColor = '#FFFFFF',
        int $size = 300
    ): string {
        // Convert hex colors to RGB
        $fgColor = $this->hexToRgb($foregroundColor);
        $bgColor = $this->hexToRgb($backgroundColor);

        // Create the QR code
        $qrCode = new QrCode(
            data: $text,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: $size,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(
                $fgColor['r'],
                $fgColor['g'],
                $fgColor['b']
            ),
            backgroundColor: new Color(
                $bgColor['r'],
                $bgColor['g'],
                $bgColor['b']
            )
        );

        $writer = $this->getWriter($format);
        $result = $writer->write($qrCode);
        $result->saveToFile($filePath);

        return $filePath;
    }

    /**
     * Get the appropriate writer based on format
     *
     * @param  string  $format  The output format
     */
    private function getWriter(string $format): WriterInterface
    {
        return match (strtolower($format)) {
            'png' => new PngWriter,
            'gif' => new GifWriter,
            'svg' => new SvgWriter,
            'webp' => new WebPWriter,
            default => throw new InvalidArgumentException("Unsupported QR code format: {$format}"),
        };
    }

    /**
     * Convert hex color to RGB array
     *
     * @param  string  $hex  Hex color code (e.g., '#000000' or '000000')
     * @return array RGB values
     */
    private function hexToRgb(string $hex): array
    {
        // Remove # if present
        $hex = ltrim($hex, '#');

        // Handle short hex format (e.g., 'F00' -> 'FF0000')
        if (strlen($hex) === 3) {
            $hex = str_repeat($hex[0], 2).str_repeat($hex[1], 2).str_repeat($hex[2], 2);
        }

        // Convert hex to RGB
        [$r, $g, $b] = sscanf($hex, '%02x%02x%02x');

        return [
            'r' => $r,
            'g' => $g,
            'b' => $b,
        ];
    }
}
