<?php

namespace s3ny4\OgImage;

use Imagine\Gd\Imagine;

class OgImage
{
    /**
     * @var OgBackground The background of the image.
     */
    private $background;

    /**
     * @var array An array of OgText objects to be added to the image.
     */
    private $texts = [];

    /**
     * @var array An array of OgWatermark objects to be added to the image.
     */
    private $watermarks = [];

    /**
     * @var Imagine The Imagine instance used for image manipulation.
     */
    private $imagine;

    /**
     * @var \Imagine\Image\ImageInterface The image being created.
     */
    private $image;

    /**
     * Constructor for the OgImage class.
     *
     * @param int $width The width of the image.
     * @param int $height The height of the image.
     */
    public function __construct($width = 1200, $height = 630)
    {
        $this->imagine = new Imagine();
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Sets the background of the image.
     *
     * @param OgBackground $background The background object.
     */
    public function setBackground(OgBackground $background)
    {
        $this->background = $background;
    }

    /**
     * Adds a text object to the image.
     *
     * @param OgText $text The text object to add.
     */
    public function addText(OgText $text)
    {
        $this->texts[] = $text;
    }

    /**
     * Adds a watermark object to the image.
     *
     * @param OgWatermark $watermark The watermark object to add.
     */
    public function addWatermark(OgWatermark $watermark)
    {
        $this->watermarks[] = $watermark;
    }

    /**
     * Renders the image with the background, texts, and watermarks.
     *
     * @param string|null $outputPath The path to save the output image. If null, the image is output directly.
     */
    public function render($outputPath = null)
    {
        // Create base image
        $this->image = $this->background->createBackground($this->width, $this->height, $this->imagine);

        // Add texts
        foreach ($this->texts as $text) {
            $text->applyToImage($this->image, $this->width, $this->height);
        }

        // Add watermarks
        foreach ($this->watermarks as $watermark) {
            $watermark->applyToImage($this->image, $this->width, $this->height, $this->imagine);
        }

        // Output the image
        if ($outputPath) {
            $this->image->save($outputPath);
        } else {
            header('Content-Type: image/png');
            echo $this->image->get('png');
        }
    }
}