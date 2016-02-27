<?php

namespace SixtyNine\WordCloud;

/**
 * List of already placed boxes used to search a free space for a new box.
 */
class Mask
{

    private $drawnBoxes = array();

    /**
     * Add a new box to the mask.
     * @param Box $box The new box to add
     */
    public function add(Box $box)
    {
        $this->drawnBoxes[] = $box;
    }

    public function getTable()
    {
        return $this->drawnBoxes;
    }

    /**
     * Test whether a box overlaps with the already drawn boxes.
     * @param Box $testBox The box to test
     * @return boolean True if the box overlaps with the already drawn boxes and false otherwise
     */
    public function overlaps(Box $testBox)
    {
        /** @var Box $box */
        foreach ($this->drawnBoxes as $box) {
            if ($box->intersects($testBox)) {
                return true;
            }
        }
        return false;
    }

    public function getEnclosingBox($margin = 10)
    {
        $left = null;
        $right = null;
        $top = null;
        $bottom = null;
        foreach ($this->drawnBoxes as $box) {
            if (($left == NULL) || ($box->left < $left)) $left = $box->left;
            if (($right == NULL) || ($box->right > $right)) $right = $box->right;
            if (($top == NULL) || ($box->top > $top)) $top = $box->top;
            if (($bottom == NULL) || ($box->bottom < $bottom)) $bottom = $box->bottom;
        }
        return array($left - $margin, $bottom - $margin, $right + $margin, $top + $margin);
    }

    public function adjust($dx, $dy)
    {
        foreach ($this->drawnBoxes as $box) {
            $box->left += $dx;
            $box->right += $dx;
            $box->top += $dy;
            $box->bottom += $dy;
        }
    }
}

