<?php

namespace Jelle_S\DataBase\Query\QueryTrait;

/**
 * Add fields with corresponding values to the query.
 *
 * @author Jelle Sebreghts
 */
trait FieldsWithValues {

  protected $fields = [];

  /**
   * Add fields to the query.
   *
   * @param array $fields
   *   An array with the column names as keys and the corresponding values as
   *   values.
   *
   * @return $this
   */
  public function fields(array $fields) {
    if (!empty($fields)) {
      $this->fields += array_keys($fields);
      $bind = array();
      foreach ($this->fields as $field) {
        $key = str_replace('.', '', $field);
        $i = 0;
        while (isset($this->bind[":$key"]) || isset($bind[":$key"])) {
          $key = str_replace('.', '', $field) . $i;
        }
        $bind[":$key"] = $fields[$field];
      }
      $this->bind += $this->cleanup($bind);
    }
    return $this;
  }

}
