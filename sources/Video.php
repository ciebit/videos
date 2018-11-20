<?php
namespace Ciebit\Videos;

use Ciebit\Status;
use DateTime;

abstract class Video
{
    private $datePublication; # DateTime
    private $description; # string
    private $id; # string
    private $sourceId; # string
    private $status; # Status
    private $title; # string
    private $uri; # string

    public function __construct(string $title, string $uri, Status $status)
    {
        $this->datePublication = new DateTime;
        $this->description = '';
        $this->id = '';
        $this->sourceId = '';
        $this->status = $status;
        $this->title = $title;
        $this->uri = $uri;
    }

    abstract private function getType(): string;

    public function getDatePublication(): DateTime
    {
        return $this->datePublication;
    }

    public function getDescription(): string
    {
        return $this->description;
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

    public function getUri(): string
    {
        return $this->uri;
    }

    public function setDatePublication(DateTime $datetime): self
    {
        $this->datePublication = $datetime;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
    }

    public function setSourceId(string $sourceId): self
    {
        $this->sourceId = $sourceId;
    }

    public function setStatus(Status $status): self
    {
        $this->status = $status;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
    }

    public function setUri(string $uri): self
    {
        $this->uri = $uri;
    }
}
