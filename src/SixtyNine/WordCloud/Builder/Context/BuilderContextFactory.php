<?php

namespace SixtyNine\WordCloud\Builder\Context;

use SixtyNine\WordCloud\FrequencyTable\FrequencyTable;

class BuilderContextFactory
{
    public static function getDefaultBuilderContext(FrequencyTable $table, $palette, $font, $img_width, $img_height)
    {
        $context = new BuilderContext();
        $fc = new DefaultFontSizeCalculator(16, 72, 1, $table->getMaxOccurrences());
        $context->setFontSizeCalculator($fc);
        $context->setColorChooser(new RotatorColorChooser($palette));
        $context->setWordUsher(new DefaultWordUsher(1.05, $img_width, $img_height));
        return $context;
    }
}
