<?php

namespace s3ny4\OgImage\Template;

use s3ny4\OgImage\OgImage;

abstract class OgImageTemplateBase
{
    protected $ogImage;


    public function __construct()
    {
        $this->ogImage = new OgImage();

    }

    /**
     * Renders the image.
     *
     * @param string|null $outputPath The path to save the image. If null, outputs directly.
     */
    public function render($outputPath = null)
    {
        // Render the image
        $this->ogImage->render($outputPath);
    }
}
