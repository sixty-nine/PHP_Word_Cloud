<?php

namespace SixtyNine\WordCloud;

class WordCloud
{
    protected $fontFile;

    protected $backgroundColor;

    protected $imageHeight = 100;

    protected $imageWidth = 100;

    protected $words = array();

    protected $mask;

    protected $renderingAdjustmentX = 0;

    protected $renderingAdjustmentY = 0;
    

    public function __construct($imageWidth, $imageHeight, $backgroundColor = array(0, 0, 0, 127))
    {
        $this->imageWidth = $imageWidth;
        $this->imageHeight = $imageHeight;
        $this->backgroundColor = $backgroundColor;
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
        $this->renderingAdjustmentX = $dx;
        $this->renderingAdjustmentY = $dy;
    }

    public function getRenderingAdjustment()
    {
        return array($this->renderingAdjustmentX, $this->renderingAdjustmentY);
    }
}