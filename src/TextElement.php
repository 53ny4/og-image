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
    private string $fontPath;
    private int $fontSize = 36;
    private string $fontColor = '#ffffff';
    private ?string $bgColor = null;
    private int $bgOpacity = 100;   // 0 = transparent, 100 = opaque
    private int $lineHeight = 48;     // custom line height in px; 0 = auto
    private ?int $textBoxWidth = null;  // custom text box width in px; null = use image width

    public function __construct() {
        $this->fontPath = __DIR__ . '/assets/fonts/BebasNeue-Regular.ttf';
    }

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

        // Word wrap into lines
        $maxTextWidth = $imageWidth - 2 * $this->paddingH;
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

        // Compute max line width
        $maxLineWidth = 0;
        foreach ($lines as $l) {
            $w = $font->box($l)->getWidth();
            $maxLineWidth = max($maxLineWidth, $w);
        }

        // Compute starting position
        $x = $this->computeX($imageWidth, $maxLineWidth);
        $y = $this->computeY($imageHeight, $totalHeight);

        // Draw background if specified
        if ($this->bgColor !== null) {
            $bg = $palette->color($this->bgColor, $this->bgOpacity);
            $startX = 0;
            $startY = max(0, $y - $this->paddingV);
            $endX   = $imageWidth;
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
