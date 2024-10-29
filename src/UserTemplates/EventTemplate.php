<?php

namespace s3ny4\OgImage\UserTemplates;

use s3ny4\OgImage\OgBackground;
use s3ny4\OgImage\OgText;
use s3ny4\OgImage\Template\OgImageTemplateBase;

class EventTemplate extends OgImageTemplateBase
{
    protected function initializeTemplate()
    {
        // Set a background image or color
        $background = new OgBackground('#000000');
        $this->ogImage->setBackground($background);

        // Add event title
        $titleText = new OgText();
        $titleText->setPosition('center', 'top');
        $titleText->setColor('ffffff');
        $titleText->setSize(50);
        $titleText->setPadding(20);
        $this->elements['eventTitle'] = $titleText;

        // Add event date
        $dateText = new OgText();
        $dateText->setPosition('center', 'bottom');
        $dateText->setColor('ffffff');
        $dateText->setSize(30);
        $dateText->setPadding(20);
        $this->elements['eventDate'] = $dateText;
    }

    public function title($text)
    {
        if (isset($this->elements['eventTitle'])) {
            /** @var OgText $titleText */
            $titleText = $this->elements['eventTitle'];
            $titleText->setText($text);
        }
        return $this;
    }

    public function date($text)
    {
        if (isset($this->elements['eventDate'])) {
            /** @var OgText $dateText */
            $dateText = $this->elements['eventDate'];
            $dateText->setText($text);
        }
        return $this;
    }
}
