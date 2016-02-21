<?php

namespace SixtyNine\WordCloud\Builder\Context;

class RandomColorChooser extends ColorChooser
{
    public function getNextColor()
    {
        return $this->palette[rand(0, count($this->palette))];
    }
}