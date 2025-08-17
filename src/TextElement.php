<?php
namespace s3ny4\OgImage;

use Imagine\Image\Point;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\RGB;
use Imagine\Image\ImagineInterface;
use s3ny4\OgImage\Traits\PositionableTrait;

class TextElement {
    use PositionableTrait;

    private string $text;
    private string $fontPath = 'path/to/font.ttf';
    private int $fontSize = 36;
    private string $fontColor = '#ffffff';
    private ?string $bgColor = null;
    private int $bgOpacity = 100;   // 0 = transparent, 100 = opaque
    private int $lineHeight = 48;     // custom line height in px; 0 = auto
    private ?int $textBoxWidth = null;  // custom text box width in px; null = use image width

    public function setText(string $text): self {
        $this->text = $text;
        return $this;
    }

    public function setFontPath(string $fontPath): self {
        $this->fontPath = $fontPath;
        return $this;
    }

    public function setFontSize(int $fontSize): self {
        $this->fontSize = $fontSize;
        return $this;
    }

    public function setFontColor(string $fontColor): self {
        $this->fontColor = $fontColor;
        return $this;
    }

    public function setBgColor(?string $bgColor): self {
        $this->bgColor = $bgColor;
        return $this;
    }

    public function setBgOpacity(int $opacity): self {
        $this->bgOpacity = max(0, min(100, $opacity));
        return $this;
    }

    /**
     * Set a custom line height in pixels (applied between lines).
     * Pass 0 to use font’s default height.
     */
    public function setLineHeight(int $height): self {
        $this->lineHeight = max(0, $height);
        return $this;
    }

    /**
     * Set the text box width in pixels.
     * This controls the maximum width available for text wrapping.
     * If null, uses the full image width minus padding.
     */
    public function setTextBoxWidth(?int $width): self {
        $this->textBoxWidth = $width;
        return $this;
    }

    /**
     * Apply the text element to the image, with word-wrap and conditional line height.
     */
    public function apply(ImageInterface $image, int $imageWidth, int $imageHeight, ImagineInterface $imagine, RGB $palette): void {
        if (!file_exists($this->fontPath)) {
            throw new \RuntimeException("Font not found at {$this->fontPath}");
        }

        // Load font
        $font = $imagine->font(
            $this->fontPath,
            $this->fontSize,
            $palette->color($this->fontColor, 100)
        );

        // Determine the effective text box width
        $effectiveTextBoxWidth = $this->textBoxWidth ?? ($imageWidth - 2 * $this->paddingH);
        
        // Word wrap into lines based on text box width
        $maxTextWidth = $effectiveTextBoxWidth - 2 * $this->paddingH;
        $words = preg_split('/\s+/', $this->text);
        $lines = [];
        $current = '';
        foreach ($words as $word) {
            $test = $current === '' ? $word : "$current $word";
            $box = $font->box($test);
            if ($box->getWidth() > $maxTextWidth && $current !== '') {
                $lines[] = $current;
                $current = $word;
            } else {
                $current = $test;
            }
        }
        if ($current !== '') {
            $lines[] = $current;
        }

        // Calculate default font height
        $defaultHeight = $font->box('Mg')->getHeight();
        $needsCustomLH = count($lines) > 1 && $this->lineHeight > 0;
        // Determine line spacing
        $lh = $needsCustomLH ? $this->lineHeight : $defaultHeight;
        // Total block height: defaultHeight + (lines-1)*lh if custom, else lines*lh
        $totalHeight = $needsCustomLH
            ? ($defaultHeight + ($lh * (count($lines) - 1)))
            : (count($lines) * $lh);

        // Compute max line width for positioning
        $maxLineWidth = 0;
        foreach ($lines as $l) {
            $w = $font->box($l)->getWidth();
            $maxLineWidth = max($maxLineWidth, $w);
        }

        // If using custom text box width, center the text box within the image
        if ($this->textBoxWidth !== null) {
            // Calculate text box margins to center it within the image
            $textBoxMargin = ($imageWidth - $this->textBoxWidth) / 2;
            // Compute X position within the text box
            $textBoxX = $this->computeX($this->textBoxWidth, $maxLineWidth);
            $x = $textBoxMargin + $textBoxX;
        } else {
            // Use original positioning logic
            $x = $this->computeX($imageWidth, $maxLineWidth);
        }
        
        $y = $this->computeY($imageHeight, $totalHeight);

        // Draw background if specified
        if ($this->bgColor !== null) {
            $bg = $palette->color($this->bgColor, $this->bgOpacity);
            if ($this->textBoxWidth !== null) {
                // Background spans the text box width
                $textBoxMargin = ($imageWidth - $this->textBoxWidth) / 2;
                $startX = (int)$textBoxMargin;
                $endX   = (int)($textBoxMargin + $this->textBoxWidth);
            } else {
                // Background spans full width
                $startX = 0;
                $endX   = $imageWidth;
            }
            $startY = max(0, $y - $this->paddingV);
            $endY   = min($imageHeight, $y + $totalHeight + $this->paddingV);
            $image->draw()->rectangle(
                new Point($startX, $startY),
                new Point($endX,   $endY),
                $bg, $bg
            );
        }

        // Draw each line
        foreach ($lines as $i => $line) {
            $dy = $y + ($i * $lh);
            $image->draw()->text($line, $font, new Point($x, $dy));
        }
    }
}
