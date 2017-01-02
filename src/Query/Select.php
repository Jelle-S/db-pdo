<?php

namespace Jelle_S\DataBase\Query;

/**
 * Description of Select
 *
 * @author Jelle Sebreghts
 */
class Select extends Query {
  use QueryTrait\Join;
  use QueryTrait\Where;
  use QueryTrait\GroupBy;
  use QueryTrait\Having;
  use QueryTrait\OrderBy;
  use QueryTrait\Limit;
  use QueryTrait\Fields;
  use QueryTrait\Condition;

  protected $table;
  protected $alias;

  public function __construct(\Jelle_S\DataBase\Connection $connection, $table, $alias = '') {
    parent::__construct($connection);
    $this->table = $table;
    $this->alias = $alias;
  }

  /**
   * Build the query. If this is not executed before the run() method, it will be called by that method.
   * @return db
   */
  public function build() {

    if (is_array($this->fields)) {
      if (empty($this->fields)) {
        $this->fields = array("*");
      }
      $this->fields = implode($this->fields, ", ");
    }
    $this->sql = "SELECT " . $this->fields . " FROM " . $this->table;
    if (!empty($this->alias)) {
      $this->sql .= " " . $this->alias;
    }
    if (!empty($this->jointables)) {
      foreach ($this->jointables as $table) {
        $this->sql .= " " . $table['join type'] . " JOIN " . $table['table'] . " " . $table['alias'] . " ON (" . $table['condition'] . ")";
      }
    }
    if (!empty($this->where)) {
      $this->sql .= " WHERE " . $this->where;
    }
    if (!empty($this->groupby)) {
      $this->sql .= " GROUP BY " . $this->groupby;
    }
    if (!empty($this->having)) {
      $this->sql .= " HAVING " . $this->having;
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
