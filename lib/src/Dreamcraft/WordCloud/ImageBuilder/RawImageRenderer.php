<?php

namespace Dreamcraft\WordCloud\ImageBuilder;

class RawImageRenderer extends AbstractImageRenderer
{
    public function getImage()
    {
        $img = $this->cloudRenderer->render($this->cloud);
        $file = sprintf('%s/%s', sys_get_temp_dir(), uniqid());

        imagepng($img, $file);
        imagedestroy($img);

        $content = file_get_contents($file);
        unlink($file);

        return $content;
    }
}
