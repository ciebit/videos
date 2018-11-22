<?php
namespace Ciebit\Videos;

use Ciebit\Videos\Video;

class File extends Video
{
    private const TYPE = 'file';

    public static function getType(): string
    {
        return self::TYPE;
    }
}
