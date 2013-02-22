<?php

namespace Dreamcraft\WordCloud;

class WordCloud
{
    protected $fontFile;

    protected $backgroundColor;

    protected $imageHeight = 100;

    protected $imageWidth = 100;

    protected $words = array();

    protected $mask;

    protected $rendering_adjustment_x = 0;

    protected $rendering_adjustment_y = 0;
    

    public function __construct($image_width, $image_height, $background_color = array(0, 0, 0, 127))
    {
        $this->imageWidth = $image_width;
        $this->imageHeight = $image_height;
        $this->backgroundColor = $background_color;
        $this->mask = new Mask();
    }

    public function addWord($text, $title)
    {
        if (!array_key_exists($text, $this->words)) {

            $word = new Word();
            $word->text = $text;
            $word->title = $title;
            $this->words[$text] = $word;
        }
        return $this->words[$text];
    }

    public function getWord($text)
    {
        if (!array_key_exists($text, $this->words)) {
            throw new \Exception("Word not found '$text'.");
        }

        return $this->words[$text];
    }

    public function getWords()
    {
        return $this->words;
    }

    public function setFont($font)
    {
        if (!file_exists($font)) {
            throw new \InvalidArgumentException("Font file '$font' not found.");
        }
        $this->fontFile = $font;
    }

    public function getFont()
    {
        return $this->fontFile;
    }

    public function getImageWidth()
    {
        return $this->imageWidth;
    }

    public function getImageHeight()
    {
        return $this->imageHeight;
    }

    public function getMask()
    {
        return $this->mask;
    }

    public function setMask(Mask $mask)
    {
        $this->mask = $mask;
    }

    public function resetMask()
    {
        $this->mask = new Mask();
    }

    public function getBackgroundColor()
    {
        return $this->backgroundColor;
    }

    /**
     * Adjusts the word cloud after the crop during the rendering
     * @param $dx
     * @param $dy
     * @return void
     */
    public function adjust($dx, $dy)
    {
        $this->getMask()->adjust($dx, $dy);
        $this->rendering_adjustment_x = $dx;
        $this->rendering_adjustment_y = $dy;
    }

    public function getRenderingAdjustment()
    {
        return array($this->rendering_adjustment_x, $this->rendering_adjustment_y);
    }
}