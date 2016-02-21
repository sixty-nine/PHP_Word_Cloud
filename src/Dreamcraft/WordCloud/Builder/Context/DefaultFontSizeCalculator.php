<?php

namespace Dreamcraft\WordCloud\Builder\Context;

class DefaultFontSizeCalculator implements FontSizeCalculatorInterface
{
    protected $min_font_size;

    protected $max_font_size;

    protected $slope;

    protected $yintercept;

    public function __construct($min_font_size, $max_font_size, $min_count, $max_count)
    {
        $min_count = 1;
        $diffcount = ($max_count - $min_count) != 0 ? ($max_count - $min_count) : 1;
        $diffsize = ($max_font_size - $min_font_size) != 0 ? ($max_font_size - $min_font_size) : 1;
        $this->slope = $diffsize / $diffcount;
        $this->yintercept = $max_font_size - ($this->slope * $max_count);
        
        $this->min_font_size = $min_font_size;
        $this->max_font_size = $max_font_size;
    }
    
    public function calculateFontSize($word, $occurrences)
    {
        $font_size = (integer)($this->slope * $occurrences + $this->yintercept);

        if ($font_size < $this->min_font_size) {
            $font_size = $this->min_font_size;
        } elseif ($font_size > $this->max_font_size) {
            $font_size = $this->max_font_size;
        }
        
        return $font_size;
    }
}