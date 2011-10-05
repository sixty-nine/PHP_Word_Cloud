<?php

require __DIR__.'/../lib/src/autoload.php';

use Dreamcraft\WordCloud\WordCloudBuilder,
    Dreamcraft\WordCloud\WordCloudRenderer,
    Dreamcraft\WordCloud\Palette,
    Dreamcraft\WordCloud\FrequencyTable\FrequencyTableFactory;

$text = <<<EOT
dreamcraft.ch is a developement company based in Switzerland, aimed to create, integrate and mantain cutting-edge technology web applications.
We provide you our expertise in PHP/MySQL, Microsoft .NET and various Content Management Systems in order to develop new web applications or maintain and upgrade your current web sites.
Our philosophy:
Establish a durable and trustworthy win-win relation with our customers.
Use quality Open Source Software whenever possible.
Enforce good programming rules and standards to create better rich content web 2.0 applications.
A folk wisdom says "united we stand, divided we fall". As such we work closely with other Swiss companies to offer you an even larger range of skills.
Aicom are the creators of interactive web based tools such as FormFish, MettingPuzzle or MailJuggler.
oriented.net is a high-quality web hosting company based in Switzerland. Our partnership with them allows us to offer advanced hosting solutions to your web application.
EOT;

$font = __DIR__.'/../fonts/TheThreeStoogesFont.ttf';

$img_width = 600;

$img_height = 600;

$palette = Palette::getPaletteFromHex(array('FFA700', 'FFDF00', 'FF4F00', 'FFEE73'));

$ft = FrequencyTableFactory::getDefaultFrequencyTable($text);

$builder = new WordCloudBuilder($ft, $font, $palette, $img_width, $img_height);
$cloud = $builder->build();

$img = WordCloudRenderer::render($cloud);

header('Content-type: image/png');
imagepng($img);
imagedestroy($img);

