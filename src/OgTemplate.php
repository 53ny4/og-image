<?php

namespace s3ny4\OgImage;

use s3ny4\OgImage\templates\OgBlogTemplate;

class OgTemplate
{


    private $ogImage;
    public $template;


    public function __construct($template)
    {
        $this->template = $template;
        $this->ogImage = new OgImage();

        // Initialize the template
        $this->initializeTemplate();
    }

    /**
 * Initializes the template based on the provided template name.
 *
 * This function sets the template object based on the template name provided
 * during the construction of the OgTemplate object. If the template name is not
 * recognized, an InvalidArgumentException is thrown.
 *
 * @throws \InvalidArgumentException if the template name is not recognized.
 */
private function initializeTemplate()
{
    switch ($this->template) {
        case 'blog':
            $tpl = new OgBlogTemplate();
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
        return $this->template->prepare($image)->render();
    }


}
