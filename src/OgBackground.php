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

    /**
     * @var array $borders The borders of the background.
     */
    private $borders = []; // Format: ['top' => ['size' => 10, 'color' => '#ff0000'], ...]

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

    private function normalizeColor($color)
    {
        return '#' . ltrim($color, '#');
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
            $image = $imagine->open($this->background); // Open the image
            $image = $this->resizeAndCrop($image, new Box($width, $height)); // Resize and crop the image
        }

        /**
         * Draw borders on the background image.
         */
        if (!empty($this->borders)) {
            $draw = $image->draw();
            $palette = new RGB();

            foreach ($this->borders as $side => $border) {
                $borderColor = $palette->color($this->normalizeColor($border['color']), 100);
                $size = $border['size'];

                switch ($side) {
                    case 'top':
                        $start = new Point(0, 0);
                        $end = new Point($width, $size);
                        break;
                    case 'bottom':
                        $start = new Point(0, $height - $size);
                        $end = new Point($width, $height);
                        break;
                    case 'left':
                        $start = new Point(0, 0);
                        $end = new Point($size, $height);
                        break;
                    case 'right':
                        $start = new Point($width - $size, 0);
                        $end = new Point($width, $height);
                        break;
                }

                // Draw filled rectangle for the border
                $draw->rectangle($start, $end, $borderColor, true);
            }
        }

        return $image;
    }

    /**
     * Sets a border on the specified side with given size and color.
     *
     * @param string $side The side to set the border on ('top', 'bottom', 'left', 'right')
     * @param int $size The size (thickness) of the border in pixels
     * @param string $color The color of the border (hex code)
     */
    public function addBorder($side, $size, $color)
    {
        $validSides = ['top', 'bottom', 'left', 'right'];
        if (in_array($side, $validSides)) {
            $this->borders[$side] = [
                'size' => $size,
                'color' => $color,
            ];
        } else {
            throw new \InvalidArgumentException("Invalid side '$side' for border. Valid sides are 'top', 'bottom', 'left', 'right'.");
        }
    }

    /**
     * Resizes and crops the given image to fit the target dimensions.
     *
     * This function first resizes the image to ensure it covers the target dimensions,
     * then crops the image to exactly match the target dimensions.
     *
     * @param ImageInterface $image The image to be resized and cropped.
     * @param Box $targetBox The target dimensions for the image.
     * @return ImageInterface The resized and cropped image.
     */
    private function resizeAndCrop(ImageInterface $image, Box $targetBox): ImageInterface
    {
        $size = $image->getSize();
        $ratio = max(
            $targetBox->getWidth() / $size->getWidth(),
            $targetBox->getHeight() / $size->getHeight()
        );
        $newSize = $size->scale($ratio);
        $image->resize($newSize);

        // Crop the image to the target size
        $cropX = max(0, ($newSize->getWidth() - $targetBox->getWidth()) / 2);
        $cropY = max(0, ($newSize->getHeight() - $targetBox->getHeight()) / 2);
        $image->crop(new Point($cropX, $cropY), $targetBox);

        return $image;
    }

}