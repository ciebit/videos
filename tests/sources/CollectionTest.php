<?php
namespace Ciebit\VideosTests;

use ArrayIterator;
use ArrayObject;
use DateTime;
use Ciebit\Videos\Collection;
use Ciebit\Videos\Status;
use Ciebit\Videos\Video;
use Ciebit\Videos\File as VideoFile;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    private $countCreateVideo = 0;

    private function getVideo(): Video
    {
        $number = $this->countCreateVideo++;
        return new VideoFile('Title '.$number, 'uri-'.$number, Status::ACTIVE());
    }

    public function testCreateFromManual(): void
    {
        $id = '3';

        $collection = new Collection;
        $collection->add($this->getVideo());
        $collection->add($this->getVideo(), $this->getVideo()->setId($id.''));
        $collection->add($this->getVideo());

        $this->assertCount(4, $collection);
        $this->assertEquals($id, $collection->getById($id.'')->getId());
        $this->assertInstanceOf(ArrayObject::class, $collection->getArrayObject());
        $this->assertInstanceOf(ArrayIterator::class, $collection->getIterator());
    }
}
