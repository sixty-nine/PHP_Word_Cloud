<?php

require __DIR__.'/../lib/src/autoload.php';

use Dreamcraft\WordCloud\Builder\WordCloudBuilder,
    Dreamcraft\WordCloud\Renderer\WordCloudRenderer,
    Dreamcraft\WordCloud\Helper\Palette,
    Dreamcraft\WordCloud\FrequencyTable\FrequencyTableFactory,
    Dreamcraft\WordCloud\Builder\Context\BuilderContextFactory;

/**
 * The text to build the word cloud from
 */
$text = <<<EOT
A tag cloud (word cloud, or weighted list in visual design) is a visual representation for text data, typically used to depict keyword metadata (tags) on websites, or to visualize free form text. 'Tags' are usually single words, normally listed alphabetically, and the importance of each tag is shown with font size or color.[1] This format is useful for quickly perceiving the most prominent terms and for locating a term alphabetically to determine its relative prominence. When used as website navigation aids, the terms are hyperlinked to items associated with the tag.
In the language of visual design, a tag cloud (or word cloud) is one kind of "weighted list", as commonly used on geographic maps to represent the relative size of cities in terms of relative typeface size. An early printed example of a weighted list of English keywords was the "subconscious files" in Douglas Coupland's Microserfs (1995). A German appearance occurred in 1992.[2]

The specific visual form and common use of the term "tag cloud" rose to prominence in the first decade of the 21st century as a widespread feature of early Web 2.0 websites and blogs, used primarily to visualize the frequency distribution of keyword metadata that describe website content, and as a navigation aid.

The first tag clouds on a high-profile website were on the photo sharing site Flickr, created by Flickr co-founder and interaction designer Stewart Butterfield in 2004. That implementation was based on Jim Flanagan's Search Referral Zeitgeist,[3] a visualization of Web site referrers. Tag clouds were also popularized around the same time by Del.icio.us and Technorati, among others.

Over-saturation of the tag cloud method and ambivalence about its utility as a web-navigation tool led to a noted decline of usage among these early adopters.[4][5] (Flickr would later "apologize" to the web-development community in their five-word acceptance speech for the 2006 "Best Practices" Webby Award, where they simply stated "sorry about the tag clouds."[6])

A second generation of software development discovered a wider diversity of uses for tag clouds as a basic visualization method for text data. Most notably, the method was adapted for visualizing word frequency in free-form natural language texts, first by TagCrowd[7], created by Stanford University researcher and designer Daniel Steinbock in 2006[8], and further popularized by Wordle[9], created by IBM researcher Jonathan Feinberg in 2008.[10]
EOT;

/**
 * The TrueType font to use
 */
$font = __DIR__.'/../fonts/Paper_Cut.ttf';

/**
 * Dimensions of the resulting image
 */
$img_width = $_GET['w'];

$img_height = $_GET['h'];

/**
 * The color palette to use
 */
$palette = Palette::getPaletteFromHex(array('FFA700', 'FFDF00', 'FF4F00', 'FFEE73'));

/**
 * Create a frequency table from the text
 */
$ft = FrequencyTableFactory::getDefaultFrequencyTable($text);

/**
 * Setup the cloud builder and build the word cloud
 */
$builder = new WordCloudBuilder(
    $ft,
    BuilderContextFactory::getDefaultBuilderContext($ft, $palette, $font, $img_width, $img_height),
    array(
         'font' => $font,
         'size' => array($img_width, $img_height)
    )
);
$cloud = $builder->build(50);

/**
 * Render the word cloud image
 */
$renderer = new WordCloudRenderer();
$img = $renderer->render($cloud);

/**
 * Return the image to the client
 */
header('Content-type: image/png');
imagepng($img);
imagedestroy($img);
