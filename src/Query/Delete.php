<?php

namespace Jelle_S\DataBase\Query;

/**
 * Description of Delete
 *
 * @author Jelle Sebreghts
 */
class Delete extends Query {

  use QueryTrait\Where;
  use QueryTrait\OrderBy;
  use QueryTrait\Limit;
  use QueryTrait\Condition;

  protected $table;

  public function __construct(\Jelle_S\DataBase\Connection $connection, $table) {
    parent::__construct($connection);
    $this->table = $table;
  }

  /**
   * Build the query. If this is not executed before the run() method, it will be called by that method.
   * @return db
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
