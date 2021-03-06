<?php

namespace Jelle_S\DataBase\Query\QueryTrait;

/**
 * Add order by clauses to queries.
 *
 * @author Jelle Sebreghts
 */
trait OrderBy {

  protected $orderby = '';

  /**
   * Add an order by clause to the query.
   *
   * @param array|string $fields
   *   A string containing the comma-separated columns (or a single column name)
   *   or an array containing the columns (or a single column).
   * @param string $order
   *  (optional) "ASC" or "DESC".
   *
   * @return $this
   */
  public function orderby($fields, $order = NULL) {
    if (empty($order)) {
      $order = "ASC";
    }
    if (!is_array($fields)) {
      $fields = array($fields);
    }
    foreach ($fields as $f) {
      $f = $f . " " . $order;
      if (!empty($this->orderby)) {
        $this->orderby .= ", " . $f;
      }
      else {
        $this->orderby = $f;
      }
    }
    return $this;
  }

}
