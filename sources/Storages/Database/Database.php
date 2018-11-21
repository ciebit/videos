<?php
namespace Ciebit\Videos\Storages\Database;

use Ciebit\Videos\Collection;
use Ciebit\Videos\Video;

interface Database
{
    public function addFilterById(string $operator, string ...$id): self;

    public function addFilterBySourceId(string $operator, string ...$ids): self;

    public function addFilterByUri(string $operator, string ...$uri): self;

    public function findOne(): ?Video;
}
