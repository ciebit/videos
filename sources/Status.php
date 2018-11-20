<?php
namespace Ciebit\Videos;

use MyClabs\Enum\Enum;

class Status extends Enum
{
    const ACTIVE = 3;
    const ANALYZE = 2;
    const DRAFT = 1;
    const INACTIVE = 4;
    const TRASH = 5;
}
