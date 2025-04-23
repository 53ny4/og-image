<?php
namespace s3ny4\OgImage\Traits;

trait PositionableTrait {
    private mixed $vertical = 'center';
    private mixed $horizontal = 'center';
    private int $marginV = 0;
    private int $marginH = 0;
    private int $paddingV = 10;
    private int $paddingH = 0;

    /**
     * @param mixed $vertical   'top'|'center'|'bottom' or px-int
     * @param mixed $horizontal 'left'|'center'|'right' or px-int
     */
    public function setPosition($vertical, $horizontal): self {
        $this->vertical = $vertical;
        $this->horizontal = $horizontal;
        return $this;
    }

    /**
     * @param int $vertical   px shift down from calculated position
     * @param int $horizontal px shift right from calculated position
     */
    public function setMargin(int $vertical, int $horizontal = 0): self {
        $this->marginV = $vertical;
        $this->marginH = $horizontal;
        return $this;
    }

    /**
     * @param int $vertical   px padding above and below content
     * @param int $horizontal px padding left and right of content
     */
    public function setPadding(int $vertical, int $horizontal = 0): self {
        $this->paddingV = $vertical;
        $this->paddingH = $horizontal;
        return $this;
    }

    /**
     * Compute X coordinate based on position and margin
     */
    protected function computeX(int $containerW, int $contentW): int {
        switch ($this->horizontal) {
            case 'left':   $x = 0; break;
            case 'center': $x = (int)(($containerW - $contentW)/2); break;
            case 'right':  $x = $containerW - $contentW; break;
            default:       $x = (int)$this->horizontal;
        }
        return max(0, min($x + $this->marginH, $containerW - $contentW));
    }

    /**
     * Compute Y coordinate based on position and margin
     */
    protected function computeY(int $containerH, int $contentH): int {
        switch ($this->vertical) {
            case 'top':    $y = 0; break;
            case 'center': $y = (int)(($containerH - $contentH)/2); break;
            case 'bottom': $y = $containerH - $contentH; break;
            default:       $y = (int)$this->vertical;
        }
        return max(0, min($y + $this->marginV, $containerH - $contentH));
    }
}
