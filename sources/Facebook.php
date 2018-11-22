<?php
namespace Ciebit\Videos;

use Ciebit\Videos\Video;

class Facebook extends Video
{
    private const TYPE = 'facebook';

    public static function getType(): string
    {
        return self::TYPE;
    }
}
