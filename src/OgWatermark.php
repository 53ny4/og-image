<?php

namespace s3ny4\OgImage;

use Imagine\Image\Box;
use Imagine\Image\Point;

class OgWatermark
{
    /**
     * @var string $imagePath The path to the image file.
     */
    private $imagePath;

    /**
     * @var array $position The position of the watermark.
     *                      'x' can be 'center', 'left', 'right', or a specific pixel value.
     *                      'y' can be 'center', 'top', 'bottom', or a specific pixel value.
     */
    private $position = ['x' => 'center', 'y' => 'center'];

    /**
     * @var int $size The size of the watermark in pixels.
     */
    private $size;

    /**
     * @var string $sizeDimension The dimension to resize ('width', 'height', or 'max').
     *                            Defaults to 'width'.
     */
    private $sizeDimension = 'width'; // Default dimension to resize

    /**
     * @var int $opacity The opacity of the watermark (0-100).
     */
    private $opacity = 100;

    public $utils;

    public function __construct()
    {
        $this->utils = new OgUtils();
    }

    /**
     * Sets the path to the image file.
     *
     * @param string $path The path to the image file.
     */
    public function image($path)
    {
        $this->imagePath = $path;
    }

    /**
     * Sets the position of the watermark.
     *
     * @param string|int $x The horizontal position ('center', 'left', 'right', or a specific pixel value).
     * @param string|int $y The vertical position ('center', 'top', 'bottom', or a specific pixel value).
     */
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

    /**
     * Applies the watermark to the given image.
     *
     * This function opens the watermark image, resizes it if necessary, calculates its position,
     * and pastes it onto the provided image with the specified opacity.
     *
     * @param \Imagine\Image\ImageInterface $image The image to which the watermark will be applied.
     * @param int $imageWidth The width of the image.
     * @param int $imageHeight The height of the image.
     * @param \Imagine\Image\ImagineInterface $imagine The Imagine instance used to open the watermark image.
     */
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

        $x = $this->utils->calculatePosition($this->position['x'], $watermarkSize->getWidth(), $imageWidth);
        $y = $this->utils->calculatePosition($this->position['y'], $watermarkSize->getHeight(), $imageHeight);

        $point = new Point($x, $y);

        $image->paste($watermark, $point, $this->opacity);
    }

    /**
     * Sets the opacity of the watermark.
     *
     * @param int $opacity The opacity value (0-100).
     */
    public function setOpacity(int $opacity): void
    {
        $this->opacity = $opacity;
    }
}