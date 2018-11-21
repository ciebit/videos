<?php
namespace Ciebit\Videos\Storages\Database;

use Ciebit\Videos\Collection;
use Ciebit\Videos\Video;

interface Database
{
    public function findOne(): ?Video;
}
