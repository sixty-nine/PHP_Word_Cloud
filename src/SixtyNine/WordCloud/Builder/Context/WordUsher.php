<?php

namespace SixtyNine\WordCloud\Builder\Context;

/**
 * Responsible to find a place for the word in the cloud
 */
abstract class WordUsher
{
    protected $padding;

    public function __construct($padding)
    {
        $this->padding = $padding;
    }

    public abstract function searchPlace($word, $angle, $box);
}