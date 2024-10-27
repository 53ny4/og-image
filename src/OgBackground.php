<?php

namespace s3ny4\OgImage;

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;

class OgBackground
{
    /**
     * @var string $background
     */
    private $background;

    /**
     * @var bool $isColor
     */
    private $isColor;

    public function __construct($background)
    {
        $this->background = $background;
        $this->isColor = $this->isColorCode($background);
    }

    /**
     * Checks if the given string is a valid hexadecimal color code.
     *
     * This function first removes the '#' character if it is present in the string.
     * Then, it checks if the remaining string consists of valid hexadecimal digits.
     *
     * @param string $string The string to check.
     * @return bool True if the string is a valid hexadecimal color code, false otherwise.
     */
    private function isColorCode($string): bool
    {
        // if string contains #, remove it
        if (str_contains($string, '#')) {
            $string = str_replace('#', '', $string);
        }
        return ctype_xdigit($string);
    }

    /**
     * Creates a background image with the specified dimensions.
     *
     * This function creates a background image based on the provided width and height.
     * If the background is a color, it creates a solid color image.
     * If the background is an image URL, it resizes and pastes the image onto a canvas.
     *
     * @param int $width The width of the background image.
     * @param int $height The height of the background image.
     * @param ImagineInterface $imagine The Imagine instance used to create and manipulate images.
     * @return ImageInterface The created background image.
     */
    public function createBackground($width, $height, $imagine): ImageInterface
    {
        /**
         * If the background is a color, create a solid color image.
         */
        if ($this->isColor) {
            $palette = new RGB();
            $color = $palette->color('#' . $this->background, 100);
            $image = $imagine->create(new Box($width, $height), $color);

            /**
             * If the background is an image, resize and paste the image onto a canvas.
             */
        } else {
            $image = $imagine->open($this->background);
            $image->resize($this->getProportionalResizeBox($image->getSize(), new Box($width, $height))); // @todo: fix
            $canvas = $imagine->create(new Box($width, $height));
            $canvas->paste($image, new Point(0, 0));
            $image = $canvas;
        }

        return $image;
    }


    /**
     * Calculates the proportional resize box for an image.
     *
     * This function determines the proportional resize box based on the current size
     * and the target size of the image. It maintains the aspect ratio of the image.
     *
     * @TODO: test thoroughly
     *
     * @param \Imagine\Image\Box $currentSize The current size of the image.
     * @param \Imagine\Image\Box $targetSize The target size for the image.
     * @return \Imagine\Image\Box The calculated proportional resize box.
     */
    private function getProportionalResizeBox($currentSize, $targetSize): Box
    {
        return new Box(
            $targetSize->getWidth(),
            $targetSize->getHeight()
        );
    }

}