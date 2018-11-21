<?php
namespace Ciebit\Videos\Storages\Database;

use PDO;
use DateTime;
use Ciebit\Videos\Status;
use Ciebit\Videos\Video;
use Ciebit\Videos\Factories\Creator;
use Ciebit\Videos\Storages\Database\Database;
use Ciebit\Videos\Storages\Database\SqlHelper;

class Sql extends SqlHelper implements Database
{
    private $pdo; # PDO
    private $table; # string

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->table = 'cb_videos';
    }

    public function addFilterById(string $operator, string ...$ids): Database
    {
        $this->addFilterBy("`{$this->table}`.`id`", PDO::PARAM_STR, $operator, $ids);
        return $this;
    }

    private function createVideo(array $data): Video
    {
        if ($data['date_publication'] != null) {
            $data['datePublication'] = new DateTime($data['date_publication']);
        }

        if ($data['source_id'] != null) {
            $data['sourceId'] = $data['source_id'];
        }

        $data['status'] = new Status((int) $data['status']);

        return (new Creator)->setData($data)->create($data['type']);
    }

    private function getFields(): string
    {
        return "
            `{$this->table}`.`date_publication`,
            `{$this->table}`.`description`,
            `{$this->table}`.`id`,
            `{$this->table}`.`source_id`,
            `{$this->table}`.`status`,
            `{$this->table}`.`title`,
            `{$this->table}`.`uri`,
            `{$this->table}`.`type`
        ";
    }

    public function findOne(): ?Video
    {
        $statement = $this->pdo->prepare(
            "SELECT
            {$this->getFields()}
            FROM {$this->table}
            {$this->generateSqlJoin()}
            WHERE {$this->generateSqlFilters()}
            {$this->generateSqlOrder()}
            LIMIT 1"
        );

        $this->bind($statement);

        if ($statement->execute() === false) {
            throw new Exception('ciebit.videos.storages.database.get_error', 2);
        }

        $videoData = $statement->fetch(PDO::FETCH_ASSOC);

        if ($videoData == false) {
            return null;
        }

        return $this->createVideo($videoData);
    }

    public function setTable(string $table): self
    {
        $this->table = $table;
        return $this;
    }
}
