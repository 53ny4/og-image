<?php

namespace s3ny4\OgImage;

use Imagine\Gd\Font;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;

class OgText
{
    private $text;
    private $position = ['x' => 'center', 'y' => 'center'];
    private $color = '000000';
    private $size = 24;
    private $fontFile = __DIR__ . '/assets/fonts/Bebas-Regular.ttf'; // Ensure you have this font or specify another

    // Background properties
    private $hasBackground = false;
    private $backgroundColor = '000000';
    private $backgroundOpacity = 100; // Opacity (0-100)

    // Text wrapping
    private $maxWidth; // Maximum width for text wrapping

    // Padding property (vertical padding)
    private $padding = 10; // Default vertical padding in pixels

    public function setText($text)
    {
        $this->text = $text;
    }

    public function setPosition($x, $y)
    {
        $this->position = ['x' => $x, 'y' => $y];
    }

    public function setColor($color)
    {
        $this->color = $color;
    }

    public function setSize($size)
    {
        $this->size = $size;
    }

    public function setFontFile($fontFile)
    {
        $this->fontFile = $fontFile;
    }

    /**
     * Enables background under the text.
     *
     * @param string $color   Background color in hex without '#'
     * @param int    $opacity Opacity from 0 (transparent) to 100 (opaque)
     */
    public function setBackground($color, $opacity = 100)
    {
        $this->hasBackground = true;
        $this->backgroundColor = $color;
        $this->backgroundOpacity = max(0, min(100, $opacity));
    }

    /**
     * Sets the maximum width for text wrapping.
     *
     * @param int $maxWidth Maximum width in pixels
     */
    public function setMaxWidth($maxWidth)
    {
        $this->maxWidth = $maxWidth;
    }

    /**
     * Sets the vertical padding around the text.
     *
     * @param int $padding Padding in pixels
     */
    public function setPadding($padding)
    {
        $this->padding = $padding;
    }

    public function applyToImage($image, $imageWidth, $imageHeight)
    {
        $palette = new RGB();
        $textColor = $palette->color('#' . $this->color, 100);
        $font = new Font($this->fontFile, $this->size, $textColor);

        // Determine maximum text width (full image width)
        $maxTextWidth = $this->maxWidth ?: $imageWidth; // Full image width

        // Wrap text into lines
        $lines = $this->wrapText($font, $this->text, $maxTextWidth);

        // Calculate total height of text block
        $lineHeight = $font->getSize() + 5; // Add 5 pixels of line spacing
        $textBlockHeight = count($lines) * $lineHeight;

        // Calculate starting Y position, including top padding
        $yStart = $this->calculatePosition($this->position['y'], $textBlockHeight + 2 * $this->padding, $imageHeight);

        // xStart is 0 since background rectangle is full width
        $xStart = 0;

        // Draw background rectangle if enabled
        if ($this->hasBackground) {
            $bgColor = $palette->color('#' . $this->backgroundColor, $this->backgroundOpacity);
            $image->draw()->rectangle(
                new Point(0, $yStart),
                new Point($imageWidth, $yStart + $textBlockHeight + 2 * $this->padding),
                $bgColor,
                true // Fill the rectangle
            );
        }

        // Draw each line of text
        foreach ($lines as $i => $line) {
            $textBox = $font->box($line);

            // Calculate x position based on alignment
            $x = $this->calculatePosition($this->position['x'], $textBox->getWidth(), $imageWidth);

            $y = $yStart + $this->padding + $i * $lineHeight;
            $point = new Point($x, $y);
            $image->draw()->text($line, $font, $point);
        }
    }

    private function wrapText($font, $text, $maxWidth)
    {
        $words = explode(' ', $text);
        $lines = [];
        $currentLine = '';

        foreach ($words as $word) {
            $testLine = $currentLine ? $currentLine . ' ' . $word : $word;
            $testBox = $font->box($testLine);

            if ($testBox->getWidth() <= $maxWidth) {
                $currentLine = $testLine;
            } else {
                if ($currentLine) {
                    $lines[] = $currentLine;
                }
                $currentLine = $word;

                // If a single word is longer than maxWidth, we need to split it
                $testBox = $font->box($currentLine);
                while ($testBox->getWidth() > $maxWidth && strlen($currentLine) > 1) {
                    $cutOff = floor(strlen($currentLine) * $maxWidth / $testBox->getWidth());
                    $part = substr($currentLine, 0, $cutOff);
                    $lines[] = $part;
                    $currentLine = substr($currentLine, $cutOff);
                    $testBox = $font->box($currentLine);
                }
            }
        }

        if ($currentLine) {
            $lines[] = $currentLine;
        }

        return $lines;
    }

    private function calculatePosition($position, $elementSize, $imageSize)
    {
        if ($position === 'center') {
            return intval(($imageSize - $elementSize) / 2);
        } elseif ($position === 'left' || $position === 'top') {
            return 0;
        } elseif ($position === 'right' || $position === 'bottom') {
            return $imageSize - $elementSize;
        } else {
            return intval($position);
        }
    }
}
