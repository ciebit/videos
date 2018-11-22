<?php
namespace Ciebit\Videos\Storages\Database;

use Ciebit\Videos\Collection;
use Ciebit\Videos\Video;
use Ciebit\Videos\Status;

interface Database
{
    public function addFilterByDescription(string $operator, string ...$description): self;

    public function addFilterById(string $operator, string ...$id): self;

    public function addFilterBySource(string $operator, string ...$source): self;

    public function addFilterBySourceId(string $operator, string ...$ids): self;

    public function addFilterByStatus(string $operator, Status ...$status): self;

    public function addFilterByUri(string $operator, string ...$uri): self;

    public function addFilterByTitle(string $operator, string ...$title): self;

    public function addOrderBy(string $column, string $order = "ASC"): self;

    public function getTotalItems(): int;

    public function findAll(): Collection;

    public function findOne(): ?Video;

    public function setLimit(int $limit): self;

    public function setOffset(int $offset): self;
}
