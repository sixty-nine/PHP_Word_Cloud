<?php

namespace SixtyNine\WordCloud\FrequencyTable\Filters;

class RemoveUnwantedWords implements FrequencyTableFilterInterface
{
    protected $unwantedWords;

    public function __construct($unwantedWords = array(
        'and', 'our', 'your', 'their', 'his', 'her', 'the', 'you', 'them', 'yours',
        'with', 'such', 'even')
    )
    {
        $this->unwantedWords = $unwantedWords;
    }

    /**
     * {@inheritdoc}
     */
    public function filterWord($word)
    {
        if (in_array($word, $this->unwantedWords))  {
            return false;
        }
        return $word;
    }
}