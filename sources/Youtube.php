<?php
namespace Ciebit\Videos;

use Ciebit\Videos\Video;

class Youtube extends Video
{
    private const TYPE = 'youtube';

    protected function getType(): string
    {
        return self::TYPE;
    }
}
