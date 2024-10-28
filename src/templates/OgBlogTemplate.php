<?php

namespace s3ny4\OgImage\templates;

use s3ny4\OgImage\OgBackground;
use s3ny4\OgImage\OgText;

/**
 * @property mixed $elements
 */
class OgBlogTemplate
{

    public function __construct()
    {

        return 'blog';

    }

    public function title($title): void
    {
        $this->elements['title'] = $title;
    }

    public function description($description): void
    {
        $this->elements['description'] = $description;
    }

    // background
    public function background($background): void
    {
        $this->elements['background'] = $background;
    }

    public function prepare($image)
    {

        // if background is not set, set default background
        if (!isset($this->elements['background'])) {
            $this->elements['background'] = '#ffffff';
        }
        // Set default background
        $background = new OgBackground($this->elements['background']);
        $background->addBorder('bottom', 20, '#3273a8');
        $image->setBackground($background);


        // Title text placeholder
        $titleText = new OgText();
        $titleText->setPosition('center', 'center');
        $titleText->setColor('000000');
        $titleText->setSize(50);
        $titleText->setPadding(20);
        $titleText->setText($this->elements['title']);
        $image->addText($titleText);

        if (isset($this->elements['description'])) {
            // Description text placeholder
            $descriptionText = new OgText();
            $descriptionText->setPosition('center', 380);
            $descriptionText->setColor('ffffff');
            $descriptionText->setSize(25);
            $descriptionText->setPadding(20);

            $descriptionText->setText($this->elements['description']);
            $descriptionText->setBackground('#3273a8', 80);

            $image->addText($descriptionText);
        }

        return $image;
    }

}