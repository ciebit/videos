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
    /** @var array */
    private $data;

    /** @var array */
    private $entities;

    public function __construct()
    {
        $this->data = [
            'title' => '',
            'url' => '',
            'status' => 0
        ];
        $this->entities = [
            Facebook::getType() => Facebook::class,
            File::getType() => File::class,
            Youtube::getType() => Youtube::class,
        ];
    }

    public function setData(array $data): self
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    /**
     * @throws Exception
    */
    public function create(string $type): Video
    {
        if (! isset($this->entities[$type])) {
            throw new Exception("ciebit.videos.factories.type_invalid", 1);
        }

        $data = $this->data;
        $video = new $this->entities[$type](
            $data['title'],
            $data['url'],
            $data['status']
        );

        if(isset($data['description'])) {
            $video->setDescription($data['description']);
        }

        if(isset($data['id'])) {
            $video->setId($data['id']);
        }

        if(isset($data['coverId'])) {
            $video->setCoverId($data['coverId']);
        }

        if(isset($data['sourceId'])) {
            $video->setSourceId($data['sourceId']);
        }

        if(isset($data['datePublication'])) {
            $video->setDatePublication($data['datePublication']);
        }

        if(isset($data['duration'])) {
            $video->setDuration($data['duration']);
        }

        return $video;
    }
}
