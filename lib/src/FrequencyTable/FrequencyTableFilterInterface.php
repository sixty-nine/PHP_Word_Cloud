<?php

interface FrequencyTableFilterInterface
{
    /**
     * @param string $word The word to filter
     * @return string The filtered word or [false | null | empty string] to reject the word
     */
    public function filterWord($word);
}