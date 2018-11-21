<?php
namespace Ciebit\Videos\Storages\Database;

use PDOStatement;

use function count;
use function implode;
use function is_array;
use function str_replace;
use function strlen;

abstract class SqlHelper
{
    private $bindList; # array
    private $limit; # ?int
    private $offset; # ?int
    private $orderBy; # array
    private $sqlFilters; # array
    private $sqlJoin; # array
    private $valueKey; # int

    protected function __construct()
    {
        $this->bindList = [];
        $this->orderBy = [];
        $this->sqlFilters = [];
        $this->sqlJoin = [];
        $this->valueKey = 0;
    }

    protected function addBind(string $key, int $type, $value): self
    {
        $this->bindList[] = [
            'key' => $key,
            'value' => $value,
            'type' => $type
        ];
        return $this;
    }

    protected function addFilterBy(string $field, int $type, string $operation, $value): self
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
            }
            $value = $value[0];
        } else {
            $key = $this->generateValueKey();
        }

        $sql = "{$field} {$operation} {$key}";
        $this->addSqlFilter($sql);
        return $this;
    }

    protected function addOrderBy(string $column, string $order = "ASC"): self
    {
        $this->orderBy[] = [$column, $order];
        return $this;
    }

    protected function addSqlFilter(string $sql): self
    {
        $this->sqlFilters[] = $sql;
        return $this;
    }

    protected function addSqlJoin(string $sql): self
    {
        $this->sqlJoin[] = $sql;
        return $this;
    }

    protected function bind(PDOStatement $statment): self
    {
        if (! is_array($this->bindList)) {
            return $this;
        }
        foreach ($this->bindList as $bind) {
            $statment->bindValue($bind['key'], $bind['value'], $bind['type']);
        }
        return $this;
    }

    protected function generateSqlFilters(): string
    {
        if (empty($this->filtersSql)) {
            return '1';
        }
        return implode(' AND ', $this->filtersSql);
    }

    private function generateSqlLimit(): string
    {
        $init = (int) $this->offset;
        $sql =
            $this->limit === null
            ? ''
            : "LIMIT {$init},{$this->limit}";
        return $sql;
    }

    protected function generateSqlJoin(): string
    {
        if (! is_array($this->sqlJoin)) {
            return '';
        }

        return implode(' ', $this->sqlJoin);
    }

    protected function generateSqlOrder(): string
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

    protected function setLimit(int $total): self
    {
        $this->limit = $total;
        return $this;
    }

    protected function setOffset(int $lineInit): self
    {
        $this->offset = $lineInit;
        return $this;
    }
}
