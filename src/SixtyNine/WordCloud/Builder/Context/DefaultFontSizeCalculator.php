<?php

namespace SixtyNine\WordCloud\Builder\Context;

class DefaultFontSizeCalculator implements FontSizeCalculatorInterface
{
    protected $minFontSize;

    protected $maxFontSize;

    protected $slope;

    protected $yintercept;

    public function __construct($minFontSize, $maxFontSize, $minCount, $maxCount)
    {
        $minCount = 1;
        $diffcount = ($maxCount - $minCount) != 0 ? ($maxCount - $minCount) : 1;
        $diffsize = ($maxFontSize - $minFontSize) != 0 ? ($maxFontSize - $minFontSize) : 1;
        $this->slope = $diffsize / $diffcount;
        $this->yintercept = $maxFontSize - ($this->slope * $maxCount);
        
        $this->minFontSize = $minFontSize;
        $this->maxFontSize = $maxFontSize;
    }
    
    public function calculateFontSize($word, $occurrences)
    {
        $font_size = (integer)($this->slope * $occurrences + $this->yintercept);

        if ($font_size < $this->minFontSize) {
            $font_size = $this->minFontSize;
        } elseif ($font_size > $this->maxFontSize) {
            $font_size = $this->maxFontSize;
        }
        
        return $font_size;
    }
}