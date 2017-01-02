<?php

namespace Jelle_S\DataBase\Query;

/**
 * Represents an SQL insert query.
 *
 * @author Jelle Sebreghts
 */
class Insert extends Query {
  use QueryTrait\FieldsWithValues;

  protected $table;

  /**
   * Creates a new \Jelle_S\DataBase\Query\Insert object.
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
    $this->sql = "INSERT INTO " . $this->table . " (" . implode($this->fields, ", ") . ") VALUES (:" . implode($this->fields, ", :") . ")";
    if (!empty($this->on_duplicate)) {
      $this->sql .= " ON DUPLICATE KEY UPDATE " . $this->on_duplicate;
    }
    $this->sql .= ';';
  }

  /**
   * If you specify ON DUPLICATE KEY UPDATE, and a row is inserted that would
   * cause a duplicate value in a UNIQUE index or PRIMARY KEY, an UPDATE of the
   * old row is performed.
   *
   * @param array $fields
   *   An array with the column names as key and the the values they should
   * update to as values.
   *
   * @return $this
   */
  public function onDuplicateKeyUpdate(array $fields) {
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
