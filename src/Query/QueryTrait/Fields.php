<?php

namespace Jelle_S\DataBase\Query\QueryTrait;

/**
 * Add fields to a query.
 *
 * @author Jelle Sebreghts
 */
trait Fields {

  protected $fields = [];

  /**
   * Add fields to the query.
   * @param array $fields
   *   An array with the column names you want to add to the query.
   *
   * @return $this
   */
  public function fields(array $fields) {

    if (!empty($fields)) {
      $this->fields += $fields;
    }
    return $this;
  }

}
