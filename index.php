<?php

require dirname(__FILE__).'/tagcloud.php';

$full_text = <<<EOT
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

$font = dirname(__FILE__).'/Arial.ttf';
$width = 600;
$height = 600;
$cloud = new WordCloud($width, $height, $font, $full_text);
$palette = Palette::get_palette_from_hex($cloud->get_image(), array('FFA700', 'FFDF00', 'FF4F00', 'FFEE73'));
$cloud->render($palette);

// Render the cloud in a temporary file, and return its base64-encoded content
$file = tempnam(getcwd(), 'img');
imagepng($cloud->get_image(), $file);
$img64 = base64_encode(file_get_contents($file));
unlink($file);
imagedestroy($cloud->get_image());
?>

<img usemap="#mymap" src="data:image/png;base64,<?php echo $img64 ?>" border="0"/>
<map name="mymap">
<?php foreach($cloud->get_image_map() as $map): ?>
<area shape="rect" coords="<?php echo $map[1]->get_map_coords() ?>" onclick="alert('You clicked: <?php echo $map[0] ?>');" />
<?php endforeach ?>
</map>