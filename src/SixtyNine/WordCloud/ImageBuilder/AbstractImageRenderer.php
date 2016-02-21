<?php

namespace SixtyNine\WordCloud\ImageBuilder;

use SixtyNine\WordCloud\Renderer\WordCloudRenderer;
use SixtyNine\WordCloud\WordCloud;

abstract class AbstractImageRenderer
{
    /** @var WordCloud */
    protected $cloud;

    /** @var WordCloudRenderer */
    protected $cloudRenderer;

    public function __construct(WordCloud $cloud, WordCloudRenderer $cloudRenderer)
    {
        $this->cloud = $cloud;
        $this->cloudRenderer = $cloudRenderer;
    }

    public abstract function getImage();
} 