<?php

namespace SixtyNine\WordCloud\FrequencyTable\Filters;

class RemoveTrailingPunctuation implements FrequencyTableFilterInterface
{
    protected $punctuation;

    public function __construct($punctuation = array('.', ',', ';', '?', '!'))
    {
        $this->punctuation = $punctuation;
    }

    /**
     * {@inheritdoc}
     */
    public function filterWord($word)
    {
        foreach($this->punctuation as $p) {
            if(substr($word, -1) == $p) {
                $word = substr($word, 0, -1);
            }
        }
        return $word;
    }
}