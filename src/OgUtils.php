<?php

namespace s3ny4\OgImage;

class OgUtils
{

    /**
     * Calculates the position of an element within an image.
     *
     * This function determines the position of an element (e.g., text or background)
     * within an image based on the specified alignment or pixel value.
     *
     * @param string|int $position The position value ('center', 'left', 'right', 'top', 'bottom', or a specific pixel value).
     * @param int $elementSize The size of the element (width or height).
     * @param int $imageSize The size of the image (width or height).
     * @return int The calculated position in pixels.
     */
    public function calculatePosition($position, $elementSize, $imageSize)
    {
        if ($position === 'center') {
            return intval(($imageSize - $elementSize) / 2);
        } elseif ($position === 'left' || $position === 'top') {
            return 0;
        } elseif ($position === 'right' || $position === 'bottom') {
            return $imageSize - $elementSize;
        } else {
            return intval($position);
        }
    }

}