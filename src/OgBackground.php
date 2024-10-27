<?php

namespace s3ny4\OgImage;

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;

class OgBackground
{
    private $background;
    private $isColor;

    public function __construct($background)
    {
        $this->background = $background;
        $this->isColor = $this->isColorCode($background);
    }

    private function isColorCode($string)
    {
        // if string contains #, remove it
        if (str_contains($string, '#')) {
            $string = str_replace('#', '', $string);
        }
        return ctype_xdigit($string);
    }

    public function createBackground($width, $height, $imagine)
    {

        if ($this->isColor) {
            $palette = new RGB();
            $color = $palette->color('#' . $this->background, 100);
            $image = $imagine->create(new Box($width, $height), $color);
        } else {
            $image = $imagine->open($this->background);
            $image->resize($this->getProportionalResizeBox($image->getSize(), new Box($width, $height)));
            $canvas = $imagine->create(new Box($width, $height));
            $canvas->paste($image, new Point(0, 0));
            $image = $canvas;
        }

        return $image;
    }

    private function getProportionalResizeBox($currentSize, $targetSize)
    {
        $ratio = min($targetSize->getWidth() / $currentSize->getWidth(), $targetSize->getHeight() / $currentSize->getHeight());
        return new Box(
            $targetSize->getWidth(),
            $targetSize->getHeight()
        );
    }

    private function resizeAndCrop(ImageInterface $image, Box $targetBox): ImageInterface
    {
        $size = $image->getSize();
        $scale = max($targetBox->getWidth() / $size->getWidth(), $targetBox->getHeight() / $size->getHeight());
        $resizedBox = $size->scale($scale);
        $image->resize($resizedBox);
        $x = ($resizedBox->getWidth() - $targetBox->getWidth()) / 2;
        $y = ($resizedBox->getHeight() - $targetBox->getHeight()) / 2;
        $image->crop(new Point($x, $y), $targetBox);
        return $image;
    }
}