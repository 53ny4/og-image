<?php

namespace s3ny4\OgImage\templates;

use s3ny4\OgImage\OgBackground;
use s3ny4\OgImage\OgText;

/**
 * @property mixed $elements
 */
class OgBlogTemplate
{

    /**
     * Sets the title element for the blog template.
     *
     * This function assigns the provided title to the 'title' element
     * in the elements array.
     *
     * @param string $title The title to be set.
     */
    public function title($title): void
    {
        $this->elements['title'] = $title;
    }

    /**
     * Sets the description element for the blog template.
     *
     * This function assigns the provided description to the 'description' element
     * in the elements array.
     *
     * @param string $description The description to be set.
     */
    public function description($description): void
    {
        $this->elements['description'] = $description;
    }


    /**
     * Sets the background element for the blog template.
     *
     * This function assigns the provided background to the 'background' element
     * in the elements array.
     *
     * @param mixed $background The background to be set.
     */
    public function background($background): void
    {
        $this->elements['background'] = $background;
    }

    /**
     * Prepares the image by setting the background and adding text elements.
     *
     * This function sets a default background if none is provided, adds a border to the background,
     * and adds title and description text elements to the image.
     *
     * @param OgImage $image The image object to be prepared.
     * @return OgImage The prepared image object.
     */
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