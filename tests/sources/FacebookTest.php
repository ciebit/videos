<?php
namespace Ciebit\VideosTests;

use DateTime;
use Ciebit\Videos\Facebook;
use Ciebit\Videos\Status;
use Ciebit\VideosTests\Video as VideosTests;

class FacebookTest extends VideosTests
{
    public function testCreateFromManual(): void
    {
        $videoFacebook = new Facebook(
            self::TITLE,
            self::URL,
            new Status(self::STATUS)
        );
        $videoFacebook
        ->setDatePublication(new DateTime(self::DATE_PUBLICATION))
        ->setDescription(self::DESCRIPTION)
        ->setId(self::ID)
        ->setCoverId(self::COVER_ID)
        ->setDuration(self::DURATION)
        ->setSourceId(self::SOURCE_ID)
        ;

        $this->assertEquals(self::DATE_PUBLICATION, $videoFacebook->getDatePublication()->format('Y-m-d H:i:s'));
        $this->assertEquals(self::DESCRIPTION, $videoFacebook->getDescription());
        $this->assertEquals(self::ID, $videoFacebook->getId());
        $this->assertEquals(self::SOURCE_ID, $videoFacebook->getSourceId());
        $this->assertEquals(self::STATUS, $videoFacebook->getStatus()->getValue());
        $this->assertEquals(self::TITLE, $videoFacebook->getTitle());
        $this->assertEquals(self::URL, $videoFacebook->getUrL());
        $this->assertEquals(Facebook::getType(), $videoFacebook->getType());
    }
}
