<?php
namespace Ciebit\Videos;

use Ciebit\Videos\Status;
use DateTime;

abstract class Video
{
    /** @var string */
    private $coverId;

    /** @var DateTime */
    private $datePublication;

    /** @var string */
    private $description;

    /** @var int in seconds */
    private $duration;

    /** @var string */
    private $id;

    /** @var string */
    private $sourceId;

    /** @var Status */
    private $status;

    /** @var string */
    private $title;

    /** @var string */
    private $url;

    public function __construct(string $title, string $url, Status $status)
    {
        $this->coverId = '';
        $this->datePublication = new DateTime;
        $this->description = '';
        $this->duration = 0;
        $this->id = '';
        $this->sourceId = '';
        $this->status = $status;
        $this->title = $title;
        $this->url = $url;
    }

    abstract public static function getType(): string;

    public function getCoverId(): string
    {
        return $this->coverId;
    }

    public function getDatePublication(): DateTime
    {
        return $this->datePublication;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /** @var int in seconds */
    public function getDuration(): int
    {
        return $this->duration;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSourceId(): string
    {
        return $this->sourceId;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setCoverId(string $id): self
    {
        $this->coverId = $id;
        return $this;
    }

    public function setDatePublication(DateTime $datetime): self
    {
        $this->datePublication = $datetime;
        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function setDuration(int $seconds): self
    {
        $this->duration = $seconds;
        return $this;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setSourceId(string $sourceId): self
    {
        $this->sourceId = $sourceId;
        return $this;
    }

    public function setStatus(Status $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }
}
