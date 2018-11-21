<?php
namespace Ciebit\Videos\Factories;

use Exception;
use Ciebit\Videos\Facebook;
use Ciebit\Videos\File;
use Ciebit\Videos\Status;
use Ciebit\Videos\Video;
use Ciebit\Videos\Youtube;

class Creator
{
    private $entities; # arrray;
    private $data; # array

    public function __construct()
    {
        $this->entities = [
            Facebook::getType() => Facebook::class,
            File::getType() => File::class,
            Youtube::getType() => Youtube::class,
        ];
    }

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @throw Exception
    */
    public function create(string $type): Video
    {
        if (! isset($this->entities[$type])) {
            throw new Exception("ciebit.videos.factories.type_invalid", 1);
        }

        $data = $this->data;
        $video = new $this->entities[$type](
            $data['title'],
            $data['uri'],
            $data['status']
        );

        $data['description'] ?? $video->setDescription($data['description']);
        $data['id'] ?? $video->setId($data['id']);
        $data['sourceId'] ?? $video->setSourceId($data['sourceId']);
        $data['datePublication'] ?? $video->setDatePublication($data['datePublication']);

        return $video;
    }
}
