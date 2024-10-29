<?php

namespace s3ny4\OgImage\Template;

use s3ny4\OgImage\OgImage;
use s3ny4\OgImage\OgText;
use s3ny4\OgImage\OgWatermark;

abstract class OgImageTemplateBase
{
    protected $ogImage;
    protected $elements = [];

    public function __construct()
    {
        $this->ogImage = new OgImage();
        $this->initializeTemplate();
    }

    /**
     * Users will implement this method to set up their template.
     */
    abstract protected function initializeTemplate();

    /**
     * Renders the image.
     *
     * @param string|null $outputPath The path to save the image. If null, outputs directly.
     */
    public function render($outputPath = null)
    {
        // Add elements to ogImage
        foreach ($this->elements as $element) {
            if ($element instanceof OgText) {
                $this->ogImage->addText($element);
            } elseif ($element instanceof OgWatermark) {
                $this->ogImage->addWatermark($element);
            }
        }

        // Render the image
        $this->ogImage->render($outputPath);
    }
}
