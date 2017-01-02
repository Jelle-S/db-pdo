<?php

namespace Jelle_S\DataBase\Query\QueryTrait;

/**
 *
 * @author Jelle Sebreghts
 */
trait GroupBy {
  protected $groupby = '';
  /**
   * Add a group by clause to the query.
   * @param mixed $fields A string containing the comma-separated columns (or a single column name) or an array containing the columns (or a single column).
   * @return db
   */
  public function groupby($fields) {
    if (!is_array($fields)) {
      $fields = array($fields);
    }
    foreach ($fields as $f) {
      if (!empty($this->groupby)) {
        $this->groupby .= ", " . $f;
      }
      else {
        $this->groupby = $f;
      }
    }
    return $this;
  }

}
