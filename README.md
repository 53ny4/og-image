
# OG Image Generator

Table of Contents
=================
- [OG Image Generator](#og-image-generator)
  - [Example](#example)
  - [📦 Installation](#-installation)
  - [⚙️ Requirements](#️-requirements)
  - [🚀 Features](#-features)
  - [🧱 Usage](#-usage)
  - [📝 License](#-license)


---

A simple and flexible PHP library for generating Open Graph (OG) (but really any type of) images with customizable
backgrounds, text, and watermarks.
Perfect for creating dynamic social media preview images for your website or application.

This project was inspired by my clients, each of them wanted to have a custom OG image for their website.
One wanted to have a simple image with a quote, another wanted to have an avatar of the user displayed and so on.
While there are projects like that and don't get me wrong, they are great, but they are not flexible enough for my
needs.

On the other hand, Imagine/Imagine is a great library, but it has a bit of code to write to get the desired result.

## Example

---

![Example](docs/images/example.png)

### Code for the above image:

```php
$og = new OgImageGenerator();
$og->setBackgroundImage('src/assets/images/bg.png');
$og->createImage();

$text = (new TextElement())
    ->setText('OgImage')
    ->setFontSize(75)
    ->setFontColor('#ffffff')
    ->setFontPath('src/assets/fonts/BebasNeue-Regular.ttf')
    ->setBgOpacity(50)
    ->setMargin(50)
    ->setPosition('top', 'center');

$text2 = (new TextElement())
    ->setText('Hello World!')
    ->setFontSize(50)
    ->setFontColor('#ffffff')
    ->setFontPath('src/assets/fonts/BebasNeue-Regular.ttf')
    ->setBgColor('#000000')
    ->setBgOpacity(50)
    ->setPosition(470, 'center');

$text3 = (new TextElement())
    ->setText('Lorem ipsum dolor sit amet, consectetur adipiscing elit. ')
    ->setFontSize(25)
    ->setFontColor('#ffffff')
    ->setFontPath('src/assets/fonts/BebasNeue-Regular.ttf')
    ->setBgOpacity(50)
    ->setPosition(539, 'center');



$watermark = (new Watermark())
    ->setWatermarkPath('src/assets/images/watermark.png')
    ->setSize(100)
    ->setMargin(10)
    ->setPosition('bottom', 'right');


$og->addWatermark($watermark);
$og->addText($text);
$og->addText($text2);
$og->addText($text3);

$og->render();
```

## 📦 Installation

---

```bash
composer require 53ny4/og-image
```

## ⚙️ Requirements

---

- PHP ^8.2
- `imagine/imagine` (automatically installed)
-  supports only `GD` driver

## 🚀 Features

---

- Add styled text to your image (font, size, color, opacity, alignment)
- Add background overlays for text
- Automatic text wrapping and line height adjustment
- Place elements using top/center/bottom and left/center/right positions
- Add watermarks with position and opacity options
- Fit and scale background images to fill the canvas

## 🧱 Usage

---

```php
require 'vendor/autoload.php';

use s3ny4\OgImage\OgImageGenerator;
use s3ny4\OgImage\TextElement;
use s3ny4\OgImage\Watermark;

// Create generator instance
$og = new OgImageGenerator(1200, 630);

// Add background image
$og->setBackgroundImage('path/to/background.jpg');

// Add text
$text = (new TextElement())
    ->setText('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam accumsan sed lacus venenatis eleifend. Aenean ipsum turpis, faucibus nec maximus sit amet, lobortis vel nisl. ')
    ->setPosition('center', 'center')
    ->setFontPath('src/assets/fonts/font.ttf')
    ->setFontSize(36)
    ->setFontColor('#ffffff')
    ->setBgOpacity(50)
    ->setBgColor('#ff0000');

$og->addText($text);

// Add watermark
$watermark = (new Watermark('src/assets/images/logo.png'))
    ->setPosition('top', 'center')
    ->setMargin(50)
    ->setSize(150)
    ->setOpacity(50);
$og->addWatermark($watermark);

// output image to the browser
$og->render();

// or save to file
// $og->render('og-image.png');
```


## 📝 License

---

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.