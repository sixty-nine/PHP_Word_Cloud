<?php

namespace SixtyNine\WordCloud\FrequencyTable\Filters;

class RemoveShortWords implements FrequencyTableFilterInterface
{
    protected $minLength;

    public function __construct($minLength = 3)
    {
        $this->minLength = $minLength;
    }

    /**
     * {@inheritdoc}
     */
    public function filterWord($word)
    {
        if (strlen($word) <= $this->minLength)  {
            return false;
        }
        return $word;
    }
}