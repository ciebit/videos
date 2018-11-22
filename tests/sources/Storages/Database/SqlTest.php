<?php
namespace Ciebit\VideosTests\Storages\Database;

use ArrayObject;
use Ciebit\Videos\Collection;
use Ciebit\Videos\File;
use Ciebit\Videos\Status;
use Ciebit\Videos\Video;
use Ciebit\Videos\Storages\Database\Sql;
use Ciebit\VideosTests\Connection;

class SqlTest extends Connection
{
    public function getDatabase(): Sql
    {
        $pdo = $this->getPdo();
        return new Sql($pdo);
    }

    public function testFindOne(): void
    {
        $database = $this->getDatabase();
        $video = $database->findOne();
        $this->assertInstanceOf(Video::class, $video);
    }

    public function testFindAll(): void
    {
        $database = $this->getDatabase();
        $videos = $database->findAll();
        $this->assertInstanceOf(Collection::class, $videos);
        $this->assertCount(5, $videos);
    }

    public function testFilterById(): void
    {
        $id = 4;
        $database = $this->getDatabase();
        $video = $database->addFilterById('=', (string) $id)->findOne();
        $this->assertInstanceOf(File::class, $video);
        $this->assertEquals((string) $id, $video->getId());
        $this->assertEquals('Title Video 04', $video->getTitle());
        $this->assertEquals('Description video 04', $video->getDescription());
        $this->assertEquals('uri-video-04', $video->getUri());
        $this->assertEquals('2018-11-08 17:29:13', $video->getDatePublication()->format('Y-m-d H:i:s'));
        $this->assertEquals(1, $video->getStatus()->getValue());
    }

    public function testFilterByMultipleId(): void
    {
        $database = $this->getDatabase();
        $videos = $database->addFilterById('=', 4, 5)->findAll();
        $this->assertCount(2, $videos);
    }

    public function testFilterBySource(): void
    {
        $database = $this->getDatabase();
        $videos = $database->addFilterBySource('=', File::getType())->findAll();
        $this->assertCount(3, $videos);
    }

    public function testFilterBySourceId(): void
    {
        $database = $this->getDatabase();
        $video = $database->addFilterBySourceId('=', '33')->findOne();
        $this->assertEquals('3', $video->getId());
    }

    public function testFilterByStatus(): void
    {
        $database = $this->getDatabase();
        $video = $database->addFilterByStatus('=', Status::DRAFT())->findOne();
        $this->assertEquals('4', $video->getId());
    }

    public function testFilterByTitle(): void
    {
        $database = $this->getDatabase();
        $video = $database->addFilterByTitle('=', 'Title Video 02')->findOne();
        $this->assertEquals('2', $video->getId());
    }

    public function testFilterByTitleSearch(): void
    {
        $database = $this->getDatabase();
        $videos = $database->addFilterByTitle('LIKE', 'Title Video%')->findAll();
        $this->assertCount(4, $videos);
    }

    public function testFilterByUri(): void
    {
        $database = $this->getDatabase();
        $video = $database->addFilterByUri('=', 'uri-video-02')->findOne();
        $this->assertEquals('2', $video->getId());
    }

    public function testOrderBy(): void
    {
        $database = $this->getDatabase();
        $video = $database->addOrderBy(Sql::FIELD_TITLE, 'DESC')->findOne();
        $this->assertEquals('4', $video->getId());
    }
}
