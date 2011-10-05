<?php

namespace Dreamcraft\WordCloud\Tests\FrequencyTable;

use Dreamcraft\WordCloud\FrequencyTable\FrequencyTable;

class FrequencyTableTest extends \PHPUnit_Framework_TestCase
{
    public function testAddWord()
    {
        $ft = new FrequencyTable();

        // Add a first word

        $ft->addWord('word');
        $table = $ft->getTable();
        $this->assertEquals(1, count($table));
        $this->assertTrue(array_key_exists('word', $table));
        $word = $table['word'];
        $this->assertInstanceOf('Dreamcraft\WordCloud\FrequencyTable\FrequencyTableWord', $word);
        $this->assertEquals(1, $word->count);
        $this->assertEquals(null, $word->title);

        $this->assertEquals(1, $ft->getTotalOccurrences());
        $this->assertEquals(1, $ft->getMaxOccurrences());

        // Add the same word two more times

        $ft->addWord('word', 2);
        $table = $ft->getTable();
        $this->assertEquals(1, count($table));
        $this->assertEquals(3, $table['word']->count);

        $this->assertEquals(3, $ft->getTotalOccurrences());
        $this->assertEquals(3, $ft->getMaxOccurrences());

        // Add another word twice

        $ft->addWord('word2', 2);
        $table = $ft->getTable();
        $this->assertEquals(2, count($table));
        $this->assertEquals(2, $table['word2']->count);

        $this->assertEquals(5, $ft->getTotalOccurrences());
        $this->assertEquals(3, $ft->getMaxOccurrences());
    }

    public function testAddWords()
    {
        $ft = new FrequencyTable();

        $ft->addWords(array('word1', 'word2', 'word2'));
        $table = $ft->getTable();
        $this->assertEquals(2, count($table));
        $this->assertEquals(1, $table['word1']->count);
        $this->assertEquals(2, $table['word2']->count);

        $this->assertEquals(3, $ft->getTotalOccurrences());
        $this->assertEquals(2, $ft->getMaxOccurrences());

        $ft->addWords(array('word1', 'word3', 'word1'));
        $table = $ft->getTable();
        $this->assertEquals(3, count($table));
        $this->assertEquals(3, $table['word1']->count);
        $this->assertEquals(2, $table['word2']->count);
        $this->assertEquals(1, $table['word3']->count);

        $this->assertEquals(6, $ft->getTotalOccurrences());
        $this->assertEquals(3, $ft->getMaxOccurrences());
    }

    public function testAddText()
    {
        $ft = new FrequencyTable();

        $ft->addText('word1 word2 word3 word1 word2 word1');
        $table = $ft->getTable();
        $this->assertEquals(3, count($table));
        $this->assertEquals(3, $table['word1']->count);
        $this->assertEquals(2, $table['word2']->count);
        $this->assertEquals(1, $table['word3']->count);

        $this->assertEquals(6, $ft->getTotalOccurrences());
        $this->assertEquals(3, $ft->getMaxOccurrences());
    }

    // TODO: add tests for the filters chain and for the title support
}