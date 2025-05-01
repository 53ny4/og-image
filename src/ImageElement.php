<?php
namespace s3ny4\OgImage;

use Imagine\Image\ImageInterface;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\Palette\RGB;
use s3ny4\OgImage\Traits\PositionableTrait;

class ImageElement {
    use PositionableTrait;

    private string $imagePath;
    private int $opacity = 100;  // 0-100
    private ?int $targetWidth = null;
    private ?int $targetHeight = null;

    private int $width;
    private int $height;

    /**
     * Set the source image file path.
     */
    public function setImagePath(string $path): self {
        if (!file_exists($path)) {
            throw new \RuntimeException("Image not found: {$path}");
        }
        $this->imagePath = $path;
        return $this;
    }

    /**
     * Set desired width and/or height for the image; preserves aspect ratio.
     */
    public function setSize(?int $width = null, ?int $height = null): self {
        $this->targetWidth = $width;
        $this->targetHeight = $height;
        return $this;
    }

    /**
     * Set overlay opacity (0 = transparent, 100 = opaque).
     */
    public function setOpacity(int $opacity): self {
        $this->opacity = max(0, min(100, $opacity));
        return $this;
    }

    /**
     * Apply this image element onto the canvas.
     */
    public function apply(ImageInterface $canvas, int $canvasW, int $canvasH, RGB $palette): void {
        $imagine = new Imagine();
        $img = $imagine->open($this->imagePath);
        $size = $img->getSize();
        $origW = $size->getWidth();
        $origH = $size->getHeight();

        // Resize logic preserving ratio
        if ($this->targetWidth !== null || $this->targetHeight !== null) {
            if ($this->targetWidth !== null && $this->targetHeight === null) {
                $newW = $this->targetWidth;
                $newH = (int) round($origH * ($newW / $origW));
            } elseif ($this->targetHeight !== null && $this->targetWidth === null) {
                $newH = $this->targetHeight;
                $newW = (int) round($origW * ($newH / $origH));
            } else {
                $newW = $this->targetWidth;
                $newH = (int) round($origH * ($newW / $origW));
            }
            $img = $img->resize(new Box($newW, $newH));
            $w = $newW;
            $h = $newH;
        } else {
            $w = $origW;
            $h = $origH;
        }

        // Compute position using trait
        $x = $this->computeX($canvasW, $w);
        $y = $this->computeY($canvasH, $h);

        $this->width  = $w;
        $this->height = $h;

        // Paste or blend based on opacity
        if ($this->opacity < 100) {
            $destRes = $canvas->getGdResource();
            $srcRes  = $img->getGdResource();
            imagecopymerge(
                $destRes,
                $srcRes,
                $x, $y,
                0, 0,
                $w, $h,
                $this->opacity
            );
        } else {
            $canvas->paste($img, new Point($x, $y));
        }
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }
}
