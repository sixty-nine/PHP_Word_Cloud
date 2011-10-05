<?php

namespace Dreamcraft\WordCloud;

use Dreamcraft\WordCloud\FrequencyTable\FrequencyTable;

class WordCloudBuilder
{
    const WORDS_HORIZONTAL = 0;
    const WORDS_MAINLY_HORIZONTAL = 1;
    const WORDS_MIXED = 6;
    const WORDS_MAINLY_VERTICAL = 9;
    const WORDS_VERTICAL = 10;

    protected $frequency_table;

    protected $cloud;

    public function __construct(FrequencyTable $table, $font, $palette, $image_width, $image_height)
    {
        $this->frequency_table = $table;
        $this->cloud = new WordCloud($image_width, $image_height);
        $this->cloud->setFont($font);
        $this->cloud->setPalette($palette);
    }

    /**
     * @param int $limit The maximal number of words to show
     * @param int $min_font_size The minimal font size
     * @param int $max_font_size The maximal font size
     * @param int $orientation The orientation (see self::WORDS_* constants)
     * @param float $padding_size
     * @param int $padding_angle
     * @return \Dreamcraft\WordCloud\WordCloud
     */
    public function build(
        $limit = null,
        $min_font_size = 16,
        $max_font_size = 72,
        $orientation = self::WORDS_MAINLY_HORIZONTAL,
        $padding_size = 1.05,
        $padding_angle = 0
    )
    {
        $table = $this->frequency_table->getTable($limit);

        $palette = $this->cloud->getPalette();
        
        $counter = 0;

        $this->cloud->resetMask();

        // Variables for font size computation
        $count = count($table);
        $min_count = 1;
        $max_count = $this->frequency_table->getMaxOccurrences();
        $diffcount = ($max_count - $min_count) != 0 ? ($max_count - $min_count) : 1;
        $diffsize = ($max_font_size - $min_font_size) != 0 ? ($max_font_size - $min_font_size) : 1;
        $slope = $diffsize / $diffcount;
        $yintercept = $max_font_size - ($slope * $max_count);

        // Add the words in the cloud and compute the size and orientation
        foreach($table as $text => $item)
        {
            $word = $this->cloud->addWord($text, $item->title);

            // ----- Calculate the font size
            $font_size = (integer)($slope * $table[$text]->count + $yintercept);

            if ($font_size < $min_font_size) {
                $font_size = $min_font_size;
            } elseif ($font_size > $max_font_size) {
                $font_size = $max_font_size;
            }

            $word->size = $font_size;


            // ----- Randomize the text orientation
            $word->angle = 0;
            if (rand(1, 10) <= $orientation) $word->angle = 90;


            // ----- Calculate the bounding box of the text
            $word->text_box = imagettfbbox(
                $word->size * $padding_size,
                $word->angle - $padding_angle,
                $this->cloud->getFont(),
                $text
            );


            // ----- Calculate the color
            $word->color = $palette[$counter % count($palette)];


            // ----- Search a place for the word

            // Set the center so that vertical words are better distributed
            if ($word->angle == 0) {
                $cx = $this->cloud->getImageWidth() / 3;
                $cy = $this->cloud->getImageHeight() / 2;
            }
            else {
                $cx = $this->cloud->getImageWidth() / 3 + rand(0, $this->cloud->getImageWidth() / 10);
                $cy = $this->height / 2 + rand(-$this->cloud->getImageHeight() / 10, $this->cloud->getImageHeight() / 10);
            }

            list($cx, $cy) = $this->cloud->getMask()->searchPlace($cx, $cy, $word->text_box);
            $word->x = $cx;
            $word->y = $cy;

            $this->cloud->getMask()->add(new Box($cx, $cy, $word->text_box));

            $counter++;
        }

        return $this->cloud;
    }

}
