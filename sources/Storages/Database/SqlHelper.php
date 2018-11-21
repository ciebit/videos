<?php
namespace Ciebit\Videos\Storages\Database;

use PDOStatement;

use function count;
use function implode;
use function is_array;
use function str_replace;
use function strlen;

class SqlHelper
{
    private $bindList; # array
    private $limit; # ?int
    private $offset; # ?int
    private $orderBy; # array
    private $sqlFilters; # array
    private $sqlJoin; # array
    private $valueKey; # int

    public function __construct()
    {
        $this->bindList = [];
        $this->orderBy = [];
        $this->sqlFilters = [];
        $this->sqlJoin = [];
        $this->valueKey = 0;
    }

    public function addBind(string $key, int $type, $value): self
    {
        $this->bindList[] = [
            'key' => $key,
            'value' => $value,
            'type' => $type
        ];
        return $this;
    }

    public function addFilterBy(string $field, int $type, string $operation, $value): self
    {
        if (is_array($value)) {
            if (count($value) > 1) {
                $operator = str_replace(['=', '!='], ['IN', 'NOT IN'], $operator);
                $keys = [];
                foreach ($value as $valueItem) {
                    $key = $this->generateValueKey();
                    $keys[] = $key;
                    $this->addBind($key, $type, $valueItem);
                }
                $key = '('. implode(',', $keys) .')';
            } else {
                $value = $value[0];
                $key = $this->generateValueKey();
                $this->addBind($key, $type, $value);
            }
        } else {
            $key = $this->generateValueKey();
            $this->addBind($key, $type, $value);
        }

        $sql = "{$field} {$operation} {$key}";
        $this->addSqlFilter($sql);
        return $this;
    }

    public function addOrderBy(string $column, string $order = "ASC"): self
    {
        $this->orderBy[] = [$column, $order];
        return $this;
    }

    public function addSqlFilter(string $sql): self
    {
        $this->sqlFilters[] = $sql;
        return $this;
    }

    public function addSqlJoin(string $sql): self
    {
        $this->sqlJoin[] = $sql;
        return $this;
    }

    public function bind(PDOStatement $statment): self
    {
        if (! is_array($this->bindList)) {
            return $this;
        }
        foreach ($this->bindList as $bind) {
            $statment->bindValue($bind['key'], $bind['value'], $bind['type']);
        }
        return $this;
    }

    public function generateSqlFilters(): string
    {
        if (empty($this->sqlFilters)) {
            return '1';
        }
        return implode(' AND ', $this->sqlFilters);
    }

    public function generateSqlLimit(): string
    {
        $init = (int) $this->offset;
        $sql =
            $this->limit === null
            ? ''
            : "LIMIT {$init},{$this->limit}";
        return $sql;
    }

    public function generateSqlJoin(): string
    {
        if (! is_array($this->sqlJoin)) {
            return '';
        }

        return implode(' ', $this->sqlJoin);
    }

    public function generateSqlOrder(): string
    {
        if (empty($this->orderBy)) {
            return '';
        }
        $array = array_map(function($item) {
            return implode(" ", $item);
        }, $this->orderBy);

        return "ORDER BY " . implode(', ', $array);
    }

    private function generateValueKey(): string
    {
        return ':value_'. $this->valueKey++;
    }

    public function setLimit(int $total): self
    {
        $this->limit = $total;
        return $this;
    }

    public function setOffset(int $lineInit): self
    {
        $this->offset = $lineInit;
        return $this;
    }
}
