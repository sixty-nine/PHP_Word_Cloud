<?php

namespace SixtyNine\WordCloud\FrequencyTable;

class FrequencyTableFactory
{
    public static function getFrequencyTable($words = '', $filters = array())
    {
        $ft = new FrequencyTable();

        foreach ($filters as $filter) {
            $ft->addFilter($filter);
        }

        if ($words) {
            if (is_array($words)) {
                $ft->addWord($words);
            } else {
                $ft->addText($words);
            }
        }
        
        return $ft;
    }

    public static function getDefaultFrequencyTable($words = '')
    {
        $filters = array(
            new Filters\RemoveShortWords(),
            new Filters\RemoveTrailingPunctuation(),
            new Filters\RemoveUnwantedCharacters(),
            new Filters\RemoveTrailingPunctuation(),
        );
        return self::getFrequencyTable($words, $filters);
    }

}