<?php

namespace s3ny4\OgImage\UserTemplates;

use s3ny4\OgImage\OgBackground;
use s3ny4\OgImage\OgText;
use s3ny4\OgImage\OgWatermark;
use s3ny4\OgImage\Template\OgImageTemplateBase;



class EventTemplate extends OgImageTemplateBase
{

    public function background($color): EventTemplate
    {
        $background = new OgBackground($color);
        $background->addBorder('top', 10, 'fff000');
        $this->ogImage->setBackground($background);
        return $this;
    }

    public function title($text): EventTemplate
    {
        $titleText = new OgText();
        $titleText->setPosition('center', 'top');
        $titleText->setColor('000000');
        $titleText->setSize(50);
        $titleText->setPadding(20);
        $titleText->setText($text);
        $this->ogImage->addText($titleText);

        return $this;
    }

    public function date($date): EventTemplate
    {
        $dateText = new OgText();
        $dateText->setPosition('center', 'bottom');
        $dateText->setColor('000000');
        $dateText->setSize(30);
        $dateText->setPadding(20);
        $dateText->setText($date);
        $this->ogImage->addText($dateText);

        return $this;
    }


    public function logo($image,$size): EventTemplate
    {
        // watermark
        $watermark = new OgWatermark();
        $watermark->image($image);
        $watermark->setPosition('center', 'center');
        $watermark->setSize($size);
        $this->ogImage->addWatermark($watermark);

        return $this;
    }
}
