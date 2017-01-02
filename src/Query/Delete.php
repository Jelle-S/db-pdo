<?php

namespace Jelle_S\DataBase\Query;

/**
 * Represents an SQL delete query.
 *
 * @author Jelle Sebreghts
 */
class Delete extends Query {

  use QueryTrait\Where;
  use QueryTrait\OrderBy;
  use QueryTrait\Limit;
  use QueryTrait\Condition;

  protected $table;

  /**
   * Creates a new \Jelle_S\DataBase\Query\Delete object.
   *
   * @param \Jelle_S\DataBase\Connection $connection
   *   The connection on which to execute this query.
   * @param string $table
   *   The table to execute the query on.
   */
  public function __construct(\Jelle_S\DataBase\Connection $connection, $table) {
    parent::__construct($connection);
    $this->table = $table;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $this->sql = "DELETE FROM " . $this->table;
    if (!empty($this->where)) {
      $this->sql .= " WHERE " . $this->where;
    }
    if (!empty($this->orderby)) {
      $this->sql .= " ORDER BY " . $this->orderby;
    }
    if (!empty($this->limit)) {
      $this->sql .= " LIMIT " . $this->limit;
    }
    $this->sql .= ';';
    return $this;
  }

}
