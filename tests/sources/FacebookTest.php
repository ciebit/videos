<?php
namespace Ciebit\VideosTests;

use DateTime;
use Ciebit\Videos\Facebook;
use Ciebit\Videos\Status;
use Ciebit\VideosTests\Video as VideosTests;
use PHPUnit\Framework\TestCase;

class FacebookTest extends VideosTests
{
    public function testCreateFromManual(): void
    {
        $videoFacebook = new Facebook(
            self::TITLE,
            self::URI,
            new Status(self::STATUS)
        );
        $videoFacebook
        ->setDatePublication(new DateTime(self::DATE_PUBLICATION))
        ->setDescription(self::DESCRIPTION)
        ->setId(self::ID)
        ->setSourceId(self::SOURCE_ID)
        ;

        $this->assertEquals(self::DATE_PUBLICATION, $videoFacebook->getDatePublication()->format('Y-m-d H:i:s'));
        $this->assertEquals(self::DESCRIPTION, $videoFacebook->getDescription());
        $this->assertEquals(self::ID, $videoFacebook->getId());
        $this->assertEquals(self::SOURCE_ID, $videoFacebook->getSourceId());
        $this->assertEquals(self::STATUS, $videoFacebook->getStatus()->getValue());
        $this->assertEquals(self::TITLE, $videoFacebook->getTitle());
        $this->assertEquals(self::URI, $videoFacebook->getUri());
    }
}
