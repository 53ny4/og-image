<?php
namespace s3ny4\OgImage;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\Palette\RGB;
use Imagine\Image\ImageInterface;

class OgImageGenerator {
    private Imagine $imagine;
    private ImageInterface $canvas;
    private int $width;
    private int $height;
    private RGB $palette;
    private string $format = 'webp';

    private ?string $bgImagePath = null;
    private ?int $bgImageW     = null;
    private ?int $bgImageH     = null;

    public function __construct() {
        $this->imagine = new Imagine();
        $this->palette = new RGB();
    }

    /**
     * Specify a background image file to use instead of a flat color.
     */
    public function setBackgroundImage(string $path): self {
        if (!file_exists($path)) {
            throw new \RuntimeException("Background image not found: {$path}");
        }
        $this->bgImagePath = $path;
        return $this;
    }

    /**
     * Optionally set a max width and/or height for the background image.
     */
    public function setBackgroundSize(?int $width = null, ?int $height = null): self {
        $this->bgImageW = $width;
        $this->bgImageH = $height;
        return $this;
    }

    /**
     * Initialize the canvas with either a background color or image.
     * If a background image is set, it will be resized to cover the canvas entirely (cropping as needed).
     */
    public function createImage(int $width = 1200, int $height = 630, string $backgroundColor = '#FFFFFF'): self {
        $this->width  = $width;
        $this->height = $height;

        if ($this->bgImagePath) {
            // Load and resize background to cover the canvas
            $bg       = $this->imagine->open($this->bgImagePath);
            $origSize = $bg->getSize();
            $origW    = $origSize->getWidth();
            $origH    = $origSize->getHeight();
            $targetW  = $this->bgImageW ?? $width;
            $targetH  = $this->bgImageH ?? $height;
            // Cover ratio: ensure bg covers full canvas
            $ratio    = max($targetW / $origW, $targetH / $origH);
            $newW     = (int) round($origW * $ratio);
            $newH     = (int) round($origH * $ratio);
            $resized  = $bg->resize(new Box($newW, $newH));
            // Crop center region of size canvas
            $cropX    = (int) round(($newW - $width) / 2);
            $cropY    = (int) round(($newH - $height) / 2);
            $this->canvas = $resized->crop(new Point($cropX, $cropY), new Box($width, $height));
        } else {
            // Flat-color canvas
            $this->canvas = $this->imagine->create(
                new Box($width, $height),
                $this->palette->color($backgroundColor, 100)
            );
        }

        return $this;
    }

    /**
     * Set output format: 'png', 'jpeg', or 'gif', or 'webp'.
     */
    public function setFormat(string $format): self {
        $fmt = strtolower($format);
        if (!in_array($fmt, ['png', 'jpeg', 'gif','webp'], true)) {
            throw new \InvalidArgumentException("Unsupported format: {$format}");
        }
        $this->format = $fmt;
        return $this;
    }

    /**
     * Add a Watermark element.
     */
    public function addWatermark(Watermark $wm): self {
        $wm->apply($this->canvas, $this->width, $this->height, $this->palette);
        return $this;
    }

    /**
     * Add a Text element.
     */
    public function addText(TextElement $te): self {
        $te->apply($this->canvas, $this->width, $this->height, $this->imagine, $this->palette);
        return $this;
    }

    /**
     * Render to browser or save to disk.
     */
    public function render(string $outputPath = null): self {
        $data = $this->canvas->get($this->format);
        if ($outputPath) {
            file_put_contents($outputPath, $data);
        } else {
            $mime = $this->format === 'jpeg' ? 'image/jpeg' : "image/{$this->format}";
            header("Content-Type: {$mime}");
            echo $data;
        }
        return $this;
    }
}
