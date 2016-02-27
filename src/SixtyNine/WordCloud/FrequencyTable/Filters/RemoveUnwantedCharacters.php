<?php

namespace SixtyNine\WordCloud\FrequencyTable\Filters;

class RemoveUnwantedCharacters implements FrequencyTableFilterInterface
{
    protected $unwantedCharacters;

    public function __construct($unwantedCharacters = array('?', '!', '\'', '"', '(', ')'))
    {
        $this->unwantedCharacters = $unwantedCharacters;
    }

    /**
     * {@inheritdoc}
     */
    public function filterWord($word)
    {
        foreach($this->unwantedCharacters as $p) {
          $word = str_replace($p, '', $word);
        }
        return $word;
    }
}