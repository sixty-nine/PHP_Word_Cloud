<?php

namespace Dreamcraft\WordCloud\FrequencyTable\Filters;

class RemoveUnwantedCharacters implements FrequencyTableFilterInterface
{
    protected $unwanted_characters;

    public function __construct($unwanted_characters = array('?', '!', '\'', '"', '(', ')'))
    {
        $this->unwanted_characters = $unwanted_characters;
    }

    /**
     * {@inheritdoc}
     */
    public function filterWord($word)
    {
        foreach($this->unwanted_characters as $p) {
          $word = str_replace($p, '', $word);
        }
        return $word;
    }
}