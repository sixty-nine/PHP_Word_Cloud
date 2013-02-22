<?php

namespace Dreamcraft\WordCloud\Renderer;

use Dreamcraft\WordCloud\WordCloud;

class WordCloudRenderer
{
    public function render(WordCloud $cloud)
    {
        list($x1, $y1, $x2, $y2) = $cloud->getMask()->getEnclosingBox();
        $bgcol = $cloud->getBackgroundColor();

        $image = imagecreatetruecolor($cloud->getImageWidth(), $cloud->getImageHeight());
        imagealphablending($image, false);
        imagesavealpha($image, true);
        $trans_colour = imagecolorallocatealpha($image, $bgcol[0],$bgcol[1], $bgcol[2], $bgcol[3]);
        imagefill($image, 0, 0, $trans_colour);

        $centerx = (int)$cloud->getImageWidth() / 2;
        $centery = (int)$cloud->getImageHeight() / 2;

        // Draw the words
        foreach($cloud->getWords() as $word) {
            // TODO: find a way to allocate the colors outside of the loop
            $color = imagecolorallocate($image, $word->color[0], $word->color[1], $word->color[2]);
            imagettftext($image, $word->size, $word->angle, $word->x, $word->y, $color, $cloud->getFont(), $word->text);
        }

        // Crop the image
        $image = $this->cropImage($image, $x1, $y1, $x2, $y2, $bgcol);

        // Adjust the map to the cropped image
        $cloud->getMask()->adjust(-$x1, -$y1);

        return $image;
    }

    protected function cropImage($img, $x1, $y1, $x2, $y2, $bgcol)
    {
        // TODO: fix transparency
        // TODO: fix cropping
        $image2 = imagecreatetruecolor(abs($x2 - $x1), abs($y2 - $y1));
        imagealphablending($image2, false);
        imagesavealpha($image2, true);
        $trans_colour = imagecolorallocatealpha($image2, $bgcol[0],$bgcol[1], $bgcol[2], $bgcol[3]);
        imagefill($image2, 0, 0, $trans_colour);
        imagecopy($image2 ,$img, 0, 0, $x1, $y1, abs($x2 - $x1), abs($y2 - $y1));
        imagedestroy($img);
        return $image2;
    }
}
 
