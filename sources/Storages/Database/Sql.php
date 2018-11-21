<?php
namespace Ciebit\Videos\Storages\Database;

use PDO;
use DateTime;
use Ciebit\Videos\Collection;
use Ciebit\Videos\Status;
use Ciebit\Videos\Video;
use Ciebit\Videos\Factories\Creator;
use Ciebit\Videos\Storages\Database\Database;
use Ciebit\Videos\Storages\Database\SqlHelper;

class Sql implements Database
{
    public const FIELD_DATE_PUBLICATION = 'date_publication';
    public const FIELD_DESCRIPTION = 'description';
    public const FIELD_ID = 'id';
    public const FIELD_SOURCE = 'type';
    public const FIELD_SOURCE_ID = 'source_id';
    public const FIELD_STATUS = 'status';
    public const FIELD_TITLE = 'title';
    public const FIELD_URI = 'uri';

    private $pdo; # PDO
    private $table; # string
    private $sqlHelper; # SqlHelper

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->sqlHelper = new SqlHelper;
        $this->table = 'cb_videos';
    }

    public function addFilterById(string $operator, string ...$ids): Database
    {
        $this->sqlHelper->addFilterBy("`{$this->table}`.`id`", PDO::PARAM_STR, $operator, $ids);
        return $this;
    }

    public function addFilterBySource(string $operator, string ...$source): Database
    {
        $this->sqlHelper->addFilterBy("`{$this->table}`.`type`", PDO::PARAM_STR, $operator, $source);
        return $this;
    }

    public function addFilterBySourceId(string $operator, string ...$ids): Database
    {
        $this->sqlHelper->addFilterBy("`{$this->table}`.`source_id`", PDO::PARAM_STR, $operator, $ids);
        return $this;
    }

    public function addFilterByStatus(string $operator, Status ...$status): Database
    {
        $field = self::FIELD_STATUS;
        $this->sqlHelper->addFilterBy("`{$this->table}`.`{$field}`", PDO::PARAM_INT, $operator, $status);
        return $this;
    }

    public function addFilterByUri(string $operator, string ...$uri): Database
    {
        $this->sqlHelper->addFilterBy("`{$this->table}`.`uri`", PDO::PARAM_STR, $operator, $uri);
        return $this;
    }

    public function addOrderBy(string $column, string $order = "ASC"): Database
    {
        $this->sqlHelper->addOrderBy("`{$this->table}`.`{$column}`", $order);
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

    /**
     * @throw Exception
    */
    public function findOne(): ?Video
    {
        $statement = $this->pdo->prepare(
            "SELECT
            {$this->getFields()}
            FROM {$this->table}
            {$this->sqlHelper->generateSqlJoin()}
            WHERE {$this->sqlHelper->generateSqlFilters()}
            {$this->sqlHelper->generateSqlOrder()}
            LIMIT 1"
        );

        $this->sqlHelper->bind($statement);

        if ($statement->execute() === false) {
            throw new Exception('ciebit.videos.storages.database.find_error', 2);
        }

        $videoData = $statement->fetch(PDO::FETCH_ASSOC);

        if ($videoData == false) {
            return null;
        }

        return $this->createVideo($videoData);
    }

    /**
     * @throw Exception
    */
    public function findAll(): Collection
    {
        $statement = $this->pdo->prepare(
            "SELECT
            {$this->getFields()}
            FROM {$this->table}
            {$this->sqlHelper->generateSqlJoin()}
            WHERE {$this->sqlHelper->generateSqlFilters()}
            {$this->sqlHelper->generateSqlOrder()}
            {$this->sqlHelper->generateSqlLimit()}"
        );

        $this->sqlHelper->bind($statement);

        if ($statement->execute() === false) {
            throw new Exception('ciebit.videos.storages.database.find_error', 2);
        }

        $videoCollectionData = $statement->fetchAll(PDO::FETCH_ASSOC);

        if ($videoCollectionData == false) {
            return null;
        }

        $collection = new Collection;

        foreach ($videoCollectionData as $videoData) {
            $collection->add(
                $this->createVideo($videoData)
            );
        }

        return $collection;
    }

    public function setLimit(int $total): Database
    {
        $this->sqlHelper->setLimit($total);
        return $this;
    }

    public function setOffset(int $offset): Database
    {
        $this->sqlHelper->setOffset($offset);
        return $this;
    }

    public function setTable(string $table): self
    {
        $this->table = $table;
        return $this;
    }
}
