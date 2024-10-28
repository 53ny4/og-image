<?php

namespace s3ny4\OgImage;

use Imagine\Gd\Font;
use Imagine\Image\FontInterface;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;

class OgText
{


    /**
     * @var string $text The text to be rendered.
     */
    private string $text;

    /**
     * @var array $position The position of the text.
     *                      'x' can be 'center', 'left', 'right', or a specific pixel value.
     *                      'y' can be 'center', 'top', 'bottom', or a specific pixel value.
     */
    private array $position = ['x' => 'center', 'y' => 'center'];

    /**
     * @var string $color The color of the text in hexadecimal format (with or without '#').
     */
    private string $color = '000000';

    /**
     * @var int $size The font size of the text.
     */
    private int $size = 24;

    /**
     * @var string $fontFile The path to the font file to be used for the text.
     */
    private string $fontFile = __DIR__ . '/assets/fonts/Bebas-Regular.ttf'; // Ensure you have this font or specify another

// Background properties

    /**
     * @var bool $hasBackground Indicates if the text has a background.
     */
    private bool $hasBackground = false;

    /**
     * @var string $backgroundColor The background color in hexadecimal format (without '#').
     */
    private $backgroundColor = '000000';

    /**
     * @var int $backgroundOpacity The opacity of the background (0-100).
     */
    private $backgroundOpacity = 100; // Opacity (0-100)

// Text wrapping

    /**
     * @var int|null $maxWidth The maximum width for text wrapping in pixels.
     */
    private $maxWidth; // Maximum width for text wrapping

// Padding property (vertical padding)

    /**
     * @var int $padding The vertical padding around the text in pixels.
     */
    private int $padding = 10; // Default vertical padding in pixels

    public $utils;


    public function __construct()
    {
        $this->utils = new OgUtils();
    }

    /**
     * Sets the text to be rendered.
     *
     * @param string $text The text to be rendered.
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * Sets the position of the text.
     *
     * @param string|int $x The horizontal position ('center', 'left', 'right', or a specific pixel value).
     * @param string|int $y The vertical position ('center', 'top', 'bottom', or a specific pixel value).
     */
    public function setPosition($x, $y)
    {
        $this->position = ['x' => $x, 'y' => $y];
    }

    /**
     * Sets the color of the text.
     *
     * @param string $color The color of the text in hexadecimal format (with or without '#').
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * Sets the font size of the text.
     *
     * @param int $size The font size of the text.
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * Sets the path to the font file to be used for the text.
     *
     * @param string $fontFile The path to the font file.
     */
    public function setFontFile($fontFile)
    {
        $this->fontFile = $fontFile;
    }

    /**
     * Enables background under the text.
     *
     * @param string $color Background color in hex with or without '#'
     * @param int $opacity Opacity from 0 (transparent) to 100 (opaque)
     */
    public function setBackground($color, $opacity = 100): void
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
    public function setMaxWidth($maxWidth): void
    {
        $this->maxWidth = $maxWidth;
    }

    /**
     * Sets the vertical padding around the text.
     *
     * @param int $padding Padding in pixels
     */
    public function setPadding($padding): void
    {
        $this->padding = $padding;
    }

    /**
     * Applies the text settings to the given image.
     *
     * This function draws the text on the provided image based on the current settings.
     * It handles text wrapping, background drawing, and text positioning.
     *
     * @param ImageInterface $image The image to which the text will be applied.
     * @param int $imageWidth The width of the image.
     * @param int $imageHeight The height of the image.
     */
    public function applyToImage($image, $imageWidth, $imageHeight)
    {
        $palette = new RGB();
        $textColor = $palette->color('#' . $this->color, 100);
        $font = new Font($this->fontFile, $this->size, $textColor);

        // Determine maximum text width (full image width)
        $maxTextWidth = $this->maxWidth ?: $imageWidth; // Full image width

        // Wrap text into lines
        $lines = $this->wrapText($font,$this->text, $maxTextWidth); // @todo: Implement wrapText method from Imagine/Imagine


        // Calculate total height of text block
        $lineHeight = $font->getSize() + 5; // Add 5 pixels of line spacing
        $textBlockHeight = count($lines) * $lineHeight;

        // Calculate starting Y position, including top padding
        $yStart = $this->utils->calculatePosition($this->position['y'], $textBlockHeight + 2 * $this->padding, $imageHeight);

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
            $x = (new OgUtils())->calculatePosition($this->position['x'], $textBox->getWidth(), $imageWidth);

            $y = $yStart + $this->padding + $i * $lineHeight;
            $point = new Point($x, $y);
            $image->draw()->text($line, $font, $point);
        }
    }

    /**
     * Wraps the given text into lines that fit within the specified maximum width.
     *
     * This function splits the text into words and arranges them into lines
     * such that each line's width does not exceed the specified maximum width.
     * If a single word is longer than the maximum width, it splits the word.
     *
     * @param \Imagine\Gd\Font $font The font used to measure the text width.
     * @param string $text The text to be wrapped.
     * @param int $maxWidth The maximum width for each line in pixels.
     * @return array An array of strings, each representing a line of wrapped text.
     */
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

    public function setFont(string $string)
    {
        $this->fontFile = $string;
    }
}
