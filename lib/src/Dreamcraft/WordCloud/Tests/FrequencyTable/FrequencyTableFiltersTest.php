<?php

namespace Dreamcraft\WordCloud\Tests\FrequencyTable\Filters;

require_once __DIR__.'/../../../../autoload.php';

use Dreamcraft\WordCloud\FrequencyTable\Filters;

class ArrayTest extends \PHPUnit_Framework_TestCase
{
    public function testFTF_RemoveShortWords()
    {
        $this->assertFilterWorks(
            new Filters\FTF_RemoveShortWords(),
            array('1', '12', '123'),
            array('1234' => '1234', '12345' => '12345')
        );
    }

    public function testFTF_RemoveTrailing()
    {
        $this->assertFilterWorks(
            new Filters\FTF_RemoveTrailingPunctuation(),
            array(),
            array(
                '1234' => '1234',
                '12345.' => '12345',
                '12345,.' => '12345',
                '12345!?' => '12345',
            )
        );
    }

    public function testFTF_RemoveUnwantedCharacters()
    {
        $this->assertFilterWorks(
            new Filters\FTF_RemoveUnwantedCharacters(),
            array(),
            array(
                '12345' => '12345',
                '123?4' => '1234',
                '"12345"' => '12345',
                '\'12345\'' => '12345',
                '?!1234!?' => '1234',
            )
        );
    }

    public function testFTF_RemoveUnwantedWords()
    {
        $this->assertFilterWorks(
            new Filters\FTF_RemoveUnwantedWords(),
            array(
                'and', 'our', 'your', 'their', 'his', 'her', 'the',
                'you', 'them', 'yours', 'with', 'such', 'even'
            ),
            array('some' => 'some', 'other' => 'other', 'words' => 'words')
        );
    }

    /**
     * Generic FrequencyTableFilterInterface testing function.
     * @param \Dreamcraft\WordCloud\FrequencyTable\FrequencyTableFilterInterface $filter An instance of the filter to test
     * @param array $rejected_words An array of words that should be rejected
     * @param array $accepted_words An associative array which key are the word to filter and the value is the expected result
     * @return void
     */
    protected function assertFilterWorks(Filters\FrequencyTableFilterInterface $filter, $rejected_words, $accepted_words)
    {
        foreach($rejected_words as $word) {
            $this->assertFalse(
                $filter->filterWord($word),
                "The filter '" . get_class($filter) . "' did not reject the word '$word' as expected."
            );
        }

        foreach($accepted_words as $word => $result) {
            $filtered = $filter->filterWord($word);
            $this->assertEquals(
                $result,
                $filtered,
                "The filter '" . get_class($filter) . "' did not produce the expected result '$result' for the word '$word'." .
                "\nGot '$filtered' instead."
            );
        }
    }
}