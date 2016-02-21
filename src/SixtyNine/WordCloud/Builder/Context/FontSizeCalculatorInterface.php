<?php

namespace SixtyNine\WordCloud\Builder\Context;

interface FontSizeCalculatorInterface
{
    /**
     * @abstract
     * @param string $word The word
     * @param int $occurrences The number of occurrences of the word
     * @return float
     */
    public function calculateFontSize($word, $occurrences);
}
