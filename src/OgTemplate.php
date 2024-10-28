<?php

namespace s3ny4\OgImage;

use s3ny4\OgImage\templates\OgBlogTemplate;

class OgTemplate
{
    private $ogImage;
    public $template;

    // Template-specific properties
    private $elements = [];

    public function __construct($template)
    {
        $this->template = $template;
        $this->ogImage = new OgImage();

        // Initialize the template
        $this->initializeTemplate();
    }

    private function initializeTemplate()
    {

        switch ($this->template) {
            case 'blog':
                $tpl =  new OgBlogTemplate();
                break;
            // Add more templates as needed
            default:
                throw new \InvalidArgumentException("Template '{$this->template}' not recognized.");
        }

        $this->template = $tpl;
    }

    public function render()
    {
        $image = new OgImage();
        return  $this->template->prepare($image)->render();


    }






}
