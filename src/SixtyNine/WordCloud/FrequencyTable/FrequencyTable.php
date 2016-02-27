<?php

namespace SixtyNine\WordCloud\FrequencyTable;

/**
 * Stores the words to and their frequencies.
 */
class FrequencyTable
{
    /**
     * An array of FrequencyTableWord
     * @var array
     */
    protected $words = array();

    /**
     * An array of FrequencyTableFilterInterface
     * @var array
     */
    protected $filters = array();

    /**
     * The total number of occurrences of words
     * @var int
     */
    protected $totalOccurrences = 0;

    /**
     * The maximum number of occurrences for a word
     * @var int
     */
    protected $maxOccurrences = 0;

    /**
     * Add a word to the frequency table.
     * @param string $word The word to add
     * @param int $occurrences The number of time the word should be counted
     * @param string $title The title to use for the word, null if none
     * @param boolean $useFilters If true the filters will be applied to the word, if false, the word will be added as it is (unless it is an empty string)
     * @return void
     */
    public function addWord($word, $occurrences = 1, $title = null, $useFilters = true)
    {
        // Filter out unwanted words
        if ($useFilters) {
            /** @var \SixtyNine\WordCloud\FrequencyTable\Filters\FrequencyTableFilterInterface $filter*/
            foreach($this->filters as $filter) {
                $word = $filter->filterWord($word);
                if (!$word) return; // Return if the word was filtered out
            }
        }

        if (!$word) return; // Return if the word is empty

        // Store the word and count occurrences
        if (!array_key_exists($word, $this->words)) {
            // TODO: expose the $use_word_as_title parameter
            $this->words[$word] = new FrequencyTableWord($word, $title);
        }
        $this->words[$word]->count += $occurrences;

        // Update global stats
        $this->totalOccurrences += $occurrences;

        if ($this->words[$word]->count > $this->maxOccurrences) {
            $this->maxOccurrences = $this->words[$word]->count;
        }
    }

    /**
     * Add a list of words to the frequency table.
     * @param array $words The list of words to add
     * @param boolean $useFilters If true the filters will be applied to the words list, if false, the words will be added as they are (unless they are ermpty)
     * @param boolean $useWordAsTitle If true, the word itself will be used as title, otherwise the title will be empty
     * @return void
     */
    public function addWords($words, $useFilters = true, $useWordAsTitle = false)
    {
        foreach($words as $word) {
            $title = null;
            if ($useWordAsTitle) {
                $title = $word;
            }
            $this->addWord($word, 1, $title, $useFilters);
        }
    }

    /**
     * Extract a list of words from a text and add them to the frequency table.
     * @param string $text The text to parse
     * @param boolean $useFilters If true the filters will be applied to the words list, if false, the words will be added as they are (unless they are ermpty)
     * @param boolean $useWordAsTitle If true, the word itself will be used as title, otherwise the title will be empty
     * @return void
     */
    public function addText($text, $useFilters = true, $useWordAsTitle = false)
    {
        $this->addWords(preg_split("/[\n\r\t ]+/", $text), $useFilters, $useWordAsTitle);
    }

    /**
     * Add a filter to the filters chain. Each filter in the filter chain can be applied in order when words are added.
     * If a filter in the chain returns an empty string, the word will not be added to the frequency table.
     * @param \SixtyNine\WordCloud\FrequencyTable\Filters\FrequencyTableFilterInterface $filter
     * @return void
     */
    public function addFilter(Filters\FrequencyTableFilterInterface $filter)
    {
        $this->filters[] = $filter;
    }

    /**
     * Get the frequency table.
     * @param int $limit The maximal number of words to return, by default all the words are returned
     * @return array(string => FrequencyTableWord)
     */
    public function getTable($limit = null)
    {
        // Cut the table so we have only $limit words
        $table = array_slice($this->words, 0, $limit);
        arsort($table);
        return $table;
    }

    /**
     * @return int
     */
    public function getTotalOccurrences()
    {
        return $this->totalOccurrences;
    }

    /**
     * @return int
     */
    public function getMaxOccurrences()
    {
        return $this->maxOccurrences;
    }
}