<?php

namespace Dreamcraft\WordCloud\Builder\Context;

class RotatorColorChooser extends ColorChooser
{
    protected $current = 0;

    public function getNextColor()
    {
        $color = $this->palette[$this->current % count($this->palette)];
        $this->current++;
        return $color;
    }
}