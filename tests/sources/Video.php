<?php
namespace Ciebit\VideosTests;

use Ciebit\Videos\Status;
use PHPUnit\Framework\TestCase;

abstract class Video extends TestCase
{
    protected const COVER_ID = '22';
    protected const DATE_PUBLICATION = '2018-11-20 14:17:22';
    protected const DESCRIPTION = 'Description Teste';
    protected const DURATION = 80;
    protected const ID = '2';
    protected const SOURCE_ID = '33';
    protected const STATUS = STATUS::ACTIVE;
    protected const TITLE = 'Title Test';
    protected const URL = 'url-test';
}
