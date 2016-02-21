<?php

namespace SixtyNine\WordCloud\Builder\Context;

abstract class ColorChooser
{
    protected $palette;

    public function __construct($palette)
    {
        $this->palette = $palette;
    }

    public abstract function getNextColor();
}