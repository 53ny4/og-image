<?php

namespace s3ny4\OgImage;

use Imagine\Gd\Imagine;

class OgImage
{
    private $background;
    private $texts = [];
    private $watermarks = [];
    private $imagine;
    private $image;

    public function __construct($width = 1200, $height = 630)
    {
        $this->imagine = new Imagine();
        $this->width = $width;
        $this->height = $height;
    }

    public function setBackground(OgBackground $background)
    {
        $this->background = $background;
    }

    public function addText(OgText $text)
    {
        $this->texts[] = $text;
    }

    public function addWatermark(OgWatermark $watermark)
    {
        $this->watermarks[] = $watermark;
    }

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