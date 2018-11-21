<?php
namespace Ciebit\VideosTests\Storages\Database;

use ArrayObject;
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
}
