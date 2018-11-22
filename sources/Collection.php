<?php
namespace Ciebit\Videos;

use Ciebit\Videos\Video;
use ArrayIterator;
use ArrayObject;
use Countable;
use IteratorAggregate;

class Collection implements Countable, IteratorAggregate
{
    private $items; #: ArrayObject

    public function __construct()
    {
        $this->items = new ArrayObject;
    }

    public function add(Video ...$videos): self
    {
        foreach ($videos as $video) {
            $this->items->append($video);
        }

        return $this;
    }

    public function getArrayObject(): ArrayObject
    {
        return clone $this->items;
    }

    public function getById(string $id): ?Video
    {
        foreach ($this->getIterator() as $video) {
            if ($id === $video->getId()) {
                return $video;
            }
        }

        return null;
    }

    public function getIterator(): ArrayIterator
    {
        return $this->items->getIterator();
    }

    public function count(): int
    {
        return $this->items->count();
    }
}
