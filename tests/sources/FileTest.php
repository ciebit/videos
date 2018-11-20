<?php
namespace Ciebit\VideosTests;

use DateTime;
use Ciebit\Videos\File;
use Ciebit\Videos\Status;
use Ciebit\VideosTests\Video as VideosData;

class FileTest extends VideosData
{
    public function testCreateFromManual(): void
    {
        $videoFile = new File(
            self::TITLE,
            self::URI,
            new Status(self::STATUS)
        );
        $videoFile
        ->setDatePublication(new DateTime(self::DATE_PUBLICATION))
        ->setDescription(self::DESCRIPTION)
        ->setId(self::ID)
        ->setSourceId(self::SOURCE_ID)
        ;

        $this->assertEquals(self::DATE_PUBLICATION, $videoFile->getDatePublication()->format('Y-m-d H:i:s'));
        $this->assertEquals(self::DESCRIPTION, $videoFile->getDescription());
        $this->assertEquals(self::ID, $videoFile->getId());
        $this->assertEquals(self::SOURCE_ID, $videoFile->getSourceId());
        $this->assertEquals(self::STATUS, $videoFile->getStatus()->getValue());
        $this->assertEquals(self::TITLE, $videoFile->getTitle());
        $this->assertEquals(self::URI, $videoFile->getUri());
    }
}
