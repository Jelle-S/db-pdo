<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Jelle_S\DataBase\Query;

/**
 * Description of Insert
 *
 * @author drupalpro
 */
class Insert extends Query {
  use QueryTrait\FieldsWithValues;

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
    $this->sql = "INSERT INTO " . $this->table . " (" . implode($this->fields, ", ") . ") VALUES (:" . implode($this->fields, ", :") . ")";
    if (!empty($this->on_duplicate)) {
      $this->sql .= " ON DUPLICATE KEY UPDATE " . $this->on_duplicate;
    }
    $this->sql .= ';';
  }

  /**
   * If you specify ON DUPLICATE KEY UPDATE, and a row is inserted that would cause a duplicate value in a UNIQUE index or PRIMARY KEY, an UPDATE of the old row is performed.
   * @param array $fields An array with the column names as key and the the values they should update to as values.
   * @return db
   */
  public function on_duplicate_key_update(array $fields) {
    $f = array_keys($fields);
    $bind = array();
    foreach ($f as $field) {
      $key = str_replace('.', '', $field);
      $i = 0;
      while (isset($this->bind[":$key"])) {
        $i++;
        $key = str_replace('.', '', $field) . $i;
      }
      $bind[":$key"] = $fields[$field];
      $this->on_duplicate .= $field . ' = :' . $key . ', ';
    }
    $this->on_duplicate = substr($this->on_duplicate, 0, -2);
    $this->bind += $this->cleanup($bind);
    return $this;
  }

}
