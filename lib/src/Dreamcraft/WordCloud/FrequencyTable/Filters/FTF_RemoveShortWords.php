<?php

namespace Dreamcraft\WordCloud\FrequencyTable\Filters;

class FTF_RemoveShortWords implements FrequencyTableFilterInterface
{
    protected $min_length;

    public function __construct($min_length = 3)
    {
        $this->min_length = $min_length;
    }

    /**
     * {@inheritdoc}
     */
    public function filterWord($word)
    {
        if (strlen($word) <= $this->min_length)  {
            return false;
        }
        return $word;
    }
}