<?php

namespace s3ny4\OgImage;

use Imagine\Image\Box;
use Imagine\Image\Point;

class OgWatermark
{
    private $imagePath;
    private $position = ['x' => 'center', 'y' => 'center'];
    private $size;
    private $sizeDimension = 'width'; // Default dimension to resize

    private $opacity = 100;

    public function image($path)
    {
        $this->imagePath = $path;
    }

    public function setPosition($x, $y)
    {
        $this->position = ['x' => $x, 'y' => $y];
    }

    /**
     * Set the size of the watermark.
     *
     * @param int $size The size in pixels.
     * @param string $dimension 'width', 'height', or 'max'. Defaults to 'width'.
     */
    public function setSize($size, $dimension = 'width')
    {
        $this->size = $size;
        $this->sizeDimension = $dimension;
    }

    public function applyToImage($image, $imageWidth, $imageHeight, $imagine)
    {
        $watermark = $imagine->open($this->imagePath);

        if ($this->size) {
            $watermarkSize = $watermark->getSize();
            $originalWidth = $watermarkSize->getWidth();
            $originalHeight = $watermarkSize->getHeight();
            $ratio = $originalWidth / $originalHeight;

            if ($this->sizeDimension === 'width') {
                $newWidth = $this->size;
                $newHeight = intval($newWidth / $ratio);
            } elseif ($this->sizeDimension === 'height') {
                $newHeight = $this->size;
                $newWidth = intval($newHeight * $ratio);
            } elseif ($this->sizeDimension === 'max') {
                if ($originalWidth > $originalHeight) {
                    $newWidth = $this->size;
                    $newHeight = intval($newWidth / $ratio);
                } else {
                    $newHeight = $this->size;
                    $newWidth = intval($newHeight * $ratio);
                }
            }

            $watermark->resize(new Box($newWidth, $newHeight));
        }

        $watermarkSize = $watermark->getSize();

        $x = $this->calculatePosition($this->position['x'], $watermarkSize->getWidth(), $imageWidth);
        $y = $this->calculatePosition($this->position['y'], $watermarkSize->getHeight(), $imageHeight);

        $point = new Point($x, $y);

        $image->paste($watermark, $point, $this->opacity);
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
            return (int)$position;
        }
    }

    public function setOpacity(int $opacity): void
    {
        $this->opacity = $opacity;
    }
}