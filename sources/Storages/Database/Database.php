<?php
namespace Ciebit\Videos\Storages\Database;

use Ciebit\Videos\Collection;
use Ciebit\Videos\Video;

interface Database
{
    public function addFilterById(string $operator, string ...$id): self;

    public function findOne(): ?Video;
}
