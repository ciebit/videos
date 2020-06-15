<?php
namespace Ciebit\Videos\Storages\Database;

use Ciebit\SqlHelper\Sql as SqlHelper;
use Ciebit\Videos\Collection;
use Ciebit\Videos\Status;
use Ciebit\Videos\Video;
use Ciebit\Videos\Factories\Creator;
use Ciebit\Videos\Storages\Database\Database;
use Ciebit\Videos\Storages\Storage;
use DateTime;
use Exception;
use PDO;

class Sql implements Database
{
    private const COLUMN_COVER_ID = 'cover_id';
    private const COLUMN_DATE_PUBLICATION = 'date_publication';
    private const COLUMN_DESCRIPTION = 'description';
    private const COLUMN_DURATION = 'duration';
    private const COLUMN_ID = 'id';
    private const COLUMN_SOURCE = 'type';
    private const COLUMN_SOURCE_ID = 'source_id';
    private const COLUMN_STATUS = 'status';
    private const COLUMN_TITLE = 'title';
    private const COLUMN_TYPE = 'type';
    private const COLUMN_URL = 'url';

    private PDO $pdo;
    private SqlHelper $sqlHelper;
    private string $table;
    private int $totalItemsOfLastFindWithoutLimitations;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->sqlHelper = new SqlHelper;
        $this->table = 'cb_videos';
        $this->totalItemsOfLastFindWithoutLimitations = 0;
    }

    public function __clone()
    {
        $this->sqlHelper = clone $this->sqlHelper;
    }

    private function addFilter(string $fieldName, int $type, string $operator, ...$value): self
    {
        $field = "`{$this->table}`.`{$fieldName}`";
        $this->sqlHelper->addFilterBy($field, $type, $operator, ...$value);
        return $this;
    }

    public function addFilterByDescription(string $operator, string ...$description): Storage
    {
        $this->addFilter(self::COLUMN_DESCRIPTION, PDO::PARAM_STR, $operator, ...$description);
        return $this;
    }

    public function addFilterById(string $operator, string ...$ids): Storage
    {
        $ids = array_map('intval', $ids);
        $this->addFilter(self::COLUMN_ID, PDO::PARAM_INT, $operator, ...$ids);
        return $this;
    }

    public function addFilterBySource(string $operator, string ...$source): Storage
    {
        $this->addFilter(self::COLUMN_TYPE, PDO::PARAM_STR, $operator, ...$source);
        return $this;
    }

    public function addFilterBySourceId(string $operator, string ...$ids): Storage
    {
        $this->addFilter(self::COLUMN_SOURCE_ID, PDO::PARAM_STR, $operator, ...$ids);
        return $this;
    }

    public function addFilterByStatus(string $operator, Status ...$status): Storage
    {
        $statusInt = array_map(function($status){
            return (int) $status->getValue();
        }, $status);
        $this->addFilter(self::COLUMN_STATUS, PDO::PARAM_INT, $operator, ...$statusInt);
        return $this;
    }

    public function addFilterByTitle(string $operator, string ...$title): Storage
    {
        $this->addFilter(self::COLUMN_TITLE, PDO::PARAM_STR, $operator, ...$title);
        return $this;
    }

    public function addFilterByUrl(string $operator, string ...$url): Storage
    {
        $this->addFilter(self::COLUMN_URL, PDO::PARAM_STR, $operator, ...$url);
        return $this;
    }

    public function addOrderBy(string $field, string $order = "ASC"): Storage
    {
        $this->sqlHelper->addOrderBy($field, $order);
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

        if ($data['cover_id'] != null) {
            $data['coverId'] = $data['cover_id'];
        }

        $data['status'] = new Status((int) $data['status']);

        return (new Creator)->setData($data)->create($data['type']);
    }

    private function getFields(): string
    {
        return "
            `{$this->table}`.`". self::COLUMN_COVER_ID ."`,
            `{$this->table}`.`". self::COLUMN_DATE_PUBLICATION ."`,
            `{$this->table}`.`". self::COLUMN_DESCRIPTION ."`,
            `{$this->table}`.`". self::COLUMN_DURATION ."`,
            `{$this->table}`.`". self::COLUMN_ID ."`,
            `{$this->table}`.`". self::COLUMN_SOURCE_ID ."`,
            `{$this->table}`.`". self::COLUMN_STATUS ."`,
            `{$this->table}`.`". self::COLUMN_TITLE ."`,
            `{$this->table}`.`". self::COLUMN_URL ."`,
            `{$this->table}`.`". self::COLUMN_TYPE ."`
        ";
    }

    public function getTotalItemsOfLastFindWithoutLimitations(): int
    {
        return $this->totalItemsOfLastFindWithoutLimitations;
    }

    /**
     * @throws Exception
    */
    public function findAll(): Collection
    {
        $statement = $this->pdo->prepare(
            "SELECT SQL_CALC_FOUND_ROWS
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

        $this->updateTotalItemsWithoutFilters();

        $collection = new Collection;

        $videoCollectionData = $statement->fetchAll(PDO::FETCH_ASSOC);

        if ($videoCollectionData == false) {
            return $collection;
        }

        foreach ($videoCollectionData as $videoData) {
            $collection->add(
                $this->createVideo($videoData)
            );
        }

        return $collection;
    }

    public function findOne(): ?Video
    {
        $storage = clone $this;
        $videoCollection = $storage->setLimit(1)->findAll();

        if (count($videoCollection) == 0) {
            return null;
        }

        return $videoCollection->getArrayObject()->offsetGet(0);
    }

    public function setLimit(int $total): Storage
    {
        $this->sqlHelper->setLimit($total);
        return $this;
    }

    public function setOffset(int $offset): Storage
    {
        $this->sqlHelper->setOffset($offset);
        return $this;
    }

    public function setTable(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function store(Video $video): string
    {
        $fields = implode('`,`', [
            self::COLUMN_COVER_ID,
            self::COLUMN_DATE_PUBLICATION,
            self::COLUMN_DESCRIPTION,
            self::COLUMN_DURATION,
            self::COLUMN_SOURCE_ID,
            self::COLUMN_STATUS,
            self::COLUMN_TITLE,
            self::COLUMN_TYPE,
            self::COLUMN_URL
        ]);

        $sqlQuery = "
            INSERT INTO `{$this->table}` 
            (`{$fields}`) 
            VALUES 
            (
                :cover_id, :date_publication, :description, 
                :duration, :source_id, :status, 
                :title, :type, :url
            )
        ";

        $statement = $this->pdo->prepare($sqlQuery);

        $statement->bindValue(':cover_id', $video->getCoverId(), PDO::PARAM_INT);
        $statement->bindValue(':date_publication', $video->getDatePublication()->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $statement->bindValue(':description', $video->getDescription(), PDO::PARAM_STR);
        $statement->bindValue(':duration', $video->getDuration(), PDO::PARAM_INT);
        $statement->bindValue(':source_id', $video->getSourceId(), PDO::PARAM_INT);
        $statement->bindValue(':status', $video->getStatus()->getValue(), PDO::PARAM_INT);
        $statement->bindValue(':title', $video->getTitle(), PDO::PARAM_STR);
        $statement->bindValue(':type', $video->getType(), PDO::PARAM_STR);
        $statement->bindValue(':url', $video->getUrl(), PDO::PARAM_STR);

        if ($statement->execute() === false) {
            throw new Exception('ciebit.videos.storages.database.storage-error', 3);
        }

        return $this->pdo->lastInsertId();
    }

    public function update(Video $video): self
    {
        $columns = [
            self::COLUMN_COVER_ID,
            self::COLUMN_DATE_PUBLICATION,
            self::COLUMN_DESCRIPTION,
            self::COLUMN_DURATION,
            self::COLUMN_SOURCE_ID,
            self::COLUMN_STATUS,
            self::COLUMN_TITLE,
            self::COLUMN_TYPE,
            self::COLUMN_URL
        ];
        $fieldId = self::COLUMN_ID;
        $fieldsSql = '';

        foreach($columns as $column) {
            $fieldsSql.= "`{$column}` = :{$column},";
        }
        $fieldsSql = substr($fieldsSql, 0, -1);

        $sqlQuery = "UPDATE {$this->table} SET {$fieldsSql} WHERE `{$fieldId}` = :id";

        $statement = $this->pdo->prepare($sqlQuery);

        $statement->bindValue(':'.self::COLUMN_COVER_ID, $video->getCoverId(), PDO::PARAM_INT);
        $statement->bindValue(':'.self::COLUMN_DATE_PUBLICATION, $video->getDatePublication()->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $statement->bindValue(':'.self::COLUMN_DESCRIPTION, $video->getDescription(), PDO::PARAM_STR);
        $statement->bindValue(':'.self::COLUMN_DURATION, $video->getDuration(), PDO::PARAM_INT);
        $statement->bindValue(':'.self::COLUMN_SOURCE_ID, $video->getSourceId(), PDO::PARAM_INT);
        $statement->bindValue(':'.self::COLUMN_STATUS, $video->getStatus()->getValue(), PDO::PARAM_INT);
        $statement->bindValue(':'.self::COLUMN_TITLE, $video->getTitle(), PDO::PARAM_STR);
        $statement->bindValue(':'.self::COLUMN_TYPE, $video->getType(), PDO::PARAM_STR);
        $statement->bindValue(':'.self::COLUMN_URL, $video->getUrl(), PDO::PARAM_STR);
        $statement->bindValue(':id', $video->getId(), PDO::PARAM_INT);

        if ($statement->execute() === false) {
            throw new Exception('ciebit.videos.storages.database.update-error', 4);
        }

        return $this;
    }

    private function updateTotalItemsWithoutFilters(): self
    {
        $this->totalItemsOfLastFindWithoutLimitations = $this->pdo->query('SELECT FOUND_ROWS()')->fetchColumn();
        return $this;
    }
}
