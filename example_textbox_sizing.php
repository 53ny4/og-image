<?php
require_once 'vendor/autoload.php';

use s3ny4\OgImage\OgImageGenerator;
use s3ny4\OgImage\TextElement;

// Create a 1000px wide image
$og = new OgImageGenerator();
$og->createImage(1000, 630, '#f8f9fa');

// Create text element with 800px text box width
// This will give 100px margins on each side when centered
$text = (new TextElement())
    ->setText('This is a demonstration of the text box sizing feature. The image is 1000px wide, but this text is constrained to an 800px wide text box, giving us 100px margins on each side when centered.')
    ->setFontPath('src/assets/fonts/font.ttf') // You'll need to provide a valid font path
    ->setFontSize(28)
    ->setFontColor('#333333')
    ->setPosition('center', 'center')
    ->setTextBoxWidth(800)  // Set text box to 800px
    ->setBgColor('#ffffff')
    ->setBgOpacity(90)
    ->setPadding(20, 20);

$og->addText($text);

// Save the example image
$og->render('textbox_sizing_example.png');

echo "Example image generated: textbox_sizing_example.png\n";
echo "Image width: 1000px\n";
echo "Text box width: 800px\n";
echo "Calculated margins: 100px on each side\n";