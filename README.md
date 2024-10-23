# OgImage v0.1.0

`OgImage` is a simple PHP class that generates Open Graph (OG) images dynamically using the Imagine library for image manipulation. It allows for customizing background colors or images, adding overlay text with adjustable styles, and setting various image dimensions.

## Features

- **Customizable background**: Set a solid color or provide a background image. Image will be resized to fit the OG area.
- **Overlay text**: Add text with adjustable font size, color, and background color.
- **Automatic font size adjustment**: Dynamically resizes text to fit within the text area.
- **Flexible image size**: Customize the image width and height to suit your needs.


## Example image
![Example Image](docs/images/example.png)

## Installation

Install via Composer:

```bash
composer require s3ny4/ogimage
```

Ensure you have the [Imagine](https://github.com/php-imagine/Imagine) library installed, as it's required for image manipulation:

```bash
composer require imagine/imagine
```

## Usage

Here's a simple example of how to use the `OgImage` class to generate an image and show it in the browser:

```php
use s3ny4\OgImage\OgImage;

require_once __DIR__ . '/vendor/autoload.php';

$ogImage = new OgImage();
$ogImage->setText('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris sed fringilla leo.')
    ->setTextColor('#FFFFFF')
    ->setTextBackgroundColor('#000000')
    ->setBackground('', __DIR__ . '/src/assets/images/img.png')
    ->generate()
    ->show('png');
```


### Methods

- `setText(string $text)`: Sets the text to be overlaid on the image.
- `setTextColor(string $textColor)`: Sets the color of the overlay text.
- `setTextBackgroundColor(string $textBackgroundColor)`: Sets the background color behind the text.
- `setBackground(string $color = '', string $image = '')`: Sets the background of the image, either as a color or from an image path.
- `setWidth(int $width)`: Sets the width of the image.
- `setHeight(int $height)`: Sets the height of the image.
- `generate()`: Generates the final image and returns it as an `ImageInterface` then it can be saved `->save()` or shown `->show()`.
- `setFontPath(string $fontPath)`: Sets the font path for the text overlay.

## License

This project is licensed under the MIT License. See the [LICENSE](https://opensource.org/license/mit) file for more details.

## Dependencies

- [Imagine Library](https://github.com/avalanche123/Imagine)

## Contributing

Contributions are welcome! Feel free to submit a pull request or open an issue for any bug fixes or improvements.