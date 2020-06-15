<?php
namespace Ciebit\Videos\Storages;

use Ciebit\Videos\Collection;
use Ciebit\Videos\Video;
use Ciebit\Videos\Status;

interface Storage
{
    public const FIELD_DATE_PUBLICATION = 'date_publication';
    public const FIELD_DESCRIPTION = 'description';
    public const FIELD_ID = 'id';
    public const FIELD_SOURCE = 'type';
    public const FIELD_SOURCE_ID = 'source_id';
    public const FIELD_STATUS = 'status';
    public const FIELD_TITLE = 'title';
    public const FIELD_TYPE = 'type';
    public const FIELD_URL = 'url';

    public function addFilterByDescription(string $operator, string ...$description): self;

    public function addFilterById(string $operator, string ...$id): self;

    public function addFilterBySource(string $operator, string ...$source): self;

    public function addFilterBySourceId(string $operator, string ...$ids): self;

    public function addFilterByStatus(string $operator, Status ...$status): self;

    public function addFilterByUrl(string $operator, string ...$url): self;

    public function addFilterByTitle(string $operator, string ...$title): self;

    public function addOrderBy(string $column, string $order = "ASC"): self;

    public function getTotalItemsOfLastFindWithoutLimitations(): int;

    public function findAll(): Collection;

    public function findOne(): ?Video;

    public function setLimit(int $limit): self;

    public function setOffset(int $offset): self;

    public function store(Video $video): string;

    public function update(Video $video): self;
}
