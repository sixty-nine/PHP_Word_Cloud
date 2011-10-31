<?php

namespace Dreamcraft\WordCloud\Builder;

use Dreamcraft\WordCloud\FrequencyTable\FrequencyTable,
    Dreamcraft\WordCloud\WordCloud,
    Dreamcraft\WordCloud\Box,
    Dreamcraft\WordCloud\Builder\Context\BuilderContext;

class WordCloudBuilder
{
    const WORDS_HORIZONTAL = 0;
    const WORDS_MAINLY_HORIZONTAL = 1;
    const WORDS_MIXED = 6;
    const WORDS_MAINLY_VERTICAL = 9;
    const WORDS_VERTICAL = 10;

    protected $frequency_table;

    protected $cloud;

    protected $context;

    protected $config;

    public function __construct(FrequencyTable $table, BuilderContext $context, $config)
    {
        $this->checkConfigParameters($config);
        $this->config = $config;
        $this->context = $context;
        $this->frequency_table = $table;
        $this->cloud = new WordCloud($config['size'][0], $config['size'][1]);
        $this->cloud->setFont($config['font']);
        $this->cloud->setPalette($config['palette']);
    }

    protected function checkConfigParameters($config)
    {
        foreach (array('size', 'font', 'palette') as $param) {
            if (!array_key_exists($param, $config)) {
                throw new \InvalidArgumentException("Missing config parameter '$param'");
            }
        }
        if (!is_array($config['size']) || !count($config['size']) == 2) {
            throw new \InvalidArgumentException("Invalid config parameter 'size'. It must be a 2 dimensional array of int.");
        }
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

        // Add the words in the cloud and compute the size and orientation
        foreach($table as $text => $item)
        {
            $word = $this->cloud->addWord($text, $item->title);

            // Calculate the font size
            $word->size = $this->context->getFontSizeCalculator()->calculateFontSize($text, $item->count);

            // Randomize the text orientation
            $word->angle = 0;
            if (rand(1, 10) <= $orientation) $word->angle = 90;

            // Calculate the bounding box of the text
            $word->text_box = imagettfbbox(
                $word->size * $padding_size,
                $word->angle - $padding_angle,
                $this->cloud->getFont(),
                $text
            );

            // Calculate the color
            $word->color = $this->context->getColorChooser()->getNextColor();

            // Search a place for the word
            $coord = $this->context->getWordUsher()->searchPlace($text, $word->angle, $word->text_box);
            $word->x = $coord[0];
            $word->y = $coord[1];

//            if ($word->angle == 0) {
//                $cx = $this->cloud->getImageWidth() / 3;
//                $cy = $this->cloud->getImageHeight() / 2;
//            }
//            else {
//                $cx = $this->cloud->getImageWidth() / 3 + rand(0, $this->cloud->getImageWidth() / 10);
//                $cy = $this->cloud->getImageHeight() / 2 + rand(-$this->cloud->getImageHeight() / 10, $this->cloud->getImageHeight() / 10);
//            }
//
//            list($cx, $cy) = $this->cloud->getMask()->searchPlace($cx, $cy, $word->text_box);
//            $word->x = $cx;
//            $word->y = $cy;
//
//            $this->cloud->getMask()->add(new Box($cx, $cy, $word->text_box));

            $counter++;
        }

        $this->cloud->setMask($this->context->getWordUsher()->getMask());

        return $this->cloud;
    }

}
