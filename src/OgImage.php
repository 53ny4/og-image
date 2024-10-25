<?php

namespace s3ny4\OgImage;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\FontInterface;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;

class OgImage
{
    protected Imagine $imagine;
    protected int $width = 1200;
    protected int $height = 630;
    protected string $background = '#ffffff';
    protected string $imageBackgroundPath = '';
    protected string $text = 'Hello, World!';
    protected string $textColor = '#000000';
    protected string $textBackgroundColor = '';
    protected string $fontPath = __DIR__ . '/assets/fonts/BebasNeue-Regular.ttf';
    protected int $fontSize = 48;
    protected array $textPosition = ['x' => 0, 'y' => 0];


    protected array $watermark = [
        'path' => '',
        'position' => ['x' => 0, 'y' => 0],
        'opacity' => 100
    ];


    /**
     * The constructor initializes an instance of the Imagine class.
     */
    public function __construct()
    {
        $this->imagine = new Imagine();
    }


    /**
     * @param string $text
     * @return $this
     */
    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @param string $textColor
     * @return $this
     */
    public function setTextColor(string $textColor): self
    {
        $this->textColor = $textColor;
        return $this;
    }


    /**
     * @param string $color
     * @param string $image
     * @return $this
     */
    public function setBackground(string $color = '', string $image = ''): self
    {
        $this->background = $color;
        $this->imageBackgroundPath = $image;
        return $this;
    }

    public function setTextBackgroundColor(string $textBackgroundColor): OgImage
    {
        $this->textBackgroundColor = $textBackgroundColor;
        return $this;
    }

    public function setWidth(int $width): OgImage
    {
        $this->width = $width;
        return $this;
    }

    public function setHeight(int $height): OgImage
    {
        $this->height = $height;
        return $this;
    }

    public function setFontPath(string $fontPath): OgImage
    {
        $this->fontPath = $fontPath;
        return $this;
    }

    public function setFontSize(int $fontSize): OgImage
    {
        $this->fontSize = $fontSize;
        return $this;
    }

    public function setTextPosition(array $textPosition): OgImage
    {
        $this->textPosition = $textPosition;
        return $this;
    }


    /**
     * This method draws a text overlay box onto the image. It calculates the available space based on the padding values and attempts to fit the text inside.
     * It dynamically adjusts the font size if the text exceeds the allowed area.
     * The text is drawn centered horizontally.
     *
     * @param ImageInterface $image
     * @param string $text
     * @param int $overlayHeight
     * @param int $overlayY
     * @param int $paddingLeft
     * @param int $paddingRight
     * @param int $paddingTop
     * @param int $paddingBottom
     * @return void
     */
    private function addOverlayText(
        ImageInterface $image,
        string         $text,
        int            $overlayHeight,
        int            $overlayY,
        int            $paddingLeft = 10,
        int            $paddingRight = 10,
        int            $paddingTop = 10,
        int            $paddingBottom = 10
    ): void
    {
        $overlayWidth = $image->getSize()->getWidth();
        $palette = new RGB();
        if (!empty($this->textBackgroundColor)) {
            $backgroundColor = $palette->color($this->textBackgroundColor, 60);
            $image->draw()->rectangle(new Point(0 + $this->textPosition['x'], $overlayY + $this->textPosition['y']), new Point($overlayWidth + $this->textPosition['x'], $overlayY + $overlayHeight + $this->textPosition['y']), $backgroundColor, true);
        }


        $font = $this->imagine->font($this->fontPath, $this->fontSize, $palette->color($this->textColor));
        $maxTextWidth = $overlayWidth - $paddingLeft - $paddingRight;
        $maxTextHeight = $overlayHeight - $paddingTop - $paddingBottom;

        do {
            $lines = $this->wrapTextToOverlay($font, $text, $maxTextWidth);
            $lineHeight = $this->fontSize * 1.2;
            $totalTextHeight = count($lines) * $lineHeight;
            if ($totalTextHeight <= $maxTextHeight) break;
            $this->fontSize -= 2;
            $font = $this->imagine->font($this->fontPath, $this->fontSize, $palette->color($this->textColor));
        } while ($this->fontSize >= 10);

        $textY = $overlayY + ($overlayHeight - $totalTextHeight) / 2;
        foreach ($lines as $line) {
            $lineX = $overlayWidth / 2 - $font->box($line)->getWidth() / 2;
            $image->draw()->text($line, $font, new Point($lineX + $this->textPosition['x'], $textY + $this->textPosition['y']));
            $textY += $lineHeight;
        }
    }

private function addWatermark(ImageInterface $image): void
{
    // Open the watermark image and get its size
    $watermark = $this->imagine->open($this->watermark['path']);
    $watermarkSize = $watermark->getSize();

    // Calculate the scaling factor to resize the watermark to 150px width
    $scale = 150 / $watermarkSize->getWidth();

    // Resize the watermark proportionally
    $newWatermarkSize = $watermarkSize->scale($scale);
    $watermark->resize($newWatermarkSize);

    // Calculate the position to place the watermark in the bottom left corner
    $watermarkX = $this->watermark['position']['x'];
    $watermarkY = $this->watermark['position']['y'];

    // Paste the watermark onto the image
    $image->paste($watermark, new Point($watermarkX, $watermarkY), $this->watermark['opacity']);
}


    /**
     * Helper method breaks the input text into multiple lines, ensuring that each line fits within the maximum width.
     * It adds words to the current line until the line's width exceeds the maximum width, then starts a new line.
     *
     * @param FontInterface $font
     * @param string $text
     * @param float $maxWidth
     * @return array
     */
    private function wrapTextToOverlay(FontInterface $font, string $text, float $maxWidth): array
    {
        $words = explode(' ', $text);
        $lines = [];
        $currentLine = '';

        foreach ($words as $word) {
            $lineWithWord = $currentLine ? $currentLine . ' ' . $word : $word;
            if ($font->box($lineWithWord)->getWidth() <= $maxWidth) {
                $currentLine = $lineWithWord;
            } else {
                if ($currentLine) $lines[] = $currentLine;
                $currentLine = $word;
            }
        }

        if ($currentLine) $lines[] = $currentLine;
        return $lines;
    }

    /**
     * Method generates a background image. If an image is provided (imageBackgroundPath),
     * it resizes and crops the image to fit the target dimensions (resizeAndCrop() method).
     * Otherwise, a plain color background is created using the background property.
     *
     * @return ImageInterface
     */
    private function createBackground(): ImageInterface
    {
        $imageBox = new Box($this->width, $this->height);
        if ($this->imageBackgroundPath) {
            $image = $this->imagine->open($this->imageBackgroundPath);
            return $this->resizeAndCrop($image, $imageBox);
        }
        $palette = new RGB();
        return $this->imagine->create($imageBox, $palette->color($this->background, 100));
    }

    /**
     * Method resizes an image proportionally to fit the target box size (targetBox), then crops it if necessary to ensure it matches the exact dimensions.
     *
     * @param ImageInterface $image
     * @param Box $targetBox
     * @return ImageInterface
     */
    private function resizeAndCrop(ImageInterface $image, Box $targetBox): ImageInterface
    {
        $size = $image->getSize();
        $scale = max($targetBox->getWidth() / $size->getWidth(), $targetBox->getHeight() / $size->getHeight());
        $resizedBox = $size->scale($scale);
        $image->resize($resizedBox);
        $x = ($resizedBox->getWidth() - $targetBox->getWidth()) / 2;
        $y = ($resizedBox->getHeight() - $targetBox->getHeight()) / 2;
        $image->crop(new Point($x, $y), $targetBox);
        return $image;
    }

    /**
     * Method creates the background image, adds overlay text to the image, and returns the final image. The text is placed 250 pixels from the bottom of the image.
     *
     * @return ImageInterface
     */
    public function generate(): ImageInterface
    {
        $image = $this->createBackground();

        if(!empty($this->watermark['path'])) {
            $this->addWatermark($image);
        }

        $overlayY = $image->getSize()->getHeight();
        $this->addOverlayText($image, $this->text, 150, $overlayY, 20, 20, 15, 15);
        return $image;
    }

    public function setWatermark(array $watermark): OgImage
    {
        $this->watermark = $watermark;
        return $this;
    }


}