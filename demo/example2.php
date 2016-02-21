<?php
/**
 * This is a basic example, it reads $_POST and web pages without any sort of
 * escaping. It should be considered unsafe to use in prod.
 */
require __DIR__.'/../lib/src/autoload.php';

use Dreamcraft\WordCloud\Builder\WordCloudBuilder;
use Dreamcraft\WordCloud\Renderer\WordCloudRenderer;
use Dreamcraft\WordCloud\Helper\Palette;
use Dreamcraft\WordCloud\FrequencyTable\FrequencyTableFactory;
use Dreamcraft\WordCloud\Builder\Context\BuilderContextFactory;
use Dreamcraft\WordCloud\ImageBuilder\RawImageRenderer;

$url = $_POST['url'] ?: 'https://en.wikipedia.org/wiki/Wikipedia';
$text = file_get_contents($url);
$d = new DOMDocument;
$mock = new DOMDocument;
$d->loadHTML($text);
$body = $d->getElementsByTagName('body')->item(0);
foreach ($body->childNodes as $child){
    $mock->appendChild($mock->importNode($child, true));
}
$text = html_entity_decode(strip_tags($mock->saveHTML()));

$img_width = 800;
$img_height = 640;

$palette = Palette::getNamedPalette($_POST['palette'] ?: 'grey');
$font = $_POST['font'] ?: 'Arial.ttf';
$font = __DIR__.'/../fonts/' . $font;

$ft = FrequencyTableFactory::getDefaultFrequencyTable($text);

$builder = new WordCloudBuilder(
    $ft,
    BuilderContextFactory::getDefaultBuilderContext($ft, $palette, $font, $img_width, $img_height),
    array(
        'font' => $font,
        'size' => array($img_width, $img_height)
    )
);

$imgRenderer = new RawImageRenderer(
    $builder->build(50),
    new WordCloudRenderer()
);

?>
<body bgcolor="black">
    <div class="controls">
        <form method="post">
            <input type="text" name="url" />
            <select name="palette">
                <option value="aqua">Aqua</option>
                <option value="yellow/blue">Yellow/Blue</option>
                <option value="grey">Greyscale</option>
                <option value="brown">Brown</option>
                <option value="army">Army</option>
                <option value="pastel">Pastel</option>
                <option value="red">Red</option>
            </select>
            <select name="font">
                <option value="Airmole_Antique.ttf">Airmole Antique</option>
                <option value="Airmole_Shaded.ttf">Airmole Shaded</option>
                <option value="Alexis_3D.ttf">Alexis 3D</option>
                <option value="Almonte_Snow.ttf">Almonte Snow</option>
                <option value="Arial.ttf">Arial</option>
                <option value="Paper_Cut.ttf">Paper Cut</option>
                <option value="TheThreeStoogesFont.ttf">The Three Stooges</option>
            </select>
            <input type="submit" value="Go" />
        </form>
    </div>
    <div class="word-cloud">
        <img src="data:image/png;base64,<?= base64_encode($imgRenderer->getImage()) ?>" />
    </div>
</body>
