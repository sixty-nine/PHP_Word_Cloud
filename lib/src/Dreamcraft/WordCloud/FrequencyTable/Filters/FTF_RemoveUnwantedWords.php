<?php

namespace Dreamcraft\WordCloud\FrequencyTable\Filters;

class FTF_RemoveUnwantedWords implements FrequencyTableFilterInterface
{
    protected $unwanted_words;

    public function __construct($unwanted_words = array(
        'and', 'our', 'your', 'their', 'his', 'her', 'the', 'you', 'them', 'yours',
        'with', 'such', 'even')
    )
    {
        $this->unwanted_words = $unwanted_words;
    }

    /**
     * {@inheritdoc}
     */
    public function filterWord($word)
    {
        if (in_array($word, $this->unwanted_words))  {
            return false;
        }
        return $word;
    }
}