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
use PDO;

class Sql implements Database
{
    /** @var string */
    private const COLUMN_COVER_ID = 'cover_id';

    /** @var string */
    private const COLUMN_DATE_PUBLICATION = 'date_publication';

    /** @var string */
    private const COLUMN_DESCRIPTION = 'description';

    /** @var string */
    private const COLUMN_DURATION = 'duration';

    /** @var string */
    private const COLUMN_ID = 'id';

    /** @var string */
    private const COLUMN_SOURCE = 'type';

    /** @var string */
    private const COLUMN_SOURCE_ID = 'source_id';

    /** @var string */
    private const COLUMN_STATUS = 'status';

    /** @var string */
    private const COLUMN_TITLE = 'title';

    /** @var string */
    private const COLUMN_TYPE = 'type';

    /** @var string */
    private const COLUMN_URL = 'url';

    /** @var PDO */
    private $pdo;

    /** @var SqlHelper */
    private $sqlHelper;

    /** @var string */
    private $table;

    /** @var int */
    private $totalItemsOfLastFindWithoutLimitations;

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

    private function updateTotalItemsWithoutFilters(): self
    {
        $this->totalItemsOfLastFindWithoutLimitations = $this->pdo->query('SELECT FOUND_ROWS()')->fetchColumn();
        return $this;
    }
}
