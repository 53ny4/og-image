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
     * @param int $vertical   px shift vertically (down for 'top', up for 'bottom', or offset from center)
     * @param int $horizontal px shift horizontally (right for 'left', left for 'right', or offset from center)
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
        if (is_numeric($this->horizontal)) {
            $x = (int)$this->horizontal + $this->marginH;
        } else {
            switch ($this->horizontal) {
                case 'left':
                    $x = $this->marginH;
                    break;
                case 'center':
                    $x = (int)(($containerW - $contentW) / 2) + $this->marginH;
                    break;
                case 'right':
                    $x = ($containerW - $contentW) - $this->marginH;
                    break;
                default:
                    $x = $this->marginH;
            }
        }
        return max(0, min($x, $containerW - $contentW));
    }

    /**
     * Compute Y coordinate based on position and margin
     */
    protected function computeY(int $containerH, int $contentH): int {
        if (is_numeric($this->vertical)) {
            $y = (int)$this->vertical + $this->marginV;
        } else {
            switch ($this->vertical) {
                case 'top':
                    $y = $this->marginV;
                    break;
                case 'center':
                    $y = (int)(($containerH - $contentH) / 2) + $this->marginV;
                    break;
                case 'bottom':
                    $y = ($containerH - $contentH) - $this->marginV;
                    break;
                default:
                    $y = $this->marginV;
            }
        }
        return max(0, min($y, $containerH - $contentH));
    }
}
