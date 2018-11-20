<?php
namespace Ciebit\VideosTests;

use DateTime;
use Ciebit\Videos\Youtube;
use Ciebit\Videos\Status;
use Ciebit\VideosTests\Video as VideosTests;

class YoutubeTest extends VideosTests
{
    public function testCreateFromManual(): void
    {
        $videoYoutube = new Youtube(
            self::TITLE,
            self::URI,
            new Status(self::STATUS)
        );
        $videoYoutube
        ->setDatePublication(new DateTime(self::DATE_PUBLICATION))
        ->setDescription(self::DESCRIPTION)
        ->setId(self::ID)
        ->setSourceId(self::SOURCE_ID)
        ;

        $this->assertEquals(self::DATE_PUBLICATION, $videoYoutube->getDatePublication()->format('Y-m-d H:i:s'));
        $this->assertEquals(self::DESCRIPTION, $videoYoutube->getDescription());
        $this->assertEquals(self::ID, $videoYoutube->getId());
        $this->assertEquals(self::SOURCE_ID, $videoYoutube->getSourceId());
        $this->assertEquals(self::STATUS, $videoYoutube->getStatus()->getValue());
        $this->assertEquals(self::TITLE, $videoYoutube->getTitle());
        $this->assertEquals(self::URI, $videoYoutube->getUri());
    }
}
