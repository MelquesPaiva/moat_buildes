<?php

namespace Source\Core;

use Exception;
use PDOException;
use Source\Support\Message;
use stdClass;

/**
 * Class Model
 * @package Source\Core
 */
abstract class Model
{
    /** @var object|null */
    protected $data;

    /** @var \PDOException|null */
    protected $fail;

    /** @var Message|null */
    protected $message;

    /** @var string */
    protected $query;

    /** @var array */
    protected array $params = [];

    /** @var string */
    protected $order;

    /** @var int */
    protected $limit;

    /** @var int */
    protected $offset;

    /** @var string $group */
    protected string $group = "";

    /** @var string $entity database table */
    protected $entity;

    /** @var array $protected no update or create */
    protected $protected;

    /** @var array $entity database table */
    protected $required;

    /** @var string */
    protected string $join = "";

    /**
     * Model constructor.
     * @param string $entity database table name
     * @param array $protected table protected columns
     * @param array $required table required columns
     */
    public function __construct(string $entity, array $protected, array $required)
    {
        $this->entity = $entity;
        $this->protected = array_merge($protected, ['created_at', "updated_at"]);
        $this->required = $required;
        $this->message = new Message();
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        if (empty($this->data)) {
            $this->data = new stdClass();
        }

        $this->data->$name = $value;
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data->$name);
    }

    /**
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        return ($this->data->$name ?? null);
    }

    /**
     * @return null|object
     */
    public function data(): ?object
    {
        return $this->data;
    }

    /**
     * @return PDOException|null
     */
    public function fail(): ?PDOException
    {
        return $this->fail;
    }

    /**
     * @return Message|null
     */
    public function message(): ?Message
    {
        return $this->message;
    }

    /**
     * @param null|string $terms
     * @param null|string $params
     * @param string $columns
     * @return Model|mixed
     */
    public function find(?string $terms = null, ?string $params = null, string $columns = "*")
    {
        if ($terms) {
            $this->query = "SELECT {$columns} FROM {$this->entity} {$this->join} WHERE {$terms}";
            parse_str($params, $this->params);
            return $this;
        }

        $this->query = "SELECT {$columns} FROM {$this->entity}";
        return $this;
    }

    /**
     * Return a list of registers
     *
     * @return array|null
     */
    public function findAll(): ?array
    {
        return $this->find()->fetchAll();
    }

    /**
     * @param string $column
     * @param mixed $value
     * @param string $returnColumns
     * @return Model|null
     */
    public function findOneBy(string $column, $value, string $returnColumns = "*"): ?Model
    {
        return $this->find("{$column} = :{$column}", "{$column}={$value}", $returnColumns)->fetch();
    }

    /**
     * @param int $id
     * @param string $columns
     * @return null|mixed|Model
     */
    public function findOneById(int $id, string $columns = "*"): ?Model
    {
        $find = $this->find("id = :id", "id={$id}", $columns);
        return $find->fetch();
    }

    /**
     * @param string $columnOrder
     * @return Model
     */
    public function order(string $columnOrder): Model
    {
        $this->order = " ORDER BY {$columnOrder}";
        return $this;
    }

    /**
     * @param int $limit
     * @return Model
     */
    public function limit(int $limit): Model
    {
        $this->limit = " LIMIT {$limit}";
        return $this;
    }

    /**
     * @param int $offset
     * @return Model
     */
    public function offset(int $offset): Model
    {
        $this->offset = " OFFSET {$offset}";
        return $this;
    }

    /**
     * @param string $group
     * @return Model
     */
    public function group(string $group): Model
    {
        $this->group = " GROUP BY {$group}";
        return $this;
    }

    /**
     * @param string $leftJoin
     * @return Model
     */
    public function leftJoin(string $leftJoin): Model
    {
        $this->join .= " LEFT JOIN {$leftJoin}";
        return $this;
    }

    /**
     * @param string $rightJoin
     * @return Model
     */
    public function rightJoin(string $rightJoin): Model
    {
        $this->join .= " RIGHT JOIN {$rightJoin}";
        return $this;
    }

    /**
     * @param string $innerJoin
     * @return Model
     */
    public function innerJoin(string $innerJoin): Model
    {
        $this->join .= " INNER JOIN {$innerJoin}";
        return $this;
    }

    /**
     * @param bool $all
     * @return null|array|mixed|Model
     */
    public function fetch(bool $all = false)
    {
        try {
            $stmt = Connect::getInstance()->prepare(
                $this->query . $this->group .  $this->order . $this->limit . $this->offset
            );
            $stmt->execute($this->params);

            if (!$stmt->rowCount()) {
                return null;
            }

            if ($all) {
                return $stmt->fetchAll(\PDO::FETCH_CLASS, static::class);
            }

            return $stmt->fetchObject(static::class);
        } catch (PDOException $exception) {
            $this->fail = $exception;
            return null;
        }
    }

    /**
     * @return null|array|mixed|Model
     */
    public function fetchAll()
    {
        return $this->fetch(true);
    }

    /**
     * @param string $key
     * @return int
     */
    public function count(string $key = "id"): int
    {
        $stmt = Connect::getInstance()->prepare($this->query);
        $stmt->execute($this->params);
        return $stmt->rowCount();
    }

    /**
     * @param array $data
     * @return int|null
     */
    protected function create(array $data): ?int
    {
        try {
            $columns = implode(", ", array_keys($data));
            $values = ":" . implode(", :", array_keys($data));

            $stmt = Connect::getInstance()->prepare("INSERT INTO {$this->entity} ({$columns}) VALUES ({$values})");
            $stmt->execute($this->filter($data));

            return Connect::getInstance()->lastInsertId();
        } catch (PDOException $exception) {
            $this->fail = $exception;
            return null;
        }
    }

    /**
     * @param array $data
     * @param string $terms
     * @param string $params
     * @return int|null
     */
    protected function update(array $data, string $terms, string $params): ?int
    {
        try {
            $dateSet = [];
            foreach ($data as $bind => $value) {
                $dateSet[] = "{$bind} = :{$bind}";
            }
            $dateSet = implode(", ", $dateSet);
            parse_str($params, $params);

            $stmt = Connect::getInstance()->prepare("UPDATE {$this->entity} SET {$dateSet} WHERE {$terms}");
            $stmt->execute($this->filter(array_merge($data, $params)));
            return ($stmt->rowCount() ?? 1);
        } catch (PDOException $exception) {
            $this->fail = $exception;
            return null;
        }
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        if (!$this->required()) {
            $this->message->warning("Fill in all mandatory fields");
            return false;
        }

        /** Update */
        if (!empty($this->id)) {
            $id = $this->id;
            $this->update($this->safe(), "id = :id", "id={$id}");
            if ($this->fail()) {
                $this->message->error("There was a error updating");
                return false;
            }
        }

        /** Create */
        if (empty($this->id)) {
            $id = $this->create($this->safe());
            if ($this->fail()) {
                $this->message->error("There was a erro creating the new register");
                return false;
            }
        }

        $this->data = $this->findOneById($id)->data();
        return true;
    }

    /**
     * @return int
     */
    public function lastId(): int
    {
        return Connect::getInstance()->query("SELECT MAX(id) as maxId FROM {$this->entity}")->fetch()->maxId + 1;
    }

    /**
     * @param string $terms
     * @param null|string $params
     * @return bool
     */
    public function delete(string $terms, ?string $params): bool
    {
        try {
            $stmt = Connect::getInstance()->prepare("DELETE FROM {$this->entity} WHERE {$terms}");
            if ($params) {
                parse_str($params, $paramList);
                $stmt->execute($paramList);
                return true;
            }

            $stmt->execute();
            return true;
        } catch (PDOException $exception) {
            $this->fail = $exception;
            return false;
        }
    }

    /**
     * @return bool
     */
    public function destroy(): bool
    {
        if (empty($this->id)) {
            $this->fail = new Exception("The ID is required to delete the register");
            return false;
        }

        $destroy = $this->delete("id = :id", "id={$this->id}");
        return $destroy;
    }

    /**
     * Campo created_at formatado
     *
     * @param string $formatTo
     * @return string
     */
    public function createdAt(string $formatTo = "d/m/Y H:i:s"): string
    {
        return date_br($this->created_at, "Y-m-d H:i:s", $formatTo);
    }

    /**
     * Campo updated_at formatado
     *
     * @return string
     */
    public function updatedAt(): string
    {
        return date_br($this->updated_at);
    }

    /**
     * @return array|null
     */
    protected function safe(): ?array
    {
        $safe = (array)$this->data;
        foreach ($this->protected as $unset) {
            unset($safe[$unset]);
        }
        return $safe;
    }

    /**
     * @param array $data
     * @return array|null
     */
    private function filter(array $data): ?array
    {
        $filter = [];
        foreach ($data as $key => $value) {
            $filter[$key] = (is_null($value) ? null : filter_var($value, FILTER_DEFAULT));
        }
        return $filter;
    }

    /**
     * @return bool
     */
    protected function required(): bool
    {
        $data = (array)$this->data();
        foreach ($this->required as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }
        return true;
    }
}
