<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Jelle_S\DataBase\Query\QueryTrait;

/**
 *
 * @author Jelle Sebreghts
 */
trait FieldsWithValues {

  protected $fields = [];

  /**
   * Add fields to the query
   * @param array $fields <p>An array with the column names as keys and the values you want to insert as values for update and insert statements.</p><p>An array with the column names you want to select as values for select statements.</p>
   * @return db
   */
  public function fields(array $fields) {
    if (!empty($fields)) {
      $this->fields += array_keys($fields);
      //$this->sql .=  " (" . implode($fields, ", ") . ") VALUES (:" . implode($fields, ", :") . ")";
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
