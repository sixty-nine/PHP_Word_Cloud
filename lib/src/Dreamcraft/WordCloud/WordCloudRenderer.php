<?php

namespace Dreamcraft\WordCloud;

use Dreamcraft\WordCloud\WordCloud;

class WordCloudRenderer
{
    public static function render(WordCloud $cloud)
    {
        list($x1, $y1, $x2, $y2) = $cloud->getMask()->getBoundingBox();
        $bgcol = $cloud->getBackgroundColor();

        $image = imagecreatetruecolor($cloud->getImageWidth(), $cloud->getImageHeight());
        imagesavealpha($image, true);
        $trans_colour = imagecolorallocatealpha($image, $bgcol[0],$bgcol[1], $bgcol[2], $bgcol[3]);
        imagefill($image, 0, 0, $trans_colour);

        // Draw the words
        foreach($cloud->getWords() as $word) {
            // TODO: find a way to allocate the colors outside of the loop
            $color = imagecolorallocate($image, $word->color[0], $word->color[1], $word->color[2]);
            imagettftext($image, $word->size, $word->angle, $word->x, $word->y, $color, $cloud->getFont(), $word->text);
        }

        // Crop the image
        // TODO: check if the transparency must be set to $image2 or if it is copied
        $image2 = imagecreatetruecolor(abs($x2 - $x1), abs($y2 - $y1));
        imagesavealpha($image2, true);
        imagecopy($image2 ,$image, 0, 0, $x1, $y1, abs($x2 - $x1), abs($y2 - $y1));
        imagedestroy($image);
        $image = $image2;

        // Adjust the map to the cropped image
        $cloud->getMask()->adjust(-$x1, -$y1);

        return $image;
    }
}
 
