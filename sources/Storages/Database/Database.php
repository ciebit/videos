<?php
namespace Ciebit\Videos\Storages\Database;

use Ciebit\Videos\Collection;
use Ciebit\Videos\Video;

interface Database
{
    public function addFilterById(string $operator, string ...$id): self;

    public function addFilterBySource(string $operator, string ...$source): self;

    public function addFilterBySourceId(string $operator, string ...$ids): self;

    public function addFilterByUri(string $operator, string ...$uri): self;

    public function addOrderBy(string $column, string $order = "ASC"): self;

    public function findAll(): Collection;

    public function findOne(): ?Video;

    public function setLimit(int $limit): self;

    public function setOffset(int $offset): self;
}
