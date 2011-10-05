<?php

require __DIR__.'/../lib/src/autoload.php';

use Dreamcraft\WordCloud\WordCloudBuilder,
    Dreamcraft\WordCloud\WordCloudRenderer,
    Dreamcraft\WordCloud\Palette,
    Dreamcraft\WordCloud\FrequencyTable\FrequencyTableFactory;

$text = <<<EOT
A tag cloud (word cloud, or weighted list in visual design) is a visual representation for text data, typically used to depict keyword metadata (tags) on websites, or to visualize free form text. 'Tags' are usually single words, normally listed alphabetically, and the importance of each tag is shown with font size or color.[1] This format is useful for quickly perceiving the most prominent terms and for locating a term alphabetically to determine its relative prominence. When used as website navigation aids, the terms are hyperlinked to items associated with the tag.
EOT;

$font = __DIR__.'/../fonts/TheThreeStoogesFont.ttf';

$img_width = 1000;

$img_height = 1000;

$palette = Palette::getPaletteFromHex(array('FFA700', 'FFDF00', 'FF4F00', 'FFEE73'));

$ft = FrequencyTableFactory::getDefaultFrequencyTable($text);

$builder = new WordCloudBuilder($ft, $font, $palette, $img_width, $img_height);
$cloud = $builder->build();

$img = WordCloudRenderer::render($cloud);

header('Content-type: image/png');
imagepng($img);
imagedestroy($img);

