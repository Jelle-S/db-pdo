<?php

namespace Jelle_S\DataBase\Query;

/**
 * Represents an SQL update query.
 *
 * @author Jelle Sebreghts
 */
class Update extends Query{
  use QueryTrait\Where;
  use QueryTrait\OrderBy;
  use QueryTrait\Limit;
  use QueryTrait\FieldsWithValues;
  use QueryTrait\Condition;


  protected $table;

  /**
   * Creates a new \Jelle_S\DataBase\Query\Update object.
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

    $this->sql = "UPDATE " . $this->table . " SET";
    foreach ($this->fields as $f) {
      $this->sql .= " " . $f . '= :' . $f . ",";
    }
    $this->sql = substr($this->sql, 0, -1);
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
