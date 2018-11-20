<?php
namespace Ciebit\Videos;

use Ciebit\Videos\Video;

class File extends Video
{
    private const TYPE = 'file';

    protected function getType(): string
    {
        return self::TYPE;
    }
}
