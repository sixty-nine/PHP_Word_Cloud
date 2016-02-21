<?php

namespace Dreamcraft\WordCloud\Builder\Context;

use Dreamcraft\WordCloud\Mask,
    Dreamcraft\WordCloud\Box;

/**
 * Responsible to find a place for the word in the cloud
 */
class DefaultWordUsher extends WordUsher
{
    protected $mask;

    protected $img_width;

    protected $img_height;

    public function __construct($padding, $img_width, $img_height)
    {
        parent::__construct($padding);

        $this->mask = new Mask();
        $this->img_width = $img_width;
        $this->img_height = $img_height;
    }

    public function getMask()
    {
        return $this->mask;
    }

    public function searchPlace($word, $angle, $box)
    {
        // Set the center so that vertical words are better distributed
        if ($angle == 0) {
            $cx = $this->img_width / 3;
            $cy = $this->img_height / 2;
        }
        else {
            $cx = $this->img_width / 3 - rand(0, $this->img_width / 10);
            $cy = $this->img_height / 2 - rand(-$this->img_height / 10, $this->img_height / 10);
        }

        $coord = $this->mask->searchPlace($cx, $cy, $box);
        $this->mask->add(new Box($coord[0], $coord[1], $box));
        return $coord;
    }
}
