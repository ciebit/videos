<?php
namespace Ciebit\Videos;

use Ciebit\Videos\Video;

class Facebook extends Video
{
    private const TYPE = 'facebook';

    private function getType(): string
    {
        return self::TYPE;
    }
}
