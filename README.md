# ciebit/videos

Simple entity video and persistence.

## Example store

```php
use Ciebit\Videos\File;
use Ciebit\Videos\Status;
use Ciebit\Videos\Storages\Database\Sql;

$video = new File('Title Video', 'uri-video.mp4', Status::ACTIVE());

$pdo = new PDO('mysql:dbname=cb_videos;host=localhost;charset=utf8', 'root', '');
$videoStorage = new Sql($pdo);
$videoStorage->store($video);

```

## Example get

```php
use Ciebit\Videos\Storages\Database\Sql;

$pdo = new PDO('mysql:dbname=cb_videos;host=localhost;charset=utf8', 'root', '');
$videoStorage = new Sql($pdo);
$video = $videoStorage->addFilterById('=', '2')->find();

echo $video->getTitle();

```
