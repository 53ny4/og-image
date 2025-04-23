
# OG Image Generator

---

Generate Open Graph (OG) images dynamically using PHP. 
This library allows you to programmatically create social media preview images with text, background, and watermarks using the Imagine image library.

## Example

---

![Example](docs/images/example.png)


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
    ->setFontPath('path/to/font.ttf')
    ->setFontSize(36)
    ->setFontColor('#ffffff')
    ->setBgOpacity(50)
    ->setBgColor('#ff0000');

$og->addText($text);

// Add watermark
$watermark = (new Watermark('path/to/logo.png'))
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


## 📁 File Structure

---

```
src/
├── OgImageGenerator.php
├── TextElement.php
├── Watermark.php
└── Traits/
    └── PositionableTrait.php
```

## 📝 License

---

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.