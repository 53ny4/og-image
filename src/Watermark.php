<?php
namespace s3ny4\OgImage;

use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\RGB;
use Imagine\Gd\Imagine as GdImagine;
use s3ny4\OgImage\Traits\PositionableTrait;

class Watermark {
    use PositionableTrait;

    private string $watermarkPath;
    private int $opacity = 100;
    private ?int $targetWidth = null;
    private ?int $targetHeight = null;

    public function setWatermarkPath(string $path): self {
        $this->watermarkPath = $path;
        return $this;
    }

    public function setOpacity(int $opacity): self {
        $this->opacity = max(0, min(100, $opacity));
        return $this;
    }

    public function setSize(?int $width = null, ?int $height = null): self {
        $this->targetWidth = $width;
        $this->targetHeight = $height;
        return $this;
    }

    public function apply(ImageInterface $canvas, int $canvasW, int $canvasH, RGB $palette): void {
        $imagine = new GdImagine();
        $watermark = $imagine->open($this->watermarkPath);
        $origSize = $watermark->getSize();
        $origW = $origSize->getWidth();
        $origH = $origSize->getHeight();

        // Resize preserving ratio
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
            $watermark = $watermark->resize(new Box($newW, $newH));
            $wmW = $newW;
            $wmH = $newH;
        } else {
            $wmW = $origW;
            $wmH = $origH;
        }

        // Compute positions using trait
        $x = $this->computeX($canvasW, $wmW);
        $y = $this->computeY($canvasH, $wmH);

        // Paste or blend
        if ($this->opacity < 100) {
            $destRes = $canvas->getGdResource();
            $srcRes = $watermark->getGdResource();
            imagecopymerge($destRes, $srcRes, $x, $y, 0, 0, $wmW, $wmH, $this->opacity);
        } else {
            $canvas->paste($watermark, new Point($x, $y));
        }
    }
}
