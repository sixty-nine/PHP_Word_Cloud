<?php

namespace SixtyNine\WordCloud\Builder\Context;

class BuilderContext
{
    protected $fontSizeCalculator;

    protected $colorChooser;

    protected $wordUsher;

    public function __construct(
        FontSizeCalculatorInterface $fontSizeCalculator = null,
        ColorChooser $colorChooser = null,
        WordUsher $wordUsher = null
    )
    {
        $this->fontSizeCalculator = $fontSizeCalculator;
        $this->colorChooser = $colorChooser;
        $this->wordUsher = $wordUsher;
    }

    public function setFontSizeCalculator(FontSizeCalculatorInterface $fontSizeCalculator)
    {
        $this->fontSizeCalculator = $fontSizeCalculator;
    }

    public function setColorChooser(ColorChooser $colorChooser)
    {
        $this->colorChooser = $colorChooser;
    }

    public function setWordUsher(WordUsher $wordUsher)
    {
        $this->wordUsher = $wordUsher;
    }

    public function getFontSizeCalculator()
    {
        return $this->fontSizeCalculator;
    }

    public function getColorChooser()
    {
        return $this->colorChooser;
    }

    public function getWordUsher()
    {
        return $this->wordUsher;
    }
}
