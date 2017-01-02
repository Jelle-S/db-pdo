<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Jelle_S\DataBase\Query;

/**
 * Description of Update
 *
 * @author drupalpro
 */
class Update extends Query{
  use QueryTrait\Where;
  use QueryTrait\OrderBy;
  use QueryTrait\Limit;
  use QueryTrait\FieldsWithValues;
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
