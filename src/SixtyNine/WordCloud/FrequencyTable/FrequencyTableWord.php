<?php

namespace SixtyNine\WordCloud\FrequencyTable;

class FrequencyTableWord
{
    public $word;

    public $title;

    public $count;

    public function __construct($word, $title = null, $useWordAsTitle = false)
    {
        $this->word = $word;
        if ($useWordAsTitle) {
            $title = $word;
        }
        $this->title = $title;
        $this->count = 0;
    }
}