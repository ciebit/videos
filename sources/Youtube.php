<?php
namespace Ciebit\Videos;

use Ciebit\Videos\Video;

class Youtube extends Video
{
    private const TYPE = 'youtube';

    public static function getType(): string
    {
        return self::TYPE;
    }
}
